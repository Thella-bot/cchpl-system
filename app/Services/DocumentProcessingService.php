<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DocumentProcessingService
{
    /**
     * Process and optimize uploaded document images
     */
    public static function processDocument(UploadedFile $file, string $path): string
    {
        $filename = $file->hashName();
        $fullPath = rtrim($path, '/') . '/' . $filename;

        // Only optimize image files
        if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
            $image = Image::make($file)
                ->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->orientate();

            // Optimize quality
            $image->save(Storage::path($fullPath), 85);
        } else {
            // Store non-images normally
            $file->storeAs($path, $filename);
        }

        return $fullPath;
    }

    /**
     * Generate thumbnail for document preview
     */
    public static function generateThumbnail(string $sourcePath, string $thumbnailPath): ?string
    {
        if (!Storage::exists($sourcePath)) {
            return null;
        }

        try {
            Image::make(Storage::get($sourcePath))
                ->resize(400, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(Storage::path($thumbnailPath), 75);

            return $thumbnailPath;
        } catch (\Exception $e) {
            return null;
        }
    }
}