<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class RequiredWithout implements Rule
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
                // اگر حداقل یکی از فیلدها پر شده باشد، الزامی نیست
                return true;
            }
        }
        // هیچکدام از فیلدها پر نشده، پس این فیلد الزامی است
        return !empty($value) || $value === '0' || $value === 0;
    }

    public function message($field)
    {
        return "فیلد {$field} زمانی که هیچکدام از فیلدهای (" . implode(', ', $this->fields) . ") پر نشده باشد الزامی است.";
    }
}