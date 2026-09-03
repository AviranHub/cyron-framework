<?php

require_once APP_PATH . '/autoload.php';
require_once APP_PATH . '/Response.php';
require_once APP_PATH . '/Core/Env.php';
\App\Core\Env::load(defined('BASE_PATH') ? BASE_PATH . '/.env' : dirname(__DIR__) . '/.env');
require_once APP_PATH . '/helpers.php';
require_once APP_PATH . '/Libs/jdf.php';
require_once APP_PATH . '/database/Db.php';
require_once APP_PATH . '/database/Model.php';
require_once APP_PATH . '/database/Migration.php';
require_once APP_PATH . '/migrate.php';
require_once APP_PATH . '/Http/ErorrBag.php';
require_once APP_PATH . '/Http/Storage.php';
require_once APP_PATH . '/Http/Kernel.php';
require_once APP_PATH . '/Http/Middleware.php';
require_once APP_PATH . '/Http/Middlewares/CsrfMiddleware.php';
require_once APP_PATH . '/Http/Middlewares/SecurityHeadersMiddleware.php';
require_once APP_PATH . '/router.php';
require_once APP_PATH . '/str.php';
require_once APP_PATH . '/Core/app.php';
require_once APP_PATH . '/Core/Lady/Compiler.php';
require_once APP_PATH . '/Core/Lady/Engine.php';
require_once APP_PATH . '/Core/Lady/Parser.php';
require_once APP_PATH . '/Core/Localization/Translator.php';
require_once APP_PATH . '/Core/Authorization/Gate.php';
require_once APP_PATH . '/Core/Http/Security/ProductionGuard.php';

use App\Core\Env;
use App\Core\Lady\Parser;
use App\Core\Lady\Compiler;
use App\Core\Lady\Engine;
use App\Core\Storage\StorageManager;
use App\Core\Localization\Translator;
use App\Core\Exceptions\Handler;
use App\Core\Http\Security\ProductionGuard;
use App\Route;

$cachePath = STORAGE_PATH . '/cache/views';
if (!is_dir($cachePath)) mkdir($cachePath, 0755, true);

$parser = new Parser();
$compiler = new Compiler($parser, $cachePath);
$engine = new Engine($compiler, $cachePath, [
    RESOURCES_PATH . '/Layouts',
    RESOURCES_PATH . '/Views',
    RESOURCES_PATH,
]);
$GLOBALS['viewEngine'] = $engine;

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_name('cyron_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
session_start();

Translator::init();
Translator::setLocale('fa');
StorageManager::setBasePath(STORAGE_PATH);

$appEnv = strtolower((string) Env::get('APP_ENV', 'production'));
$appDebug = Env::get('APP_DEBUG');
if ($appDebug === null) {
    $appDebug = $appEnv === 'development';
}
$debug = $appEnv !== 'production' && filter_var($appDebug, FILTER_VALIDATE_BOOLEAN);
Handler::setDebug($debug);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
set_exception_handler([Handler::class, 'handle']);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});
ProductionGuard::validate();

Route::globalMiddleware(\App\Http\Middlewares\SecurityHeadersMiddleware::class);
Route::globalMiddleware(\App\Http\Middlewares\CsrfMiddleware::class);

require_once ROUTES_PATH . '/web.php';
