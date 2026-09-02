<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Min implements Rule
{
    protected $min;
    protected $numeric;

    public function __construct($min, $numeric = false)
    {
        $this->min = (int)$min;
        $this->numeric = $numeric;
    }

    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true;
        if ($this->numeric) {
            return $value >= $this->min;
        }
        return mb_strlen($value) >= $this->min;
    }

    public function message($field)
    {
        if ($this->numeric) {
            return "فیلد {$field} باید حداقل {$this->min} باشد.";
        }
        return "فیلد {$field} باید حداقل {$this->min} کاراکتر باشد.";
    }
}