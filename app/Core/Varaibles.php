<?php
// تعریف مسیر پایه اگر وجود نداشت
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

$varaible = BASE_PATH . '/.var';
$file_name = "var.php";
$path = APP_PATH . '/includes/';

// بررسی وجود فایل .var
if (!file_exists($varaible)) {
    // ایجاد فایل .var پیش‌فرض
    $default_content = "APP_NAME=Cyron\nAPP_ENV=development\nAPP_DEBUG=true\nAPP_URL=http://localhost\nAPP_KEY=\nDB_HOST=localhost\nDB_NAME=clubhub\nDB_USERNAME=root\nDB_PASSWORD=\nADMIN_EMAIL=admin@example.com\nADMIN_PASSWORD=";
    file_put_contents($varaible, $default_content);
}

$file = file_get_contents($varaible);
$lines = explode("\n", $file);
$php_text = "<?php \n\n// Auto-generated config file\n// Do not edit manually\n\n";

foreach ($lines as $line) {
    // حذف فضای خالی و کاراکترهای خاص از ابتدا و انتهای خط
    $line = trim($line, " \t\n\r\0\x0B,"); // حذف فاصله و کاما از دو طرف
    
    // رد شدن از خطوط خالی یا کامنت
    if (empty($line) || strpos($line, '#') === 0) {
        continue;
    }

    // بررسی وجود '=' در خط
    if (strpos($line, '=') !== false) {
        $parts = explode('=', $line, 2);
        $define_name = trim($parts[0]);
        
        // حذف کاراکترهای اضافی از مقدار
        $define_value = trim($parts[1], " \t\n\r\0\x0B,'\"");
        
        // رد کردن خطوط نامعتبر
        if (empty($define_name)) {
            continue;
        }
        
        // تبدیل مقادیر خالی به رشته خالی
        if (empty($define_value)) {
            $php_text .= 'define(' . var_export($define_name, true) . ", '');\n";
        } else {
            $php_text .= 'define(' . var_export($define_name, true) . ', ' . var_export($define_value, true) . ");\n";
        }
    }
}

// اطمینان از وجود پوشه includes
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

// ذخیره فایل
file_put_contents($path . $file_name, $php_text);

// بارگذاری فایل ساخته شده
require_once $path . $file_name;
