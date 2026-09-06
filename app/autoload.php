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
        require_once $file;
    }
});

spl_autoload_register(function ($class) {
    $prefix = 'App\\Database\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $cyronClass = 'Cyron\\Database\\' . substr($class, strlen($prefix));
    if (class_exists($cyronClass) || interface_exists($cyronClass)) {
        class_alias($cyronClass, $class);
    }
});

spl_autoload_register(function ($class) {
    $prefix = 'Cyron\\';
    $base_dir = dirname(__DIR__) . '/src/Cyron/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

foreach (['Schema', 'TableBuilder', 'Migration'] as $databaseClass) {
    $legacyClass = 'App\\Database\\' . $databaseClass;
    $cyronClass = 'Cyron\\Database\\' . $databaseClass;
    if (!class_exists($legacyClass, false) && class_exists($cyronClass)) {
        class_alias($cyronClass, $legacyClass);
    }
}

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

// Composer handles PSR-4 classes when available; retain eager loading only as a
// fallback for installations that have not run Composer yet.
$composerAvailable = class_exists('Composer\\Autoload\\ClassLoader', false);
if (!$composerAvailable) {
    autoloadDirectory('Models');
    autoloadDirectory('Http/Controllers');
    autoloadDirectory('Http/Middlewares');
    autoloadDirectory('Services');
}


// ========== PHPMailer (بدون Composer) ==========
// لود کردن کتابخانه PHPMailer به صورت دستی
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/Exception.php';
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/PHPMailer.php';
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/SMTP.php';
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/POP3.php';    // اختیاری
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/OAuthTokenProvider.php';   // اختیاری
require_once dirname(__DIR__) . '/src/Cyron/Libs/PHPMailer/OAuth.php';   // اختیاری
// =================================================