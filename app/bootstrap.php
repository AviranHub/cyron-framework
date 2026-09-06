<?php

$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}
require_once APP_PATH . '/autoload.php';
\Cyron\Support\Env::load(defined('BASE_PATH') ? BASE_PATH . '/.env' : dirname(__DIR__) . '/.env');
require_once BASE_PATH . '/src/Cyron/helpers.php';
require_once BASE_PATH . '/src/Cyron/Libs/jdf.php';
require_once BASE_PATH . '/src/Cyron/Http/ErrorBag.php';
require_once BASE_PATH . '/src/Cyron/Http/Storage.php';
require_once BASE_PATH . '/src/Cyron/Http/Kernel.php';
require_once APP_PATH . '/Http/Middleware.php';
require_once APP_PATH . '/Http/Middlewares/CsrfMiddleware.php';
require_once APP_PATH . '/Http/Middlewares/SecurityHeadersMiddleware.php';
require_once dirname(APP_PATH) . '/src/Cyron/Application.php';

\Cyron\Events\Event::load(APP_PATH . '/Events.php');

\Cyron\Plugin\PluginManager::discover([APP_PATH . '/Plugins']);
$pluginConfig = is_file(APP_PATH . '/Config/plugins.php') ? require APP_PATH . '/Config/plugins.php' : [];
foreach (($pluginConfig['enabled'] ?? []) as $pluginName) {
    \Cyron\Plugin\PluginManager::activate((string) $pluginName);
}

\Cyron\Database\ModelRegistry::registerMany([
    'user' => \App\Models\User::class,
    'login_attempt' => \App\Models\LoginAttempt::class,
    'login_history' => \App\Models\LoginHistory::class,
    'password_reset_token' => \App\Models\PasswordResetToken::class,
    'auth_session' => \App\Models\AuthSession::class,
    'remember_token' => \App\Models\RememberToken::class,
    'personal_access_token' => \App\Models\PersonalAccessToken::class,
    'user_totp' => \App\Models\UserTotp::class,
    'user_two_factor' => \App\Models\UserTwoFactor::class,
    'two_factor_recovery_code' => \App\Models\TwoFactorRecoveryCode::class,
    'verification_challenge' => \App\Models\VerificationChallenge::class,
    'user_activity' => \App\Models\UserActivity::class,
    'audit_log' => \App\Models\AuditLog::class,
]);

foreach ([APP_PATH . '/Config/analytics.php', APP_PATH . '/Config/metrics.php', APP_PATH . '/Config/segments.php'] as $analyticsConfig) {
    if (is_file($analyticsConfig)) require_once $analyticsConfig;
}

use Cyron\Support\Env;
use Cyron\Lady\Parser;
use Cyron\Lady\Compiler;
use Cyron\Lady\Engine;
use Cyron\Storage\StorageManager;
use Cyron\Localization\Translator;
use Cyron\Exceptions\Handler;
use Cyron\Http\Security\ProductionGuard;
use Cyron\Routing\Route;

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

\Cyron\Authorization\Gate::setUserResolver(static function () {
    return \Cyron\Authentication\Auth::user();
});
\Cyron\Authorization\Ownership::setUserResolver(static function () {
    return \Cyron\Authentication\Auth::user();
});

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
require_once ROUTES_PATH . '/docs.php';
require_once ROUTES_PATH . '/api.php';