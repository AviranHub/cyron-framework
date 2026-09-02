<?php
namespace App\Core\Validation;

interface Rule
{
    /**
     * بررسی صحت قانون
     * @param string $field
     * @param mixed $value
     * @param array $data (کل داده‌ها برای قوانینی مثل confirmed)
     * @return bool
     */
    public function passes($field, $value, $data);

    /**
     * پیام خطا
     * @param string $field
     * @return string
     */
    public function message($field);
}