<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Required implements Rule
{
    public function passes($field, $value, $data)
    {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && empty($value)) return false;
        return true;
    }

    public function message($field)
    {
        return "فیلد {$field} اجباری است.";
    }
}