<?php

namespace App\Core;

/**
 * Minimal .env loader for Cyron.
 *
 * .env is the single supported source of environment variables.
 */
class Env
{
    protected static array $values = [];

    public static function load(?string $path = null): void
    {
        $path = $path ?: dirname(__DIR__, 2) . '/.env';
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            self::$values[$key] = $value;
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }

        $value = getenv($key);
        return $value === false ? $default : $value;
    }

    public static function has(string $key): bool
    {
        return self::get($key, null) !== null;
    }
}
