<?php
require_once __DIR__ . '/../Colors.php';

class AdminUnpublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Remove published admin panel files";
    }

    public function execute()
    {
        $trackFile = STORAGE_PATH . '/published.json';
        if (!file_exists($trackFile)) {
            echo Colors::error("No published records found.\n");
            return;
        }

        $data = json_decode(file_get_contents($trackFile), true);
        if (!isset($data['admin'])) {
            echo Colors::error("Admin panel not published.\n");
            return;
        }

        $paths = $data['admin'];

        // حذف فایل‌ها
        if (isset($paths['controllers'])) {
            $this->deleteDirectory(APP_PATH . '/' . $paths['controllers']);
            echo Colors::yellow("✓ Controllers removed\n");
        }
        if (isset($paths['views'])) {
            $this->deleteDirectory(RESOURCES_PATH . '/' . $paths['views']);
            echo Colors::yellow("✓ Views removed\n");
        }
        if (isset($paths['routes'])) {
            $routeFile = ROUTES_PATH . '/' . $paths['routes'];
            if (file_exists($routeFile)) unlink($routeFile);
            echo Colors::yellow("✓ Routes file removed\n");
        }
        if (isset($paths['assets'])) {
            $this->deleteDirectory(PUBLIC_PATH . '/' . $paths['assets']);
            echo Colors::yellow("✓ Assets removed\n");
        }
        if (isset($paths['config'])) {
            $configFile = APP_PATH . '/' . $paths['config'];
            if (file_exists($configFile)) unlink($configFile);
            echo Colors::yellow("✓ Config removed\n");
        }

        unset($data['admin']);
        file_put_contents($trackFile, json_encode($data, JSON_PRETTY_PRINT));
        echo Colors::brightGreen("\n✓ Admin panel unpublished.\n");
    }

    protected function deleteDirectory($dir)
    {
        if (!is_dir($dir)) return;
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
