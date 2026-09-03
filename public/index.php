<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');


// تعریف مسیرهای ثابت
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('ROUTES_PATH', BASE_PATH . '/routes');
define('STORAGE_PATH', BASE_PATH . '/storage');

// بارگذاری bootstrap
require_once APP_PATH . '/bootstrap.php';

// اجرای روت‌ها
use App\Route;
Route::run();

// گزارش خطاها (فقط در محیط توسعه)
