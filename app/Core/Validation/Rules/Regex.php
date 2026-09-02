<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Regex implements Rule
{
    protected $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function passes($fieldName, $value, $data)
    {
        if (is_null($value)) return true;
        return preg_match($this->pattern, $value) === 1;
    }

    public function message($field)
    {
        return "فرمت فیلد {$field} معتبر نیست.";
    }
}