<?php

namespace Cyron\Events;

class Event
{
    protected static $dispatcher;

    public static function dispatcher()
    {
        if (static::$dispatcher === null) {
            static::$dispatcher = new EventDispatcher();
        }

        return static::$dispatcher;
    }

    public static function listen($event, $listener, $queued = false)
    {
        return static::dispatcher()->listen($event, $listener, $queued);
    }

    public static function dispatch($event, ...$arguments)
    {
        return static::dispatcher()->dispatch($event, ...$arguments);
    }

    public static function listeners($event = null)
    {
        return static::dispatcher()->listeners($event);
    }

    public static function load($path)
    {
        if (!is_file($path)) {
            return;
        }

        $definition = require $path;
        if (is_callable($definition)) {
            $definition(static::dispatcher());
        }
    }
}
