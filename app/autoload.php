<?php

spl_autoload_register(function ($class) {

    if (strpos($class, 'App\\Database\\Migrations\\') === 0) {
        return;
    }

    if (strpos($class, 'App\\Plugins') === 0) {
        return;
    }

    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// بارگذاری خودکار پوشه‌ها با Glob (بهینه شده)
function autoloadDirectory($directory)
{
    $path = APP_PATH . '/' . $directory;
    if (!is_dir($path)) return;

    $files = glob($path . "/*.php");
    foreach ($files as $file) {
        require_once $file;
    }
}

// بارگذاری مدل‌ها، کنترلرها، میدلورها و migration‌ها
autoloadDirectory('Models');
autoloadDirectory('Http/Controllers');
autoloadDirectory('Http/Middlewares');
// autoloadDirectory('database/Migrations');
autoloadDirectory('Core/Lady');


// ========== PHPMailer (بدون Composer) ==========
// لود کردن کتابخانه PHPMailer به صورت دستی
require_once APP_PATH . '/Libs/PHPMailer/Exception.php';
require_once APP_PATH . '/Libs/PHPMailer/PHPMailer.php';
require_once APP_PATH . '/Libs/PHPMailer/SMTP.php';
require_once APP_PATH . '/Libs/PHPMailer/POP3.php';    // اختیاری
require_once APP_PATH . '/Libs/PHPMailer/OAuthTokenProvider.php';   // اختیاری
require_once APP_PATH . '/Libs/PHPMailer/OAuth.php';   // اختیاری
// =================================================