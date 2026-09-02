<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Boolean implements Rule
{
    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true; // nullable
        if (is_bool($value)) return true;
        if (is_numeric($value)) return $value == 0 || $value == 1;
        if (is_string($value)) {
            $value = strtolower($value);
            return in_array($value, ['1', '0', 'true', 'false', 'on', 'off']);
        }
        return false;
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک مقدار بولی (true/false یا 0/1) باشد.";
    }
}