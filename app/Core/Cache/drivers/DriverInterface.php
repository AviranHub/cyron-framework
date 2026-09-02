<?php
namespace App\Core\Cache\drivers;

interface DriverInterface
{
    public function get(string $key, mixed $default = null): mixed;
    public function put(string $key, mixed $value, int $seconds = 3600): bool;
    public function remember(string $key, int $seconds, callable $callback): mixed;
    public function forget(string $key): bool;
    public function flush(): bool;
    public function has(string $key): bool;
    public function increment(string $key, int $amount = 1, int $seconds = 60): int;
}