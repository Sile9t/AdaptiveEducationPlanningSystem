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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriorityService
{
    const inputFileType = "Xlsx";
    const requiredColumns = [ 'B', 'D', 'E', 'L', 'T', 'U', 'V', 'X'];
    
    const errorColumnIndex = 'Z';
    const passibleErrors = [
        'program' => 'Не указана программа ("Доп. информация 1", "Доп. информация 2", "Учебная программа")',
        'branch' => 'Не указан "Филиал"',
        'position' => 'Не указана "Должность"',
        'passed_at' => 'Не указана дата ("Дата окончания")',
        'full_name' => 'Не указано "ФИО"',
    ];
    
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
        
        $reader = IOFactory::createReader(self::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $writer = IOFactory::createWriter($spreadsheet, self::inputFileType);

        $sheets = $spreadsheet->getAllSheets();
        $sheetNames = $spreadsheet->getSheetNames();
        
        $priorities = array();
        
        for ($sheetIndex=0; $sheetIndex < count($sheets); $sheetIndex++) { 
            $worksheet = $sheets[$sheetIndex];
            $sheetNameAsCategory = $sheetNames[$sheetIndex];
            $sheetNameAsCategory = self::mapCategory[$sheetNameAsCategory] ?? $sheetNameAsCategory;
            
            $currentCategory = $this->categories->first(fn ($category, $key) => strcasecmp($category['name'], $sheetNameAsCategory) == 0);
            if (! isset($currentCategory) || $currentCategory == '') continue;
            
            $rowCount = $worksheet->getHighestRow();
            
            for ($rowIndex=4; $rowIndex < $rowCount; $rowIndex++) { 
                $errors = array();
                $programTitle = self::getProgramTitleFromExcelRow($worksheet, $rowIndex);
                
                if ((! isset($programTitle) || $programTitle === '')) {
                    array_push($errors, self::passibleErrors['program']);
                }
                else {
                    $hits = $index->search( '\''. $programTitle . '\'', [
                        'distinct' => 'title',
                        'matchingStrategy' => 'frequency',
                        'showRankingScore' => true,
                    ])->getHits();
    
                    if (count($hits) == 0) {
                        array_push($errors, "Программа \"$programTitle\" не найдена");
                    }
                    else {
                        $program = $this->programs->where('id', '==', $hits[0]['id'])->first();
                    }
                }
                
                
                $branch = $worksheet->getCell(self::requiredColumns[0] . $rowIndex)->getValue();
                if ((! isset($branch) || $branch === '')) array_push($errors, self::passibleErrors['branch']);
                $position = $worksheet->getCell(self::requiredColumns[2] . $rowIndex)->getValue();
                if ((! isset($position) || $position === '')) array_push($errors, self::passibleErrors['position']);

                $passed_at = Carbon::createFromDate(self::getDate($worksheet->getCell(self::requiredColumns[3] . $rowIndex)->getValue()))->addYears(array_rand(range(0, 10)));
                if (! isset($passed_at) || $passed_at === '') {
                    array_push($errors, self::passibleErrors['passed_at']);
                }
                else if (isset($program) && $program !== '') {
                    $periodicity = $this->permits
                        ->first(
                            fn ($permit) => 
                                $permit['program_id'] == $program->id 
                                && $permit['category_id'] == $currentCategory->id,
                        )['periodicity_years'];
                    $expired_at = $passed_at->addYears($periodicity);
                    
                    $status = self::getPriorityStatusForExpiredDate($expired_at);
                }

                $full_name = $worksheet->getCell(self::requiredColumns[1] . $rowIndex)->getValue();
                // if (! isset($full_name) || $full_name === '') array_push($errors, self::passibleErrors['full_name']);

                if (count($errors) == 0) {
                    $id = PriorityDTO::count();
                    $full_name = $full_name ?? "Employee$id";
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
                else {
                    $errorsText = implode(", ", $errors);
                    $worksheet->getCell(self::errorColumnIndex . $rowIndex)->setValue($errorsText);
                }
            }
        }
        $writer->save($filePath);
        
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

    private function isEmpty(string $value) {
        return (! isset($value) || $value === '');
    }
}