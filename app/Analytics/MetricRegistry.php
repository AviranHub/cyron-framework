<?php
namespace App\Analytics;

class MetricRegistry
{
    protected static array $metrics = [];
    public static function register(string $key, array $definition): void
    {
        static::$metrics[$key] = array_merge(['label'=>$key,'event'=>null,'aggregation'=>'count','property'=>null], $definition);
    }
    public static function registerMany(array $metrics): void { foreach($metrics as $key=>$definition) static::register($key,$definition); }
    public static function all(): array { return static::$metrics; }
}