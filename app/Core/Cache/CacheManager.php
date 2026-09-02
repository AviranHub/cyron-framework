<?php
namespace App\Core\Cache;

use App\Core\Cache\drivers\FileDriver;
use App\Core\Cache\drivers\DriverInterface;

class CacheManager
{
    protected static ?DriverInterface $driver = null;

    public static function driver(?string $name = null): DriverInterface
    {
        if (self::$driver === null) {
            $path = defined('STORAGE_PATH') ? STORAGE_PATH . '/cache/data' : __DIR__ . '/../../../storage/cache/data';
            self::$driver = new FileDriver($path);
        }
        return self::$driver;
    }

    public static function get(string $key, mixed $default = null): mixed { return self::driver()->get($key, $default); }
    public static function put(string $key, mixed $value, int $seconds = 3600): bool { return self::driver()->put($key, $value, $seconds); }
    public static function increment(string $key, int $amount = 1, int $seconds = 60): int { return self::driver()->increment($key, $amount, $seconds); }
    public static function remember(string $key, int $seconds, callable $callback): mixed { return self::driver()->remember($key, $seconds, $callback); }
    public static function forget(string $key): bool { return self::driver()->forget($key); }
    public static function flush(): bool { return self::driver()->flush(); }
    public static function has(string $key): bool { return self::driver()->has($key); }
}