<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Confirmed implements Rule
{
    public function passes($field, $value, $data)
    {
        $confirmationField = $field . '_confirmation';
        return isset($data[$confirmationField]) && $value === $data[$confirmationField];
    }

    public function message($field)
    {
        return "تأیید فیلد {$field} مطابقت ندارد.";
    }
}