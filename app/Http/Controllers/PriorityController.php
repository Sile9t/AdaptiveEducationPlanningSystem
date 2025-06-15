<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityUploadRequest;
use App\Models\Branch;
use App\Models\EmployeeCategory;
use App\Models\Permit;
use App\Models\Priority;
use App\Models\PriorityStatus;
use App\Models\TrainingProgram;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class PriorityController extends Controller
{
    const uploadFolderName = 'uploads';
    const inputFileType = "Xlsx";

    public function index()
    {
        $userIdHash = hash('sha256', Auth::user()->id);
        $fileName = $userIdHash . '_' . '5366.xlsx';

        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $filePath = $folderPath . '/' . $fileName;
        
        $file = Storage::path($filePath);
        
        if (! $file) {
            return 'No available data';
        }
        
        self::processFile($file);

        return Priority::all();
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file5366' => 'required|mimes:xlsx|max:50000'
        ]);

        $file = $request->file('file5366');

        if (! str_contains($file->getClientOriginalName(), '5366')) {
            return redirect()->back()->with('message', "File need to contain '5366' in the name");
        }

        $userIdHash = hash('sha256', Auth::user()->id);
        $fileName = $userIdHash . '_' . '5366.xlsx';
        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $file->storeAs($folderPath, $fileName);

        return redirect()->route('priority.index')->with('message', 'File uploaded successfully.');
    }

    function getFittingPriorityStatus(DateTime $expired_date)
    {
        $diff = $expired_date - now();
        $diffYears = floor($diff / (365*60*60*24));

        if ($diffYears >= 2) return PriorityStatus::Active;
        if (1 <= $diffYears && $diffYears < 2) return PriorityStatus::Control;
        if (0 < $diffYears && $diffYears < 1) return PriorityStatus::Expiring;
            
        return PriorityStatus::Passed;                
    }

    function processFile(string $filename)
    {
        // Needed columns: B, D, E, T, U, V, X, Y
        $requiredColumns = [ 'B', 'D', 'E', 'T', 'U', 'V', 'X', 'Y' ];
        
        $reader = IOFactory::createReader(self::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filename);

        $sheets = $spreadsheet->getAllSheets();
        $sheetNames = $spreadsheet->getSheetNames();
        
        $worksheet = $sheets[1];
        $sheetNameAsCategory = $sheetNames[1];

        $rowCount = $worksheet->getHighestRow();
        
        $categories = EmployeeCategory::all('id', 'name');
        $branches = Branch::all('id', 'name')->toArray();
        $permits = Permit::all();
        $programs = TrainingProgram::all('id', 'title');

        $currentCategory = $categories->first(fn ($category, $key) => strcasecmp($category['name'], $sheetNameAsCategory) == 0);

        for ($rowIndex=4; $rowIndex < 100; $rowIndex++) { 
            $finalProgram = $worksheet->getCell($requiredColumns[4] . $rowIndex)->getValue();

            if (! isset($finalProgram) || $finalProgram === '') {
                $finalProgram = $worksheet->getCell($requiredColumns[5] . $rowIndex)->getValue();

                if (! isset($finalProgram) || $finalProgram === '') {
                   $finalProgram = $worksheet->getCell($requiredColumns[3] . $rowIndex)->getValue();
                }
            }

            // Skip if all training program was empty 
            if (! isset($finalProgram) || $finalProgram === '') continue;

            $program = $programs->first(fn ($p, $k) => stristr($finalProgram, $p));
            if (! isset($program)) continue;
            $branch = $worksheet->getCell($requiredColumns[0] . $rowIndex);
            $position = $worksheet->getCell($requiredColumns[2] . $rowIndex);
            $passed_at = now()->addDays(array_rand(range(-50, 50)))->addYears(range(-2, 1))->toDateTime();
            $periodicity = $permits
                ->first(
                    fn ($permit) => 
                        $permit['program_id'] == $program['id'] 
                        && $permit['category_id'] == $currentCategory['id'],
                )['periodicity_years'];
            $expired_at = Carbon::parse($passed_at)->addYears($periodicity);
            $status = self::getFittingPriorityStatus($expired_at);

            $priority = Priority::factory()->create([
                'category' => $currentCategory,
                'position' => $position,
                'branch' => $branch,
                'permit' => $finalProgram,
                'passed_at' => $passed_at,
                'expired_at' => $expired_at,
                'status' => $status,
            ]);

            // $programCells = [];
            // $programCells[] = $worksheet->getCell($requiredColumns[3] . $rowIndex)->getValue();
            // $programCells[] = $worksheet->getCell($requiredColumns[4] . $rowIndex)->getValue();
            // $programCells[] = $worksheet->getCell($requiredColumns[5] . $rowIndex)->getValue();
            
            // foreach ($programs as $program) {
            //     if (
            //         stristr($programCells[0], $program['title'])
            //         || stristr($programCells[1], $program['title'])
            //         || stristr($programCells[2], $program['title'])
            //     ) {
            //         dump("Founded program: " . $program['title']);
            //         break;

            //         $branch = $worksheet->getCell($requiredColumns[0] . $rowIndex);
            //         $position = $worksheet->getCell($requiredColumns[2] . $rowIndex);
            //         $passed_at = now()->addDays(Arr::random(range(-50, 50)))->addYears(range(-2, 1))->toDateTime();
            //         $periodicity = $permits
            //             ->first(
            //                 fn ($permit) => 
            //                     $permit['program_id'] == $program['id'] 
            //                     && $permit['category_id'] == $currentCategory['id'],
            //             )['periodicity_years'];
            //         $expired_at = $passed_at->addYears($periodicity);
            //         $status = getFittingPriorityStatus($expired_at);

            //         Priority::factory()->create([
            //             'category' => $category,
            //             'position' => $position,
            //             'branch' => $branch,
            //             'permit' => $program['title'],
            //             'passed_at' => $passed_at,
            //             'expired_at' => $expired_at,
            //             'status' => $status,
            //         ]);
            //     }
            // }
        }

    }
}
