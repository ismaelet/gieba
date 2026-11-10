<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHelper
{
    /**
     * Get the versioned path of a file in the public storage directory
     */
    public static function storageAsset(string $file): string
    {
        if (! self::exists($file)) {
            throw new Exception('File not found: '.$file);
        }

        $timestamp = self::lastModified($file);

        return asset('storage/'.$file).'?v='.$timestamp;
    }

    /**
     * Check if a file exists in the storage
     */
    public static function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Get the last modified timestamp of a file in the storage
     */
    public static function lastModified(string $path): int
    {
        return Storage::disk('public')->lastModified($path);
    }

    /**
     * Delete a file from the storage
     */
    public static function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Download a file from the public storage directory
     */
    public static function download(string $path, ?string $name = null): BinaryFileResponse
    {
        if (! self::exists($path)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($path);

        return response()->download($fullPath, $name);
    }

    /**
     * Move a file to the public storage directory
     */
    public static function upload(UploadedFile $file, string $filePath): bool
    {
        $fileDirectory = ArrayHelper::explodeLast('/', $filePath)[0];
        $fileName = ArrayHelper::explodeLast('/', $filePath)[1];

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $storedFile = $file->storeAs($fileDirectory, $fileName, 'public');

        return (bool) $storedFile;
    }
}
