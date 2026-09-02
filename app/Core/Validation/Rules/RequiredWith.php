<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class RequiredWith implements Rule
{
    protected $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function passes($fieldName, $value, $data)
    {
        foreach ($this->fields as $otherField) {
            if (isset($data[$otherField]) && !empty($data[$otherField])) {
                // حداقل یکی از فیلدها پر شده، پس این فیلد الزامی است
                return !empty($value) || $value === '0' || $value === 0;
            }
        }
        return true;
    }

    public function message($field)
    {
        return "فیلد {$field} زمانی که یکی از فیلدهای (" . implode(', ', $this->fields) . ") پر شده باشد الزامی است.";
    }
}