<?php

namespace Cyron\Database;

final class ModelRegistry
{
    private static array $models = [];

    public static function register(string $key, string $model): void
    {
        if (!is_a($model, Model::class, true)) {
            throw new \InvalidArgumentException("Model [{$model}] must extend " . Model::class . '.');
        }
        self::$models[$key] = $model;
    }

    public static function registerMany(array $models): void
    {
        foreach ($models as $key => $model) self::register($key, $model);
    }

    public static function get(string $key): string
    {
        if (!isset(self::$models[$key])) {
            throw new \RuntimeException("No model registered for [{$key}].");
        }
        return self::$models[$key];
    }
}