<?php
// cli/PluginInstallCommand.php

require_once __DIR__ . '/../Colors.php';

class PluginInstallCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Install a plugin (publish + migrate + activate)";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            echo Colors::error("Usage: php zeno plugin:install <plugin-name>\n");
            return;
        }

        // 1. Publish
        echo Colors::blue("\n[1/3] Publishing plugin...\n");
        $publishCmd = new PluginPublishCommand($this->input);
        // فراخوانی publish (از طریق متد execute اما با جلوگیری از خروجی اضافی؟ ساده: صدا بزن)
        // توجه: PluginPublishCommand نیاز به نام در آرگومان دارد. بهتر است یک متد publishOnly بسازیم یا همینجا کد publish را تکرار کنیم.
        // برای سادگی، دوباره کد publish را می‌نویسم (از تکرار کد خوشم نمیاد ولی فعلاً):
        $this->publishPlugin($name);

        // 2. Run migrations
        echo Colors::blue("\n[2/3] Running migrations...\n");
        $pluginPath = BASE_PATH . "/plugins/{$name}";
        $migrationPath = $pluginPath . "/Migrations";
        if (is_dir($migrationPath)) {
            require_once BASE_PATH . '/cli/MigrateCommand.php';
            $migrator = new \App\Database\Migrator();
            if (method_exists($migrator, 'runFromPath')) {
                $migrator->runFromPath($migrationPath, $name);
                echo Colors::green("✓ Migrations executed.\n");
            } else {
                echo Colors::error("Migrator::runFromPath not available. Run migrations manually.\n");
            }
        } else {
            echo Colors::dim("No migrations found.\n");
        }

        // 3. Activate
        echo Colors::blue("\n[3/3] Activating plugin...\n");
        require_once BASE_PATH . '/app/Core/Plugin/PluginManager.php';
        \App\Core\Plugin\PluginManager::setPath(BASE_PATH . '/plugins');
        \App\Core\Plugin\PluginManager::discover();
        \App\Core\Plugin\PluginManager::activate($name, true);
        echo Colors::brightGreen("\n✓ Plugin '{$name}' installed and activated.\n");
    }

    private function publishPlugin($name)
    {
        $pluginDir = BASE_PATH . '/plugins/' . $name;
        $manifestFile = $pluginDir . '/plugin.json';
        if (!file_exists($manifestFile)) {
            echo Colors::error("Manifest not found.\n");
            return;
        }
        $manifest = json_decode(file_get_contents($manifestFile), true);
        $publish = $manifest['publish'] ?? [];
        if (empty($publish)) return;

        // Controllers
        if (isset($publish['controllers']) && is_dir($pluginDir . '/Controllers')) {
            $target = APP_PATH . '/' . $publish['controllers'];
            $this->copyDirectory($pluginDir . '/Controllers', $target);
            echo Colors::green("✓ Controllers copied.\n");
        }
        // Views
        if (isset($publish['views']) && is_dir($pluginDir . '/Views')) {
            $target = RESOURCES_PATH . '/' . $publish['views'];
            $this->copyDirectory($pluginDir . '/Views', $target);
            echo Colors::green("✓ Views copied.\n");
        }
        // Routes
        if (isset($publish['routes']) && file_exists($pluginDir . '/routes.php')) {
            $routesContent = file_get_contents($pluginDir . '/routes.php');
            $targetRouteFile = ROUTES_PATH . '/' . $publish['routes'];
            $dir = dirname($targetRouteFile);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($targetRouteFile, $routesContent, FILE_APPEND);
            echo Colors::green("✓ Routes added.\n");
        }
        // Assets
        if (isset($publish['assets']) && is_dir($pluginDir . '/assets')) {
            $target = PUBLIC_PATH . '/' . $publish['assets'];
            $this->copyDirectory($pluginDir . '/assets', $target);
            echo Colors::green("✓ Assets copied.\n");
        }
        // Config
        if (isset($publish['config']) && file_exists($pluginDir . '/config/plugin_config.php')) {
            $targetConfig = APP_PATH . '/Config/' . $publish['config'];
            $configDir = dirname($targetConfig);
            if (!is_dir($configDir)) mkdir($configDir, 0755, true);
            copy($pluginDir . '/config/plugin_config.php', $targetConfig);
            echo Colors::green("✓ Config copied.\n");
        }

        $this->trackPublished("plugin_{$name}", $publish);
    }

    private function copyDirectory($src, $dst)
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

    private function trackPublished($name, $paths)
    {
        $trackFile = STORAGE_PATH . '/published.json';
        $data = file_exists($trackFile) ? json_decode(file_get_contents($trackFile), true) : [];
        $data[$name] = $paths;
        file_put_contents($trackFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}