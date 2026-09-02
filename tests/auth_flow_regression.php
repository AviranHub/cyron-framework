<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$auth = file_get_contents($root . '/app/Core/Authentication/Auth.php');
$manager = file_get_contents($root . '/app/Auth/LoginManager.php');
$controller = file_get_contents($root . '/app/Modules/Auth/Controllers/Auth/LoginController.php');
$routes = file_get_contents($root . '/app/Modules/Auth/routes.php');

$checks = [
    'Auth exposes credential-only verification' => str_contains($auth, 'function credentials('),
    'LoginManager handles credential verification' => str_contains($manager, 'Auth::credentials('),
    'LoginManager handles 2FA before completion' => str_contains($manager, 'TwoFactor::enabled(') && str_contains($manager, "'pending_auth'"),
    'LoginManager registers authenticated sessions' => str_contains($manager, 'AuthenticationPipeline::succeeded('),
    'LoginController uses LoginManager' => str_contains($controller, 'LoginManager::attempt('),
    'LoginController does not call Auth::attempt directly' => !str_contains($controller, 'Auth::attempt('),
    '2FA challenge routes exist' => str_contains($routes, '/login/two-factor'),
];

$failed = 0;
foreach ($checks as $name => $ok) {
    echo ($ok ? 'PASS: ' : 'FAIL: ') . $name . PHP_EOL;
    if (!$ok) $failed++;
}
exit($failed ? 1 : 0);
