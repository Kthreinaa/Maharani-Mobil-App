<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewPhotoWatermarker
{
    public static function storeUploaded(UploadedFile $file): string
    {
        $relativePath = 'reviews/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $binary = self::buildWatermarkedBinary($file->getPathname(), $file->getMimeType() ?: 'image/jpeg');

        Storage::disk('public')->put($relativePath, $binary);

        return $relativePath;
    }

    public static function rewriteStoredPhoto(string $relativePath): void
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($relativePath)) {
            return;
        }

        $absolutePath = $disk->path($relativePath);
        $mime = mime_content_type($absolutePath) ?: 'image/jpeg';

        $disk->put($relativePath, self::buildWatermarkedBinary($absolutePath, $mime));
    }

    private static function buildWatermarkedBinary(string $sourcePath, string $mime): string
    {
        $image = @imagecreatefromstring((string) file_get_contents($sourcePath));

        if (!$image) {
            return (string) file_get_contents($sourcePath);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);

        $watermarkText = 'Maharani Mobil';
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($watermarkText);
        $textHeight = imagefontheight($font);

        $xStep = max($textWidth + 55, 180);
        $yStep = max($textHeight + 50, 95);

        $softWhite = imagecolorallocatealpha($image, 255, 255, 255, 78);
        $deepBlue = imagecolorallocatealpha($image, 8, 19, 46, 92);

        for ($y = 18; $y < $height + $yStep; $y += $yStep) {
            $offset = ((int) (($y / $yStep)) % 2) * (int) floor($xStep / 2);

            for ($x = -40 + $offset; $x < $width + $xStep; $x += $xStep) {
                imagestring($image, $font, $x, $y, $watermarkText, $softWhite);
                imagestring($image, $font, $x + 1, $y + 1, $watermarkText, $deepBlue);
            }
        }

        ob_start();

        if (str_contains($mime, 'png')) {
            imagepng($image, null, 6);
        } elseif (str_contains($mime, 'webp')) {
            imagewebp($image, null, 82);
        } else {
            imagejpeg($image, null, 88);
        }

        $binary = (string) ob_get_clean();
        imagedestroy($image);

        return $binary;
    }
}
