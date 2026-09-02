<?php
// cli/Commands/AuthPublishCommand.php

require_once __DIR__ . '/../Colors.php';

class AuthPublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Publish authentication system (models, controllers, views, routes, migrations, middlewares)";
    }

    public function execute()
    {
        $sourceDir = APP_PATH . '/Modules/Auth';
        $manifestFile = $sourceDir . '/auth.json';

        if (!file_exists($manifestFile)) {
            echo Colors::error("Auth module not found or manifest missing.\n");
            echo Colors::dim("Make sure app/Modules/Auth/ exists with auth.json\n");
            return;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);
        $publish = $manifest['publish'] ?? [];

        if (empty($publish)) {
            echo Colors::error("Invalid auth.json: missing 'publish' section.\n");
            return;
        }

        // 1. کپی مدل‌ها
        if (isset($publish['models']) && is_dir($sourceDir . '/Models')) {
            $target = APP_PATH . '/' . $publish['models'];
            $this->copyDirectory($sourceDir . '/Models', $target);
            echo Colors::green("✓ Models copied to {$publish['models']}\n");
        }

        // 2. کپی کنترلرها
        if (isset($publish['controllers']) && is_dir($sourceDir . '/Controllers')) {
            $target = APP_PATH . '/' . $publish['controllers'];
            $this->copyDirectory($sourceDir . '/Controllers', $target);
            echo Colors::green("✓ Controllers copied to {$publish['controllers']}\n");
        }

        // 3. کپی ویوها
        if (isset($publish['views']) && is_dir($sourceDir . '/Views')) {
            $target = RESOURCES_PATH . '/' . $publish['views'];
            $this->copyDirectory($sourceDir . '/Views', $target);
            echo Colors::green("✓ Views copied to {$publish['views']}\n");
        }

        // 4. افزودن روت‌ها (append به routes/auth.php)
        if (isset($publish['routes']) && file_exists($sourceDir . '/routes.php')) {
            $routesContent = file_get_contents($sourceDir . '/routes.php');
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

        // 5. کپی کانفیگ
        if (isset($publish['config']) && file_exists($sourceDir . '/config/auth.php')) {
            $targetConfig = APP_PATH . '/Config/' . $publish['config'];
            $configDir = dirname($targetConfig);
            if (!is_dir($configDir)) mkdir($configDir, 0755, true);
            copy($sourceDir . '/config/auth.php', $targetConfig);
            echo Colors::green("✓ Config copied to app/Config/{$publish['config']}\n");
        }

        // 6. کپی مایگریشن‌ها (اصلاح مسیر به APP_PATH)
        if (isset($publish['migrations']) && is_dir($sourceDir . '/Migrations')) {
            // مسیر مقصد: app/database/Migrations
            $target = APP_PATH . '/' . $publish['migrations'];
            $this->copyDirectory($sourceDir . '/Migrations', $target);
            echo Colors::green("✓ Migrations copied to {$publish['migrations']}\n");
        }

        // 7. کپی میدلورها
        if (isset($publish['middlewares']) && is_dir($sourceDir . '/Middlewares')) {
            $target = APP_PATH . '/' . $publish['middlewares'];
            $this->copyDirectory($sourceDir . '/Middlewares', $target);
            echo Colors::green("✓ Middlewares copied to {$publish['middlewares']}\n");
        }

        // ثبت اطلاعات برای unpublish
        $this->trackPublished('auth', $publish);

        echo Colors::brightGreen("\n✓ Authentication system published successfully.\n");
        echo Colors::dim("Next steps:\n");
        echo Colors::dim("  - Run: php zeno migrate\n");
        echo Colors::dim("  - Check routes: php zeno route:list\n");
        echo Colors::dim("  - Visit: /login, /register, /dashboard\n");
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