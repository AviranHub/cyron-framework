<?php
namespace App\Core\Cache\drivers;

class FileDriver implements DriverInterface
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
        if (!is_dir($this->path)) mkdir($this->path, 0755, true);
    }

    protected function getFilePath(string $key): string
    {
        return $this->path . '/' . hash('sha256', $key) . '.cache';
    }

    protected function read(string $file): ?array
    {
        if (!is_file($file)) return null;
        $raw = file_get_contents($file);
        if ($raw === false) return null;
        $data = @unserialize($raw, ['allowed_classes' => false]);
        return is_array($data) && isset($data['expires']) ? $data : null;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getFilePath($key);
        $data = $this->read($file);
        if ($data === null || $data['expires'] < time()) {
            if ($data !== null) $this->forget($key);
            return $default;
        }
        return $data['value'] ?? $default;
    }

    public function put(string $key, mixed $value, int $seconds = 3600): bool
    {
        if ($seconds < 0) throw new \InvalidArgumentException('Cache lifetime cannot be negative.');
        $data = ['value' => $value, 'expires' => time() + $seconds];
        return file_put_contents($this->getFilePath($key), serialize($data), LOCK_EX) !== false;
    }

    public function increment(string $key, int $amount = 1, int $seconds = 60): int
    {
        $file = $this->getFilePath($key);
        $handle = fopen($file, 'c+');
        if ($handle === false) throw new \RuntimeException('Unable to open cache counter.');
        try {
            if (!flock($handle, LOCK_EX)) throw new \RuntimeException('Unable to lock cache counter.');
            $raw = stream_get_contents($handle);
            $data = $raw !== false ? @unserialize($raw, ['allowed_classes' => false]) : null;
            if (!is_array($data) || !isset($data['expires']) || $data['expires'] < time()) $data = ['value' => 0, 'expires' => time() + $seconds];
            $data['value'] = (int)($data['value'] ?? 0) + $amount;
            ftruncate($handle, 0); rewind($handle);
            $ok = fwrite($handle, serialize($data)); fflush($handle); flock($handle, LOCK_UN);
            if ($ok === false) throw new \RuntimeException('Unable to write cache counter.');
            return (int)$data['value'];
        } finally { fclose($handle); }
    }

    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        $value = $this->get($key);
        if ($value !== null) return $value;
        $value = $callback();
        $this->put($key, $value, $seconds);
        return $value;
    }

    public function forget(string $key): bool
    {
        $file = $this->getFilePath($key);
        return !file_exists($file) || unlink($file);
    }

    public function flush(): bool
    {
        $success = true;
        foreach (glob($this->path . '/*.cache') ?: [] as $file) if (!unlink($file)) $success = false;
        return $success;
    }

    public function has(string $key): bool { return $this->get($key, null) !== null; }
}