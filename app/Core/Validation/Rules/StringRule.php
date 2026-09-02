<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class StringRule implements Rule
{
    public function passes($field, $value, $data)
    {
        // مقدار null مجاز است (برای فیلدهایی که required نیستند)
        if (is_null($value)) return true;
        
        // اگر مقدار رشته نباشد، رد کن
        if (!is_string($value)) return false;
        
        // رشته خالی مجاز است (برای فیلدهای غیراجباری، required آن را رد می‌کند)
        if ($value === '') return true;
        
        // حداقل یک حرف (Unicode) باید وجود داشته باشد
        // این الگو هر جا که حرف باشد (فارسی، انگلیسی، عربی و ...) را می‌گیرد
        return preg_match('/\p{L}/u', $value) === 1;
    }

    public function message($field)
    {
        return "فیلد {$field} باید حداقل شامل یک حرف (فارسی یا انگلیسی) باشد و نمی‌تواند فقط شامل عدد یا نماد باشد.";
    }
}