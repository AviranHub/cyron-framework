<?php

namespace App\Http;

class Storage
{
    protected static $drivers = [
        'public' => ['directory' => 'public', 'visibility' => 'public'],
        'private' => ['directory' => 'private', 'visibility' => 'private'],
    ];

    public static function driver($driver)
    {
        if (!isset(self::$drivers[$driver])) throw new \InvalidArgumentException("Driver not found.");
        return new StorageDriver(self::$drivers[$driver]);
    }
}

class StorageDriver
{
    protected static $root = './storage/';
    protected $directory;
    protected $visibility;
    protected array $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    public function __construct(array $config)
    {
        $this->directory = $config['directory'];
        $this->visibility = $config['visibility'];
    }

    public function upload($file)
    {
        if (!isset($file['tmp_name'], $file['error'], $file['size']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Invalid file upload.');
        }

        $max = (int) (vars('STORAGE_MAX_UPLOAD_SIZE') ?? vars('STORAGE_MAX_UPLUAD_SIZE') ?? 10) * 1024 * 1024;
        if ($file['size'] < 1 || $file['size'] > $max) throw new \RuntimeException('File size is not allowed.');

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!isset($this->allowedMimeTypes[$mime])) throw new \RuntimeException('File type is not allowed.');

        $targetDir = rtrim(self::$root, '/') . '/' . $this->directory;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Unable to create storage directory.');
        }

        $name = bin2hex(random_bytes(16)) . '.' . $this->allowedMimeTypes[$mime];
        $target = $targetDir . '/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $target)) throw new \RuntimeException('Failed to store uploaded file.');
        @chmod($target, 0644);

        return $name;
    }

    public function delete($fileName)
    {
        $target = rtrim(self::$root, '/') . '/' . $this->directory . '/' . basename($fileName);
        return file_exists($target) && unlink($target);
    }

    public function getFilePath($fileName)
    {
        $target = rtrim(self::$root, '/') . '/' . $this->directory . '/' . basename($fileName);
        if (!file_exists($target)) throw new \RuntimeException('File not found.');
        return $target;
    }

    public function info($file)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return [
            'type' => $finfo->file($file['tmp_name']),
            'size' => $file['size'] ?? 0,
            'name' => $file['name'] ?? '',
            'extension' => strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION)),
        ];
    }
}
