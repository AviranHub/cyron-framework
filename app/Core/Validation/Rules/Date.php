<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Date implements Rule
{
    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true; // nullable
        // بررسی فرمت‌های رایج تاریخ: Y-m-d، Y/m/d، d-m-Y، و ...
        $timestamp = strtotime($value);
        return $timestamp !== false;
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک تاریخ معتبر باشد.";
    }
}