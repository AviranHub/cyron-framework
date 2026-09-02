<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Email implements Rule
{
    public function passes($field, $value, $data)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک ایمیل معتبر باشد.";
    }
}