<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Different implements Rule
{
    protected $otherField;

    public function __construct($otherField)
    {
        $this->otherField = $otherField;
    }

    public function passes($fieldName, $value, $data)
    {
        $otherValue = $data[$this->otherField] ?? null;
        return $value != $otherValue;
    }

    public function message($field)
    {
        return "فیلد {$field} باید با فیلد {$this->otherField} متفاوت باشد.";
    }
}