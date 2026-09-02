<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Url implements Rule
{
    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true; // nullable
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message($field)
    {
        return "فیلد {$field} باید یک آدرس وب معتبر باشد (شامل http:// یا https://).";
    }
}