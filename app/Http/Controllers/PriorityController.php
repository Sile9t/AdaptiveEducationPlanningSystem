<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmployeeCategory;
use App\Models\Permit;
use App\Models\PriorityDTO;
use App\Models\PriorityStatus;
use App\Models\TrainingProgram;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PriorityController extends Controller
{
    const uploadFolderName = 'uploads';
    const inputFileType = "Xlsx";

    public function index()
    {
        $userId = Auth::user()->id;
        $userIdHash = hash('sha256', $userId);
        $fileName = $userIdHash . '_' . '5366.xlsx';

        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $filePath = $folderPath . '/' . $fileName;
        
        $file = Storage::path($filePath);
        
        if (! $file) {
            return  response()->json([
                'message' => 'No available data',
            ]);
        }

        $redisKey = hash('sha256', "priority$userId");
        
        self::processFile($file, $userId);

        $data = Redis::get($redisKey);
        return response()->json(
            // json_encode($data)
            $data
        );
        // $query = Priority::orderBy('id', 'asc');
        // $query = $query->groupBy('status');
        // return $query->paginate()->toResourceCollection();
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
        $diff = now()->date_diff($expired_date);
        $diffYears = floor($diff / (365*60*60*24));

        if ($diffYears >= 2) return PriorityStatus::Active;
        if (1 <= $diffYears && $diffYears < 2) return PriorityStatus::Control;
        if (0 < $diffYears && $diffYears < 1) return PriorityStatus::Expiring;
            
        return PriorityStatus::Passed;                
    }

    function processFile(string $filename, int $userId)
    {
        // Needed columns: B, D, E, T, U, V, X, Y
        $requiredColumns = [ 'B', 'D', 'E', 'T', 'U', 'V', 'X', 'Y' ];
        
        $redisKey = hash('sha256', "priority$userId");

        $reader = IOFactory::createReader(self::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filename);

        $sheets = $spreadsheet->getAllSheets();
        $sheetNames = $spreadsheet->getSheetNames();
        
        $priorities = array();

        for ($sheetIndex=0; $sheetIndex < count($sheets); $sheetIndex++) { 
            $worksheet = $sheets[$sheetIndex];
            $sheetNameAsCategory = $sheetNames[$sheetIndex];
            
            $categories = EmployeeCategory::all('id', 'name');
            $currentCategory = $categories->first(fn ($category, $key) => strcasecmp($category['name'], $sheetNameAsCategory) == 0);
            if (! isset($currentCategory) || $currentCategory == '') continue;

            $rowCount = $worksheet->getHighestRow();
            
            $branches = Branch::all('id', 'name')->toArray();
            $permits = Permit::all();
            $programs = TrainingProgram::all('id', 'title');
            
            for ($rowIndex=4; $rowIndex < $rowCount; $rowIndex++) { 
                $finalProgram = $worksheet->getCell($requiredColumns[4] . $rowIndex)->getValue();
                
                if (! isset($finalProgram) || $finalProgram === '') {
                    $finalProgram = $worksheet->getCell($requiredColumns[5] . $rowIndex)->getValue();
                    
                    if (! isset($finalProgram) || $finalProgram === '') {
                       $finalProgram = $worksheet->getCell($requiredColumns[3] . $rowIndex)->getValue();
                    }
                }
                
                if (! isset($finalProgram) || $finalProgram === '') continue;
                
                $program = $programs->first(fn ($p, $k) => stristr($finalProgram, $p));
                if (! isset($program) || $program == '') continue;
                
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
                //TODO: add periodicity dependency
                $status = self::getFittingPriorityStatus($expired_at);
                
                $id = PriorityDTO::count();
                $full_name = "employee$id";
                $priority = PriorityDTO::create(
                    $full_name,
                    $currentCategory,
                    $position,
                    $branch,
                    $finalProgram,
                    $passed_at,
                    $expired_at,
                    $status
                );
                
                $priorityState = $priority->toArray();
                Redis::set(
                    $redisKey,
                    $priority->toJson()
                );
                array_push($priorities, $priorityState);
            }
        }
        
        Redis::set(
            $redisKey,
            json_encode($priorities)
        );
    }
}
