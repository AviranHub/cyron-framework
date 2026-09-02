<?php
// cli/PluginPublishCommand.php

require_once __DIR__ . '/../Colors.php';

class PluginPublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Publish a plugin to the application structure";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            echo Colors::error("Usage: php zeno plugin:publish <plugin-name>\n");
            return;
        }

        $pluginDir = APP_PATH . '/Plugins/' . $name;
        $manifestFile = $pluginDir . '/plugin.json';

        if (!file_exists($manifestFile)) {
            echo Colors::error("Plugin '{$name}' not found or manifest missing.\n");
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);
        $publish = $manifest['publish'] ?? [];

        if (empty($publish)) {
            echo Colors::error("Invalid plugin.json: missing 'publish' section.\n");
            return;
        }

        // 1. Controllers
        if (isset($publish['controllers']) && is_dir($pluginDir . '/Controllers')) {
            $target = APP_PATH . '/' . $publish['controllers'];
            $this->copyDirectory($pluginDir . '/Controllers', $target);
            echo Colors::green("✓ Controllers copied to {$publish['controllers']}\n");
        }

        // 2. Views
        if (isset($publish['views']) && is_dir($pluginDir . '/Views')) {
            $target = RESOURCES_PATH . '/' . $publish['views'];
            $this->copyDirectory($pluginDir . '/Views', $target);
            echo Colors::green("✓ Views copied to {$publish['views']}\n");
        }

        // 3. Routes
        if (isset($publish['routes']) && file_exists($pluginDir . '/routes.php')) {
            $routesContent = file_get_contents($pluginDir . '/routes.php');
            $targetRouteFile = ROUTES_PATH . '/' . $publish['routes'];
            $dir = dirname($targetRouteFile);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (file_exists($targetRouteFile)) {
                file_put_contents($targetRouteFile, "\n" . $routesContent, FILE_APPEND);
            } else {
                file_put_contents($targetRouteFile, $routesContent);
            }
            echo Colors::green("✓ Routes added to {$publish['routes']}\n");
        }

        // 4. Assets
        if (isset($publish['assets']) && is_dir($pluginDir . '/assets')) {
            $target = PUBLIC_PATH . '/' . $publish['assets'];
            $this->copyDirectory($pluginDir . '/assets', $target);
            echo Colors::green("✓ Assets copied to {$publish['assets']}\n");
        }

        // 5. Config (کپی به app/Config با نام دلخواه)
        if (isset($publish['config']) && file_exists($pluginDir . '/config/plugin_config.php')) {
            $targetConfig = APP_PATH . '/Config/' . $publish['config'];
            $configDir = dirname($targetConfig);
            if (!is_dir($configDir)) mkdir($configDir, 0755, true);
            copy($pluginDir . '/config/plugin_config.php', $targetConfig);
            echo Colors::green("✓ Config copied to app/Config/{$publish['config']}\n");
        }

        $this->trackPublished("plugin_{$name}", $publish);
        echo Colors::brightGreen("\n✓ Plugin '{$name}' published successfully.\n");
    }

    protected function copyDirectory($src, $dst)
    {
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }

    protected function trackPublished($name, $paths)
    {
        $trackFile = STORAGE_PATH . '/published.json';
        $data = file_exists($trackFile) ? json_decode(file_get_contents($trackFile), true) : [];
        $data[$name] = $paths;
        file_put_contents($trackFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}