<?php
namespace Cyron\Validation\Rules;

use Cyron\Validation\Rule;

class Same implements Rule
{
    protected $otherField;

    public function __construct($otherField)
    {
        $this->otherField = $otherField;
    }

    public function passes($fieldName, $value, $data)
    {
        $otherValue = $data[$this->otherField] ?? null;
        return $value == $otherValue;
    }

    public function message($field)
    {
        return "فیلد {$field} باید با فیلد {$this->otherField} یکسان باشد.";
    }
}