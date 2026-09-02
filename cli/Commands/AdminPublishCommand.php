<?php
require_once __DIR__ . '/../Colors.php';

class AdminPublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Publish the admin panel to the application structure";
    }

    public function execute()
    {
        $sourceDir = APP_PATH . '/Modules/Admin';
        $manifestFile = $sourceDir . '/admin.json';

        if (!file_exists($manifestFile)) {
            echo Colors::error("Admin module not found or manifest missing.\n");
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);
        $publish = $manifest['publish'] ?? [];

        if (empty($publish)) {
            echo Colors::error("Invalid admin.json: missing 'publish' section.\n");
            return;
        }

        // 1. کپی کنترلرها
        if (isset($publish['controllers']) && is_dir($sourceDir . '/Controllers')) {
            $this->copyDirectory($sourceDir . '/Controllers', APP_PATH . '/' . $publish['controllers']);
            echo Colors::green("✓ Controllers copied to {$publish['controllers']}\n");
        }

        // 2. کپی ویوها
        if (isset($publish['views']) && is_dir($sourceDir . '/Views')) {
            $this->copyDirectory($sourceDir . '/Views', RESOURCES_PATH . '/' . $publish['views']);
            echo Colors::green("✓ Views copied to {$publish['views']}\n");
        }

        // 3. افزودن روت‌ها
        if (isset($publish['routes']) && file_exists($sourceDir . '/routes.php')) {
            $routesContent = file_get_contents($sourceDir . '/routes.php');
            $targetRouteFile = ROUTES_PATH . '/' . $publish['routes'];
            $dir = dirname($targetRouteFile);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            // اگر فایل وجود دارد، محتوا را append کن
            if (file_exists($targetRouteFile)) {
                file_put_contents($targetRouteFile, "\n" . $routesContent, FILE_APPEND);
            } else {
                file_put_contents($targetRouteFile, $routesContent);
            }
            echo Colors::green("✓ Routes added to {$publish['routes']}\n");
        }

        // 4. کپی assets به public
        if (isset($publish['assets']) && is_dir($sourceDir . '/assets')) {
            $this->copyDirectory($sourceDir . '/assets', PUBLIC_PATH . '/' . $publish['assets']);
            echo Colors::green("✓ Assets copied to {$publish['assets']}\n");
        }

        // 5. کپی کانفیگ
        if (isset($publish['config']) && file_exists($sourceDir . '/config/admin_models.php')) {
            $targetConfig = APP_PATH . '/' . $publish['config'];
            $configDir = dirname($targetConfig);
            if (!is_dir($configDir)) mkdir($configDir, 0755, true);
            copy($sourceDir . '/config/admin_models.php', $targetConfig);
            echo Colors::green("✓ Config copied to {$publish['config']}\n");
        }

        // ذخیره اطلاعات برای unpublish
        $this->trackPublished('admin', $publish);
        echo Colors::brightGreen("\n✓ Admin panel published successfully.\n");
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
