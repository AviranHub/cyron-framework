<?php

namespace Cyron\Validation\Rules;

use Cyron\Validation\Rule;

class StringRule implements Rule
{
    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true;
        if (!is_string($value)) return false;
        if ($value === '') return true;
        return preg_match('/\p{L}/u', $value) === 1;
    }

    public function message($field)
    {
        return "فیلد {$field} باید حداقل شامل یک حرف (فارسی یا انگلیسی) باشد و نمی‌تواند فقط شامل عدد یا نماد باشد.";
    }
}
