<?php

namespace App\Services;

use App\Models\FileType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    const uploadFolderName = 'uploads';
    private int $userId;

    private function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public static function create(int $userId)
    {
        return new FileService($userId);
    }

    public function getFilePathByType(FileType $fileType)
    {
        $userIdHash = hash('sha256', $this->userId);
        $folderPath = $this::uploadFolderName . '\\'. $userIdHash;
        $filePath = $folderPath . '\\' . $fileType->value;
        return Storage::path($filePath);
    }

    public function checkFileExistsByType(FileType $fileType)
    {
        $file = $this->getFilePathByType($fileType);
        
        return Storage::exists($file);
    }

    public function storeFileByType(FileType $fileType, UploadedFile $file)
    {
        $userIdHash = hash('sha256', $this->userId);
        $fileName = $userIdHash . '_' . $fileType->value;
        $folderPath = $this::uploadFolderName . '/'. $userIdHash;

        $file->storeAs($folderPath, $fileName);
    }
}