<?php

namespace Cyron\Storage;

use Exception;

class ImageHandler
{
    protected StorageManager $storage;
    protected int $defaultQuality = 80;
    protected int $defaultMaxWidth = 1200;
    protected int $defaultMaxHeight = 1200;

    public function __construct()
    {
        $this->storage = StorageManager::disk('public');
    }

    public function uploadAndConvert($file, string $directory, array $options = [])
    {
        if (!$file->isValid()) {
            throw new Exception("فایل معتبر نیست");
        }

        $quality = $options['quality'] ?? $this->defaultQuality;
        $maxWidth = $options['maxWidth'] ?? $this->defaultMaxWidth;
        $maxHeight = $options['maxHeight'] ?? $this->defaultMaxHeight;
        $keepOriginal = $options['keepOriginal'] ?? false;

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $webpName = $this->sanitizeFilename($originalName) . '.webp';
        $webpPath = $directory . '/' . $webpName;

        $imageData = file_get_contents($file->getTempName());
        if (!$imageData) {
            throw new Exception("خطا در خواندن فایل");
        }

        $webpData = $this->convertToWebP($imageData, $quality, $maxWidth, $maxHeight);
        if (!$webpData) {
            throw new Exception("خطا در تبدیل تصویر به WebP");
        }

        $saved = $this->storage->put($webpPath, $webpData);
        if (!$saved) {
            throw new Exception("خطا در ذخیره فایل WebP");
        }

        if ($keepOriginal) {
            $ext = $file->getClientOriginalExtension();
            $origName = $this->sanitizeFilename($originalName) . '.' . $ext;
            $origPath = $directory . '/' . $origName;
            $this->storage->put($origPath, $imageData);
        }

        return $webpPath;
    }

    protected function convertToWebP(string $imageData, int $quality, ?int $maxWidth, ?int $maxHeight): ?string
    {
        $srcImage = @imagecreatefromstring($imageData);
        if (!$srcImage) {
            return null;
        }

        $width = imagesx($srcImage);
        $height = imagesy($srcImage);
        $resized = false;

        if ($maxWidth && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($height * ($maxWidth / $width));
            $resized = true;
        } elseif ($maxHeight && $height > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = (int) ($width * ($maxHeight / $height));
            $resized = true;
        }

        if ($resized) {
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $dstImage;
        }

        ob_start();
        $success = imagewebp($srcImage, null, $quality);
        $webpData = ob_get_clean();
        imagedestroy($srcImage);

        return $success ? $webpData : null;
    }

    protected function sanitizeFilename(string $filename): string
    {
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
        return trim(preg_replace('/\s+/', '-', $filename));
    }
}
