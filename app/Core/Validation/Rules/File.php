<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class File implements Rule
{
    public function passes($field, $value, $data)
    {
        // فرض می‌کنیم $value در اینجا شیء File است (از متد file() گرفته می‌شود)
        // یا می‌توانیم بررسی کنیم که فایل آپلود شده وجود دارد.
        return !is_null($value) && $value instanceof \App\File && $value->isValid();
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک فایل معتبر باشد.";
    }
}