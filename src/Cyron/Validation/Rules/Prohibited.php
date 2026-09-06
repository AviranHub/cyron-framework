<?php
namespace Cyron\Validation\Rules;

use Cyron\Validation\Rule;

class Prohibited implements Rule
{
    public function passes($fieldName, $value, $data)
    {
        // فیلد نباید وجود داشته باشد یا مقدار آن خالی باشد
        return !isset($data[$fieldName]) || empty($data[$fieldName]);
    }

    public function message($field)
    {
        return "فیلد {$field} نباید ارسال شود یا باید خالی باشد.";
    }
}