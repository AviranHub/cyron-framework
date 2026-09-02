<?php
namespace App\Core\Plugin;

class HookManager
{
    protected static array $hooks = [];

    /**
     * ثبت یک تابع (callback) برای یک هوک مشخص
     */
    public static function listen(string $hook, callable $callback): void
    {
        if (!isset(self::$hooks[$hook])) {
            self::$hooks[$hook] = [];
        }
        self::$hooks[$hook][] = $callback;
    }

    /**
     * اجرای همه توابع ثبت شده برای یک هوک
     */
    public static function trigger(string $hook, ...$args): array
    {
        $results = [];
        if (isset(self::$hooks[$hook])) {
            foreach (self::$hooks[$hook] as $callback) {
                $results[] = $callback(...$args);
            }
        }
        return $results;
    }

    /**
     * اجرای یک هوک و برگرداندن اولین نتیجه غیر null
     */
    public static function first(string $hook, ...$args)
    {
        if (isset(self::$hooks[$hook])) {
            foreach (self::$hooks[$hook] as $callback) {
                $result = $callback(...$args);
                if ($result !== null) return $result;
            }
        }
        return null;
    }
}