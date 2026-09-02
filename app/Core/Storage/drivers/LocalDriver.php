<?php
namespace App\Core\Storage\drivers;

use App\Core\Storage\DriverInterface;
use RuntimeException;

class LocalDriver implements DriverInterface
{
    protected string $root;
    protected string $visibility;

    public function __construct(string $root, string $visibility = 'public')
    {
        $this->root = rtrim($root, '/');
        $this->visibility = $visibility;
        $this->ensureDirectoryExists();
    }

    protected function ensureDirectoryExists(): void
    {
        if (!is_dir($this->root)) {
            mkdir($this->root, 0755, true);
        }
    }

    protected function getFullPath(string $path): string
    {
        $path = ltrim($path, '/');
        $full = $this->root . '/' . $path;
        // امنیت: جلوگیری از path traversal
        $realRoot = realpath($this->root);
        $realFull = realpath(dirname($full)) . '/' . basename($full);
        if (strpos($realFull, $realRoot) !== 0) {
            throw new RuntimeException("Access denied: {$path}");
        }
        return $full;
    }

    public function put(string $path, $contents): bool
    {
        $full = $this->getFullPath($path);
        $dir = dirname($full);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return file_put_contents($full, $contents) !== false;
    }

    public function get(string $path): string
    {
        $full = $this->getFullPath($path);
        if (!file_exists($full)) {
            throw new RuntimeException("File not found: {$path}");
        }
        return file_get_contents($full);
    }

    public function delete(string $path): bool
    {
        $full = $this->getFullPath($path);
        if (file_exists($full)) {
            return unlink($full);
        }
        return false;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function size(string $path): int|false
    {
        $full = $this->getFullPath($path);
        return file_exists($full) ? filesize($full) : false;
    }

    public function move(string $from, string $to): bool
    {
        $fromFull = $this->getFullPath($from);
        $toFull = $this->getFullPath($to);
        $dir = dirname($toFull);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return rename($fromFull, $toFull);
    }

    public function copy(string $from, string $to): bool
    {
        $fromFull = $this->getFullPath($from);
        $toFull = $this->getFullPath($to);
        $dir = dirname($toFull);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        return copy($fromFull, $toFull);
    }

    public function url(string $path): string
    {
        if ($this->visibility !== 'public') {
            throw new RuntimeException("Cannot generate URL for non-public disk.");
        }
        return '/storage/' . ltrim($path, '/');
    }

    public function upload(array $file, string $subdirectory = ''): string
    {
        if (!isset($file['tmp_name'], $file['error'], $file['size']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException("Invalid file upload.");
        }
        if ($file['size'] < 1 || $file['size'] > 10 * 1024 * 1024) {
            throw new RuntimeException("File size is not allowed.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        if (!isset($allowed[$mime])) {
            throw new RuntimeException("File type is not allowed.");
        }

        $subdir = trim(str_replace('\\\\', '/', $subdirectory), '/');
        if ($subdir !== '') {
            $parts = explode('/', $subdir);
            foreach ($parts as $part) {
                if ($part === '' || $part === '.' || $part === '..' || !preg_match('/^[A-Za-z0-9_-]+$/', $part)) {
                    throw new RuntimeException("Invalid upload subdirectory.");
                }
            }
        }
        $targetDir = $this->getFullPath($subdir ?: '.');
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException("Unable to create upload directory.");
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $targetFile = $targetDir . '/' . $newName;
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new RuntimeException("Failed to move uploaded file.");
        }
        @chmod($targetFile, 0644);
        return ($subdir ? $subdir . '/' : '') . $newName;
    }

    protected function sanitize(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-\p{L}]/u', '_', $name);
    }
}