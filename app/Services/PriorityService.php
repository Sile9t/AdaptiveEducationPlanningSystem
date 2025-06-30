<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\EmployeeCategory;
use App\Models\Permit;
use App\Models\PriorityDTO;
use App\Models\PriorityStatus;
use App\Models\TrainingProgram;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriorityService
{
    const inputFileType = "Xlsx";
    const requiredColumns = [ 'B', 'D', 'E', 'L', 'T', 'U', 'V', 'X'];
    const mapCategory = [
        'Руководители' => 'Руководитель',
        'Рабочие' => 'Рабочий',
        'Специалисты' => 'Специалист'
    ];
    private int $userId;
    private Collection $categories;
    private Collection $permits;
    private Collection $programs;
    private MeiliSearchService $meili;

    public function __construct(int $userId, MeiliSearchService $meiliSearchService)
    {
        $this->userId = $userId;
        $this->meili = $meiliSearchService;
        $this->categories = EmployeeCategory::all('id', 'name');
        $this->permits = Permit::all();
        $this->programs = TrainingProgram::all('id', 'title');    
    }

    public function getDataFromAndWriteErrorsIntoFile(string $filePath)
    {
        $index = $this->meili->getIndex('training_programs');
        
        $reader = IOFactory::createReader($this::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        $sheets = $spreadsheet->getAllSheets();
        $sheetNames = $spreadsheet->getSheetNames();
        
        $priorities = array();

        for ($sheetIndex=0; $sheetIndex < count($sheets); $sheetIndex++) { 
            $worksheet = $sheets[$sheetIndex];
            $sheetNameAsCategory = $sheetNames[$sheetIndex];
            $sheetNameAsCategory = $mapCategory[$sheetNameAsCategory] ?? $sheetNameAsCategory;
            
            $currentCategory = $this->categories->first(fn ($category, $key) => strcasecmp($category['name'], $sheetNameAsCategory) == 0);
            if (! isset($currentCategory) || $currentCategory == '') continue;

            $rowCount = $worksheet->getHighestRow();
            
            for ($rowIndex=4; $rowIndex < $rowCount; $rowIndex++) { 
                $programTitle = self::getProgramTitleFromExcelRow($worksheet, $rowIndex);
                
                if (! isset($programTitle) || $programTitle === '') continue;
                
                $hits = $index->search( '\''. $programTitle . '\'', [
                    'distinct' => 'title',
                    'matchingStrategy' => 'frequency',
                    'showRankingScore' => true,
                ])->getHits();

                if (count($hits) == 0) continue;

                $program = $this->programs->where('id', '==', $hits[0]['id'])->first();
                
                $branch = $worksheet->getCell(self::requiredColumns[0] . $rowIndex)->getValue();
                $position = $worksheet->getCell(self::requiredColumns[2] . $rowIndex)->getValue();

                $passed_at = Carbon::createFromDate(self::getDate($worksheet->getCell(self::requiredColumns[3] . $rowIndex)->getValue()))->addYears(array_rand(range(0, 10)));
                $periodicity = $this->permits
                    ->first(
                        fn ($permit) => 
                            $permit['program_id'] == $program->id 
                            && $permit['category_id'] == $currentCategory->id,
                    )['periodicity_years'];
                $expired_at = $passed_at->addYears($periodicity);
                
                $status = self::getPriorityStatusForExpiredDate($expired_at);
                
                $id = PriorityDTO::count();
                $full_name = "employee$id";
                $priority = PriorityDTO::create(
                    $full_name,
                    $currentCategory->name,
                    $position,
                    $branch,
                    $programTitle,
                    $passed_at->toDate(),
                    $expired_at->toDate(),
                    $status
                );
                
                $priorityState = $priority->toArray();
                array_push($priorities, $priorityState);
            }
        }
        
        return $priorities;
    }

    private function getPriorityStatusForExpiredDate(DateTime $expired_date): PriorityStatus
    {
        $diff = date_diff($expired_date, now())->days;
        $diffYears = $diff / (365);

        if ($diffYears >= 2) PriorityStatus::Active;
        if (1 <= $diffYears && $diffYears < 2) PriorityStatus::Control;
        if (0 < $diffYears && $diffYears < 1) PriorityStatus::Expiring;

        return PriorityStatus::Passed;
    }

    private function getDate($dateFromExcel) {
        return Date::excelToDateTimeObject($dateFromExcel);
    }

    private function getProgramTitleFromExcelRow(Worksheet $worksheet, int $rowIndex)
    {
        $programTitle = $worksheet->getCell(self::requiredColumns[5] . $rowIndex)->getValue();

        if (! isset($programTitle) || $programTitle === '') {
            $programTitle = $worksheet->getCell(self::requiredColumns[6] . $rowIndex)->getValue();
            
            if (! isset($programTitle) || $programTitle === '') {
                $programTitle = $worksheet->getCell(self::requiredColumns[4] . $rowIndex)->getValue();
            }
        }

        return $programTitle;
    }
}