<?php

namespace Cyron\Lady;

class AttributeBag
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function merge(array $defaults): self
    {
        $merged = array_merge($defaults, $this->attributes);

        if (isset($defaults['class']) || isset($this->attributes['class'])) {
            $defaultClass = $defaults['class'] ?? '';
            $extraClass = $this->attributes['class'] ?? '';
            $merged['class'] = trim($defaultClass . ' ' . $extraClass);
        }

        return new self($merged);
    }

    public function get(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function all(): array
    {
        return $this->attributes;
    }

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
