<?php

namespace App\Core\Http\Security;

final class ProductionGuard
{
    public static function validate(): void
    {
        $env = strtolower(self::string('APP_ENV', getenv('APP_ENV')));
        $debug = self::bool('APP_DEBUG', getenv('APP_DEBUG'));

        if ($env !== 'production') {
            return;
        }

        if ($debug) {
            throw new \RuntimeException('Production environment cannot run with APP_DEBUG enabled.');
        }

        self::ensureSecret('APP_KEY');
        self::ensureUrl();
        self::ensureWritablePaths();
    }

    private static function ensureSecret(string $name): void
    {
        $value = self::string($name, getenv($name));
        $weak = ['', 'changeme', 'replace-me', 'secret', 'password', '123456789'];

        if (in_array(strtolower($value), $weak, true) || strlen($value) < 32) {
            throw new \RuntimeException("Production requires a strong {$name} (minimum 32 characters).");
        }
    }

    private static function ensureUrl(): void
    {
        $url = self::string('APP_URL', getenv('APP_URL'));

        if ($url === '' || preg_match('~^(?:https?://)?(?:localhost|127\.0\.0\.1|0\.0\.0\.0|\[::1\])(?:[:/]|$)~i', $url)) {
            throw new \RuntimeException('Production APP_URL must use a real non-loopback host.');
        }
    }

    private static function ensureWritablePaths(): void
    {
        $paths = [
            defined('STORAGE_PATH') ? STORAGE_PATH : (defined('ROOT_PATH') ? ROOT_PATH . '/storage' : null),
            defined('LOG_PATH') ? LOG_PATH : (defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs' : null),
        ];

        foreach ($paths as $path) {
            if ($path && is_dir($path) && (fileperms($path) & 0002)) {
                throw new \RuntimeException('Production storage path must not be world-writable: ' . $path);
            }
        }
    }

    private static function string(string $key, $fallback = null): string
    {
        $value = defined($key)
            ? constant($key)
            : ($fallback !== false && $fallback !== null
                ? $fallback
                : (function_exists('vars') ? vars($key) : null));

        return is_string($value) ? trim($value) : (string) ($value ?? '');
    }

    private static function bool(string $key, $fallback = null): bool
    {
        $value = defined($key) ? constant($key) : $fallback;

        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1;

        return is_string($value)
            && in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }
}
