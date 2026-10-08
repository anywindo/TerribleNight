<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class FileUploadService
{
    /**
     * Upload and compress an image (like a selfie).
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @return string The stored file path
     */
    public function uploadImage(UploadedFile $file, string $directory = 'selfies', int $quality = 70): string
    {
        // Convert to WebP format
        $filename = uniqid() . '_' . time() . '.webp';
        $path = $directory . '/' . $filename;

        // Process image with Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file->getRealPath());
        
        // Resize to a max dimension to save space while keeping aspect ratio
        $image->scaleDown(width: 800, height: 800);

        // Compress and encode to webp
        $encodedImage = $image->encode(new WebpEncoder(quality: $quality));

        // Store to local disk (public)
        Storage::disk('public')->put($path, (string) $encodedImage);

        return $path;
    }
}
