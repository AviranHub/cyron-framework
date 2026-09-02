<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Integer implements Rule
{
    public function passes($field, $value, $data)
    {
        return is_null($value) || filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک عدد صحیح باشد.";
    }
}