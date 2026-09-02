<?php
// cli/PluginUnpublishCommand.php

require_once __DIR__ . '/../Colors.php';

class PluginUnpublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Remove published files of a plugin";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            echo Colors::error("Usage: php zeno plugin:unpublish <plugin-name>\n");
            return;
        }

        $trackFile = STORAGE_PATH . '/published.json';
        if (!file_exists($trackFile)) {
            echo Colors::error("No published records found.\n");
            return;
        }

        $data = json_decode(file_get_contents($trackFile), true);
        $key = "plugin_{$name}";
        if (!isset($data[$key])) {
            echo Colors::error("Plugin '{$name}' is not published.\n");
            return;
        }

        $paths = $data[$key];

        // حذف کنترلرها
        if (isset($paths['controllers'])) {
            $this->deleteDirectory(APP_PATH . '/' . $paths['controllers']);
            echo Colors::yellow("✓ Controllers removed\n");
        }
        // حذف ویوها
        if (isset($paths['views'])) {
            $this->deleteDirectory(RESOURCES_PATH . '/' . $paths['views']);
            echo Colors::yellow("✓ Views removed\n");
        }
        // حذف فایل روت
        if (isset($paths['routes'])) {
            $routeFile = ROUTES_PATH . '/' . $paths['routes'];
            if (file_exists($routeFile)) unlink($routeFile);
            echo Colors::yellow("✓ Routes file removed\n");
        }
        // حذف assets
        if (isset($paths['assets'])) {
            $this->deleteDirectory(PUBLIC_PATH . '/' . $paths['assets']);
            echo Colors::yellow("✓ Assets removed\n");
        }
        // حذف فایل کانفیگ
        if (isset($paths['config'])) {
            $configFile = APP_PATH . '/Config/' . $paths['config'];
            if (file_exists($configFile)) unlink($configFile);
            echo Colors::yellow("✓ Config removed\n");
        }

        unset($data[$key]);
        file_put_contents($trackFile, json_encode($data, JSON_PRETTY_PRINT));
        echo Colors::brightGreen("\n✓ Plugin '{$name}' unpublished.\n");
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