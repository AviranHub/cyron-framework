<?php
namespace App\Core\Storage;

use App\Core\Storage\drivers\PublicDriver;
use App\Core\Storage\drivers\PrivateDriver;

class StorageManager
{
    protected static ?string $basePath = null;
    protected static array $disks = [];

    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/');
    }

    public static function disk(string $name = 'public'): DriverInterface
    {
        if (isset(self::$disks[$name])) {
            return self::$disks[$name];
        }

        $root = self::$basePath . '/' . $name;
        switch ($name) {
            case 'public':
                return self::$disks[$name] = new PublicDriver($root);
            case 'private':
                return self::$disks[$name] = new PrivateDriver($root);
            default:
                throw new \InvalidArgumentException("Disk [{$name}] not supported.");
        }
    }

    // متد کمکی برای دسترسی سریع به دیسک پیش‌فرض (public)
    public static function put(string $path, $contents): bool
    {
        return self::disk('public')->put($path, $contents);
    }

    public static function get(string $path): string
    {
        return self::disk('public')->get($path);
    }

    public static function delete(string $path): bool
    {
        return self::disk('public')->delete($path);
    }

    public static function exists(string $path): bool
    {
        return self::disk('public')->exists($path);
    }

    public static function url(string $path): string
    {
        return self::disk('public')->url($path);
    }

    public static function upload(array $file, string $subdirectory = ''): string
    {
        return self::disk('public')->upload($file, $subdirectory);
    }
}