<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class RequiredIf implements Rule
{
    protected $field;
    protected $value;

    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function passes($fieldName, $value, $data)
    {
        // اگر شرط برقرار است، باید مقدار موجود و غیرخالی باشد
        if (isset($data[$this->field]) && $data[$this->field] == $this->value) {
            return !empty($value) || $value === '0' || $value === 0;
        }
        return true; // شرط برقرار نیست، الزامی نیست
    }

    public function message($field)
    {
        return "فیلد {$field} زمانی که {$this->field} برابر {$this->value} باشد الزامی است.";
    }
}