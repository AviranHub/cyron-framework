<?php
namespace App\Core\Log;

use App\Core\Log\drivers\FileDriver;
use App\Core\Log\drivers\DriverInterface;

class LogManager
{
    protected static ?DriverInterface $driver = null;
    protected static string $minLevel = 'debug'; // سطح حداقل لاگ

    public static function driver(?string $name = null): DriverInterface
    {
        if (self::$driver === null) {
            $path = defined('STORAGE_PATH') ? STORAGE_PATH . '/logs' : __DIR__ . '/../../../storage/logs';
            self::$driver = new FileDriver($path);
        }
        return self::$driver;
    }

    public static function setMinLevel(string $level): void
    {
        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if (in_array($level, $levels)) {
            self::$minLevel = $level;
        }
    }

    protected static function shouldLog(string $level): bool
    {
        $levels = ['debug' => 0, 'info' => 1, 'notice' => 2, 'warning' => 3, 'error' => 4, 'critical' => 5, 'alert' => 6, 'emergency' => 7];
        return $levels[$level] >= $levels[self::$minLevel];
    }

    public static function __callStatic($method, $arguments)
    {
        if (method_exists(self::driver(), $method)) {
            if (self::shouldLog($method)) {
                return self::driver()->$method(...$arguments);
            }
        }
        throw new \BadMethodCallException("Method {$method} does not exist.");
    }
}