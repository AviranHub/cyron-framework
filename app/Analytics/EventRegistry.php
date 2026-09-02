<?php
namespace App\Analytics;

class EventRegistry
{
    protected static array $events = [];

    public static function register(string $event, array $definition): void
    {
        static::$events[$event] = array_merge([
            'label' => $event,
            'category' => explode('.', $event, 2)[0],
        ], $definition);
    }

    public static function registerMany(array $events): void
    {
        foreach ($events as $event => $definition) static::register($event, $definition);
    }

    public static function get(string $event): array
    {
        return static::$events[$event] ?? [
            'label' => $event,
            'category' => explode('.', $event, 2)[0],
        ];
    }

    public static function all(): array { return static::$events; }
}