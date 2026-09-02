<?php
// cli/Commands/AuthUnpublishCommand.php

require_once __DIR__ . '/../Colors.php';

class AuthUnpublishCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Remove published authentication system files (controllers, models, views, routes, config, migrations, middlewares)";
    }

    public function execute()
    {
        $trackFile = STORAGE_PATH . '/published.json';
        if (!file_exists($trackFile)) {
            echo Colors::error("No published records found.\n");
            return;
        }

        $data = json_decode(file_get_contents($trackFile), true);
        if (!isset($data['auth'])) {
            echo Colors::error("Auth module is not published.\n");
            return;
        }

        $paths = $data['auth'];

        // 1. حذف کنترلرها (پوشه Auth داخل مسیر کنترلرها)
        if (isset($paths['controllers'])) {
            $target = APP_PATH . '/' . $paths['controllers'] . '/Auth';
            if (is_dir($target)) {
                $this->deleteDirectory($target);
                echo Colors::yellow("✓ Auth controllers removed from {$paths['controllers']}/Auth\n");
            }
            // همچنین حذف کنترلرهای User/DashboardController اگر جداگانه باشند (اختیاری)
            $userControllerDir = APP_PATH . '/' . $paths['controllers'] . '/User';
            if (is_dir($userControllerDir)) {
                $this->deleteDirectory($userControllerDir);
                echo Colors::yellow("✓ User controllers removed from {$paths['controllers']}/User\n");
            }
        }

        // 2. حذف مدل‌های منتشر شده توسط auth
        if (isset($paths['models'])) {
            $modelsToDelete = ['User.php', 'Role.php', 'Permission.php', 'UserOtp.php', 'UserActivity.php'];
            $targetDir = APP_PATH . '/' . $paths['models'];
            foreach ($modelsToDelete as $modelFile) {
                $file = $targetDir . '/' . $modelFile;
                if (file_exists($file)) {
                    unlink($file);
                    echo Colors::yellow("✓ Model deleted: {$modelFile}\n");
                }
            }
        }

        // 3. حذف ویوهای auth
        if (isset($paths['views'])) {
            $authViewsDir = RESOURCES_PATH . '/' . $paths['views'] . '/auth';
            if (is_dir($authViewsDir)) {
                $this->deleteDirectory($authViewsDir);
                echo Colors::yellow("✓ Auth views removed from {$paths['views']}/auth\n");
            }
            $userViewsDir = RESOURCES_PATH . '/' . $paths['views'] . '/user';
            if (is_dir($userViewsDir)) {
                $this->deleteDirectory($userViewsDir);
                echo Colors::yellow("✓ User views removed from {$paths['views']}/user\n");
            }
        }

        // 4. حذف فایل روت
        if (isset($paths['routes'])) {
            $routeFile = ROUTES_PATH . '/' . $paths['routes'];
            if (file_exists($routeFile)) {
                unlink($routeFile);
                echo Colors::yellow("✓ Routes file removed: {$paths['routes']}\n");
            }
        }

        // 5. حذف فایل کانفیگ
        if (isset($paths['config'])) {
            $configFile = APP_PATH . '/Config/' . $paths['config'];
            if (file_exists($configFile)) {
                unlink($configFile);
                echo Colors::yellow("✓ Config file removed: app/Config/{$paths['config']}\n");
            }
        }

        // 6. حذف مایگریشن‌های مربوط به auth (مسیر اصلاح شده)
        if (isset($paths['migrations'])) {
            // مسیر مایگریشن‌ها: APP_PATH . '/database/Migrations'
            $migrationDir = APP_PATH . '/' . $paths['migrations'];  // تغییر از BASE_PATH به APP_PATH
            $authMigrations = [
                '2025_01_01_000001_create_users_table.php',
                '2025_01_01_000002_create_roles_table.php',
                '2025_01_01_000003_create_permissions_table.php',
                '2025_01_01_000004_create_role_permissions_table.php',
                '2025_01_01_000005_create_user_roles_table.php',
                '2025_01_01_000006_create_user_otps_table.php',
                '2025_01_01_000007_create_phone_verification_codes_table.php',
                '2025_01_01_000008_create_password_reset_tokens_table.php',
                '2025_01_01_000009_create_sessions_table.php',
                '2025_01_01_000010_create_user_activities_table.php',
            ];
            foreach ($authMigrations as $migrationFile) {
                $file = $migrationDir . '/' . $migrationFile;
                if (file_exists($file)) {
                    unlink($file);
                    echo Colors::yellow("✓ Migration removed: {$migrationFile}\n");
                }
            }
        }

        // 7. حذف میدلورها
        if (isset($paths['middlewares'])) {
            $middlewareFile = APP_PATH . '/' . $paths['middlewares'] . '/AuthMiddleware.php';
            if (file_exists($middlewareFile)) {
                unlink($middlewareFile);
                echo Colors::yellow("✓ AuthMiddleware removed\n");
            }
        }

        // حذف رکورد از published.json
        unset($data['auth']);
        file_put_contents($trackFile, json_encode($data, JSON_PRETTY_PRINT));

        echo Colors::brightGreen("\n✓ Authentication system unpublished successfully.\n");
        echo Colors::dim("Note: Database tables were NOT dropped. Run 'php zeno migrate:rollback' if needed.\n");
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