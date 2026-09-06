<?php
namespace Cyron\Validation\Rules;

use Cyron\Validation\Rule;

class File implements Rule
{
    public function passes($field, $value, $data)
    {
        // فرض می‌کنیم $value در اینجا شیء فایل معتبر باشد.
        return !is_null($value) && is_object($value) && method_exists($value, 'isValid') && $value->isValid();
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک فایل معتبر باشد.";
    }
}