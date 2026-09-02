<?php
namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

class NotIn implements Rule
{
    protected $forbidden;

    public function __construct(array $forbidden)
    {
        $this->forbidden = $forbidden;
    }

    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true; // nullable
        return !in_array($value, $this->forbidden);
    }

    public function message($field)
    {
        return "فیلد {$field} نباید یکی از مقادیر غیرمجاز باشد: " . implode(', ', $this->forbidden);
    }
}