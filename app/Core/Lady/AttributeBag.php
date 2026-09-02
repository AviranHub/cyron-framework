<?php

namespace App\Core\Lady;

class AttributeBag
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * ادغام ویژگی‌ها با مقادیر پیش‌فرض (مشابه merge لاراول)
     */
    public function merge(array $defaults): self
    {
        $merged = array_merge($defaults, $this->attributes);

        // ادغام هوشمند کلاس‌ها
        if (isset($defaults['class']) || isset($this->attributes['class'])) {
            $defaultClass = $defaults['class'] ?? '';
            $extraClass = $this->attributes['class'] ?? '';
            $merged['class'] = trim($defaultClass . ' ' . $extraClass);
        }

        return new self($merged);
    }

    /**
     * دریافت یک ویژگی خاص
     */
    public function get(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * بررسی وجود ویژگی
     */
    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * دریافت همه ویژگی‌ها
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * تبدیل به رشته (برای استفاده در {!! $attributes !!})
     */
    public function __toString(): string
    {
        $parts = [];
        foreach ($this->attributes as $key => $value) {
            if ($value === null) continue;
            $parts[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
        }
        return implode(' ', $parts);
    }
}