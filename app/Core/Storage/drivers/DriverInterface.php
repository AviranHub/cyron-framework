<?php
namespace App\Core\Storage;

interface DriverInterface
{
    public function put(string $path, $contents): bool;
    public function get(string $path): string;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
    public function size(string $path): int|false;
    public function move(string $from, string $to): bool;
    public function copy(string $from, string $to): bool;
    public function url(string $path): string;
    public function upload(array $file, string $subdirectory = ''): string;
}