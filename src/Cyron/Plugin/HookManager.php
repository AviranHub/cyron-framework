<?php

namespace Cyron\Plugin;

class HookManager
{
    protected static array $hooks = [];

    public static function listen(string $hook, callable $callback): void
    {
        if (!isset(self::$hooks[$hook])) {
            self::$hooks[$hook] = [];
        }
        self::$hooks[$hook][] = $callback;
    }

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
