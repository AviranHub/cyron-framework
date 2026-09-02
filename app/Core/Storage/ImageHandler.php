<?php
namespace App\Core\Storage;

use App\Core\Storage\StorageManager;
use Exception;

class ImageHandler
{
    protected StorageManager $storage;
    protected int $defaultQuality = 80;
    protected int $defaultMaxWidth = 1200;
    protected int $defaultMaxHeight = 1200;

    public function __construct()
    {
        $this->storage = StorageManager::disk('public'); // یا هر درایور دلخواه
    }

    /**
     * آپلود تصویر و تولید نسخه WebP
     * @param \App\Http\File $file شیء فایل آپلود شده (از Request)
     * @param string $directory دایرکتوری مقصد (نسبت به root storage)
     * @param array $options گزینه‌های تبدیل (quality, maxWidth, maxHeight, keepOriginal)
     * @return string|null مسیر فایل WebP ذخیره شده (نسبی به root storage)
     */
    public function uploadAndConvert($file, string $directory, array $options = [])
    {
        if (!$file->isValid()) {
            throw new Exception("فایل معتبر نیست");
        }

        // تنظیم گزینه‌ها
        $quality = $options['quality'] ?? $this->defaultQuality;
        $maxWidth = $options['maxWidth'] ?? $this->defaultMaxWidth;
        $maxHeight = $options['maxHeight'] ?? $this->defaultMaxHeight;
        $keepOriginal = $options['keepOriginal'] ?? false;

        // نام فایل WebP (با extension .webp)
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $webpName = $this->sanitizeFilename($originalName) . '.webp';
        $webpPath = $directory . '/' . $webpName;

        // خواندن محتوای فایل
        $imageData = file_get_contents($file->getTempName());
        if (!$imageData) {
            throw new Exception("خطا در خواندن فایل");
        }

        // تبدیل به WebP
        $webpData = $this->convertToWebP($imageData, $quality, $maxWidth, $maxHeight);
        if (!$webpData) {
            throw new Exception("خطا در تبدیل تصویر به WebP");
        }

        // ذخیره نسخه WebP در Storage
        $saved = $this->storage->put($webpPath, $webpData);
        if (!$saved) {
            throw new Exception("خطا در ذخیره فایل WebP");
        }

        // در صورت نیاز، نسخه اصلی هم ذخیره می‌شود
        if ($keepOriginal) {
            $ext = $file->getClientOriginalExtension();
            $origName = $this->sanitizeFilename($originalName) . '.' . $ext;
            $origPath = $directory . '/' . $origName;
            $this->storage->put($origPath, $imageData);
        }

        return $webpPath;
    }

    /**
     * تبدیل داده‌های تصویر به WebP (با تغییر اندازه اختیاری)
     * @param string $imageData باینری تصویر
     * @param int $quality کیفیت WebP (۰ تا ۱۰۰)
     * @param int|null $maxWidth حداکثر عرض (در صورت نیاز resize)
     * @param int|null $maxHeight حداکثر ارتفاع
     * @return string|null باینری تصویر WebP
     */
    protected function convertToWebP(string $imageData, int $quality, ?int $maxWidth, ?int $maxHeight): ?string
    {
        // ایجاد GD image از منبع
        $srcImage = @imagecreatefromstring($imageData);
        if (!$srcImage) {
            return null;
        }

        // تغییر اندازه در صورت نیاز
        $width = imagesx($srcImage);
        $height = imagesy($srcImage);
        $resized = false;

        if ($maxWidth && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)($height * ($maxWidth / $width));
            $resized = true;
        } elseif ($maxHeight && $height > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = (int)($width * ($maxHeight / $height));
            $resized = true;
        }

        if ($resized) {
            $dstImage = imagecreatetruecolor($newWidth, $newHeight);
            // حفظ شفافیت برای PNG/GIF
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $dstImage;
        }

        // تبدیل به WebP
        ob_start();
        $success = imagewebp($srcImage, null, $quality);
        $webpData = ob_get_clean();
        imagedestroy($srcImage);

        return $success ? $webpData : null;
    }

    /**
     * پاکسازی نام فایل (حذف کاراکترهای غیرمجاز)
     */
    protected function sanitizeFilename(string $filename): string
    {
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
        return trim(preg_replace('/\s+/', '-', $filename));
    }
}