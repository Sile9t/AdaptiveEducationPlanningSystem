<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityUploadRequest;
use App\Models\Branch;
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
        $rowIterator = $worksheet->getRowIterator(4);
        
        $branches = Branch::all('name');
        dump($branches);
        
        foreach ($rowIterator as $row) {
            $columnIterator = $row->getCellIterator($requiredColumns[0]);
            
            dump($row);
            if ($row->getRowIndex() > 19) break;
            foreach ($columnIterator as $cell) {
                $currentColumn = $cell->getColumn();
                
                if (in_array($currentColumn, $requiredColumns)) {
                    $cellValue = $cell->getValue();
                    dump("$currentColumn : $cell");
                    switch ($currentColumn) {
                        case $requiredColumns[0]:
                            if (in_array($cell, ))
                            break;
                        case $requiredColumns[1]:
                            break;
                        case $requiredColumns[2]:
                        case $requiredColumns[3]:
                        case $requiredColumns[4]:
                            break;
                        case $requiredColumns[5]:
                            break;
                        case $requiredColumns[0]:
                            break;
                    }
                }
            }
        }
    }
}
