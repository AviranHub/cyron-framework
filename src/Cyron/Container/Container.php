<?php

namespace Cyron\Container;

class Container
{
    protected static array $bindings = [];
    protected static array $instances = [];

    public static function bind(string $abstract, $concrete = null): void
    {
        static::$bindings[$abstract] = $concrete ?? $abstract;
    }

    public static function singleton(string $abstract, $concrete = null): void
    {
        static::bind($abstract, $concrete);
    }

    public static function make(string $abstract)
    {
        if (isset(static::$instances[$abstract])) return static::$instances[$abstract];

        $concrete = static::$bindings[$abstract] ?? $abstract;
        if ($concrete instanceof \Closure) {
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
