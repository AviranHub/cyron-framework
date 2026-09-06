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

$instance = Route::getInstance();
$reflectionProperty = new ReflectionProperty($instance, 'routes');
$reflectionProperty->setAccessible(true);
$routes = $reflectionProperty->getValue($instance);

echo "Total routes: " . count($routes) . "\n\n";

// Find only the /api/v1/books routes - check for duplicate or conflicting routes
$testUri = '/api/v1/books/c03f39';
$matchedRoutes = [];

foreach ($routes as $i => $route) {
    if ($route['method'] !== 'GET') continue;
    
    // Test pattern matching
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)(\?)?\}/', '(?P<$1>[^/]+)$2', $route['uri']);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $testUri, $matches)) {
        echo "Route #$i: " . $route['uri'] . " MATCHES\n";
        echo "  Action: " . json_encode($route['action']) . "\n";
        echo "  Middlewares: " . json_encode($route['middlewares']) . "\n";
        echo "  Pattern: $pattern\n";
        echo "\n";
        $matchedRoutes[] = $i;
    }
}

if (empty($matchedRoutes)) {
    echo "NO ROUTES MATCH: $testUri\n\n";
    echo "All routes with /books:\n";
    foreach ($routes as $i => $route) {
        if (strpos($route['uri'], '/books') !== false) {
            echo "Route #$i: " . $route['uri'] . " (" . $route['method'] . ")\n";
        }
    }
}
