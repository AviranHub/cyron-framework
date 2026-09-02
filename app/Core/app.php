<?php
// app/Core/app.php

use App\Core\Lady\Engine;

class AppContainer
{
    protected static array $bindings = [];
    protected static array $instances = [];

    public static function bind(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        static::$bindings[$abstract] = $concrete;
    }

    public static function singleton(string $abstract, $concrete = null): void
    {
        static::bind($abstract, $concrete);
    }

    public static function make(string $abstract)
    {
        if (isset(static::$instances[$abstract])) {
            return static::$instances[$abstract];
        }

        $concrete = static::$bindings[$abstract] ?? $abstract;

        if ($concrete instanceof Closure) {
            $object = $concrete();
        } elseif (is_string($concrete) && class_exists($concrete)) {
            $object = new $concrete();
        } else {
            $object = $concrete;
        }

        static::$instances[$abstract] = $object;
        return $object;
    }

    public static function has(string $abstract): bool
    {
        return isset(static::$bindings[$abstract]) || isset(static::$instances[$abstract]);
    }

    public static function instance(string $abstract, $instance): void
    {
        static::$instances[$abstract] = $instance;
    }
}

if (!function_exists('app')) {
    function app(?string $abstract = null)
    {
        if ($abstract === null) {
            return new class {
                public function bind(...$args) { return AppContainer::bind(...$args); }
                public function singleton(...$args) { return AppContainer::singleton(...$args); }
                public function make(...$args) { return AppContainer::make(...$args); }
                public function has(...$args) { return AppContainer::has(...$args); }
                public function instance(...$args) { return AppContainer::instance(...$args); }
            };
        }
        return AppContainer::make($abstract);
    }
}