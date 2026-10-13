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

        // Ensure destination directory exists on disk (handles /tmp in serverless)
        try {
            $root = Storage::disk('public')->path('');
            $targetDir = rtrim($root, '/') . '/' . trim($directory, '/');
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
        } catch (\Throwable $e) {
            // Ignore path resolution issues
        }

        // Store to local disk (public)
        Storage::disk('public')->put($path, (string) $encodedImage);

        return $path;
    }

    /**
     * Upload and compress an image, returning both storage path and base64 string
     * for persistence across ephemeral serverless containers.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @return array{path: string, base64: string}
     */
    public function uploadImageWithBase64(UploadedFile $file, string $directory = 'selfies', int $quality = 65): array
    {
        $filename = uniqid() . '_' . time() . '.webp';
        $path = $directory . '/' . $filename;

        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file->getRealPath());
        $image->scaleDown(width: 600, height: 600);
        $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
        $binary = (string) $encodedImage;

        try {
            $root = Storage::disk('public')->path('');
            $targetDir = rtrim($root, '/') . '/' . trim($directory, '/');
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }
        } catch (\Throwable $e) {}

        Storage::disk('public')->put($path, $binary);

        return [
            'path' => $path,
            'base64' => base64_encode($binary),
        ];
    }
}
