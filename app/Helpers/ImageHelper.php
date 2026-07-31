<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function uploadAndCompress($file, $directory, $disk = 'public', $maxSize = 1048576, $maxWidth = 1920, $maxHeight = 1080): string
    {
        $path = $file->store($directory, $disk);
        $fullPath = Storage::disk($disk)->path($path);
        self::compressFile($fullPath, $maxSize, $maxWidth, $maxHeight);
        return $path;
    }

    public static function compressFile(string $filePath, int $maxSize = 1048576, int $maxWidth = 1920, int $maxHeight = 1080): void
    {
        if (!file_exists($filePath)) return;

        $info = @getimagesize($filePath);
        if (!$info) return;

        [$width, $height, $type] = $info;
        $mime = $info['mime'] ?? '';

        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($filePath),
            'image/png'  => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            'image/gif'  => @imagecreatefromgif($filePath),
            default      => null,
        };

        if (!$src) return;

        if ($ratio < 1) {
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            if (in_array($mime, ['image/png', 'image/gif'], true)) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        match ($mime) {
            'image/jpeg' => self::saveJpeg($src, $filePath, $maxSize),
            'image/png'  => self::savePng($src, $filePath),
            'image/webp' => self::saveWebp($src, $filePath, $maxSize),
            'image/gif'  => imagegif($src, $filePath),
            default      => null,
        };

        imagedestroy($src);
    }

    private static function saveJpeg($src, string $path, int $maxSize): void
    {
        $quality = 85;
        do {
            ob_start();
            imagejpeg($src, null, $quality);
            $data = ob_get_clean();
            $quality -= 5;
        } while (strlen($data) > $maxSize && $quality >= 30);
        file_put_contents($path, $data);
    }

    private static function saveWebp($src, string $path, int $maxSize): void
    {
        $quality = 85;
        do {
            ob_start();
            imagewebp($src, null, $quality);
            $data = ob_get_clean();
            $quality -= 5;
        } while (strlen($data) > $maxSize && $quality >= 30);
        file_put_contents($path, $data);
    }

    private static function savePng($src, string $path): void
    {
        imagepng($src, $path, 9);
    }
}
