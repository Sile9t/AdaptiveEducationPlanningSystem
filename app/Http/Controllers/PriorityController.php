<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityUploadRequest;
use App\Models\Branch;
use App\Models\TrainingProgram;
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

    public function index(): View
    {
        $userIdHash = hash('sha256', Auth::user()->id);
        $fileName = $userIdHash . '_' . '5366.xlsx';

        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $filePath = $folderPath . '/' . $fileName;
        
        $file = Storage::path($filePath);
        
        if (! $file) {
            return view('priority.empty');
        }
        
        self::processFile($file);

        return view('priority.index');
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

    function processFile(string $filename)
    {
        // Needed columns: B, D, E, T, U, V, X, Y
        $requiredColumns = [ 'B', 'D', 'E', 'T', 'U', 'V', 'X', 'Y' ];
        
        $reader = IOFactory::createReader(self::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filename);
        $sheets = $spreadsheet->getSheetCount();
        
        $worksheet = $spreadsheet->getSheet(1);
        $rowCount = $worksheet->getHighestRow();

        $rowIterator = $worksheet->getRowIterator(4);
        
        $branches = Branch::all('name')->toArray();
        dump($branches);
        
        $programs = TrainingProgram::all('title')->toArray();

        for ($rowIndex=4; $rowIndex < 100; $rowIndex++) { 
            $program1 = $worksheet->getCell($requiredColumns[3] . $rowIndex)->getValue();
            $program2 = $worksheet->getCell($requiredColumns[4] . $rowIndex)->getValue();
            $program3 = $worksheet->getCell($requiredColumns[5] . $rowIndex)->getValue();
            // dump($program1 . ' | ' . $program2 . ' | ' . $program3);
            
            foreach ($programs as $program) {
                if (
                    stristr($program1, $program['title'])
                    || stristr($program2, $program['title'])
                    || stristr($program3, $program['title'])
                ) {
                    dump("Founded program: " . $program['title']);
                    break;
                }
            }
        }

        // foreach ($rowIterator as $row) {
        //     $columnIterator = $row->getCellIterator($requiredColumns[0]);
            
        //     dump($row);
        //     if ($row->getRowIndex() > 19) break;
        //     foreach ($columnIterator as $cell) {
        //         $currentColumn = $cell->getColumn();
                
        //         if (in_array($currentColumn, $requiredColumns)) {
        //             $cellValue = $cell->getValue();
        //             dump("$currentColumn : $cell");
                    
        //             switch ($currentColumn) {
        //                 case $requiredColumns[0]:
        //                     if (! empty(Branch::where('name', '==', $cell)->get()))
        //                         $columnIterator->rewind();
        //                     if (in_array($cell->getValue(), $branches))
        //                         $row->next();
        //                     break;

        //                 case $requiredColumns[1]:
        //                     break;
                            
        //                 case $requiredColumns[2]:
        //                 case $requiredColumns[3]:
        //                 case $requiredColumns[4]:
        //                     break;

        //                 case $requiredColumns[5]:
        //                     break;
        //                 case $requiredColumns[0]:
        //                     break;
        //             }
        //         }
        //     }
        // }
    }
}
