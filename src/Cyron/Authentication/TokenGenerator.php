<?php

namespace Cyron\Authentication;

final class TokenGenerator
{
    public static function make(int $bytes = 40): string
    {
        if ($bytes < 1) {
            throw new \InvalidArgumentException('Token size must be positive.');
        }

        return bin2hex(random_bytes($bytes));
    }

    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
