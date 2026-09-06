<?php
$root = __DIR__;
define('BASE_PATH', $root);
define('APP_PATH', $root . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', $root . DIRECTORY_SEPARATOR . 'public');
define('RESOURCES_PATH', $root . DIRECTORY_SEPARATOR . 'resources');
define('ROUTES_PATH', $root . DIRECTORY_SEPARATOR . 'routes');
define('STORAGE_PATH', $root . DIRECTORY_SEPARATOR . 'storage');
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require APP_PATH . DIRECTORY_SEPARATOR . 'autoload.php';
require APP_PATH . DIRECTORY_SEPARATOR . 'bootstrap.php';

use Cyron\Routing\Route;

// Simulate the HTTP request for /api/v1/books/c03f39
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/v1/books/c03f39';

// Add debug logging to Route::run()
$instance = Route::getInstance();

// Check all routes that might match
$reflectionProperty = new ReflectionProperty($instance, 'routes');
$reflectionProperty->setAccessible(true);
$routes = $reflectionProperty->getValue($instance);

echo "Checking which route matches /api/v1/books/c03f39...\n\n";

foreach ($routes as $i => $route) {
    if ($route['method'] !== 'GET' && $route['method'] !== 'ANY') continue;
    
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)(\?)?\}/', '(?P<$1>[^/]+)$2', $route['uri']);
    $pattern = '#^' . $pattern . '$#';
    
    $matches = [];
    if (preg_match($pattern, '/api/v1/books/c03f39', $matches)) {
        echo "Route #$i: " . $route['method'] . " " . $route['uri'] . " ✓ MATCH\n";
        echo "  Action: " . json_encode($route['action']) . "\n";
        break;
    }
}
