<?php

namespace Cyron\Console;

class CommandLoader
{
    protected static $commands = [];

    public static function load(array $paths = [])
    {
        $paths = $paths ?: self::defaultPaths();

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            foreach (glob($path . '/*.php') ?: [] as $file) {
                require_once $file;
            }
        }

        self::discoverApplicationCommands();
    }

    public static function register($name, $className)
    {
        self::$commands[$name] = $className;
    }

    public static function commands()
    {
        return self::$commands;
    }

    protected static function discoverApplicationCommands()
    {
        foreach (get_declared_classes() as $className) {
            if (!str_starts_with($className, 'App\\Console\\Commands\\')) {
                continue;
            }

            if (method_exists($className, 'getName')) {
                self::register($className::getName(), $className);
            }
        }
    }

    protected static function defaultPaths()
    {
        $paths = [];

        if (defined('BASE_PATH')) {
            $paths[] = BASE_PATH . '/src/Cyron/Console/Commands';
            $paths[] = BASE_PATH . '/app/Console/Commands';
        }

        return $paths;
    }
}