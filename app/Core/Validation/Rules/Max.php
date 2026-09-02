<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class Max implements Rule
{
    protected $max;
    protected $numeric;

    public function __construct($max, $numeric = false)
    {
        $this->max = (int)$max;
        $this->numeric = $numeric;
    }

    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true;

        if ($this->numeric) {
            return $value <= $this->max;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }

        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        return false;
    }

    public function message($field)
    {
        if ($this->numeric) {
            return "فیلد {$field} نباید بیشتر از {$this->max} باشد.";
        }
        return "فیلد {$field} نباید بیشتر از {$this->max} کاراکتر باشد.";
    }
}