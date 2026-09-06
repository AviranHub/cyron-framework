<?php
namespace Cyron\Validation\Rules;

use Cyron\Validation\Rule;

class In implements Rule
{
    protected $allowed;

    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    public function passes($field, $value, $data)
    {
        if (is_null($value)) return true; // nullable
        return in_array($value, $this->allowed);
    }

    public function message($field)
    {
        return "فیلد {$field} باید یکی از مقادیر مجاز باشد: " . implode(', ', $this->allowed);
    }
}