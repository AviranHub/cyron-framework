<?php
declare(strict_types=1);

$root = __DIR__;
define('BASE_PATH', $root);
define('APP_PATH', $root . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', $root . DIRECTORY_SEPARATOR . 'public');
define('RESOURCES_PATH', $root . DIRECTORY_SEPARATOR . 'resources');
define('ROUTES_PATH', $root . DIRECTORY_SEPARATOR . 'routes');
define('STORAGE_PATH', $root . DIRECTORY_SEPARATOR . 'storage');
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require APP_PATH . '/autoload.php';
require APP_PATH . '/bootstrap.php';

use Cyron\Routing\Route;

// Load routes
require ROUTES_PATH . '/api.php';

$instance = Route::getInstance();
$reflectionProperty = new ReflectionProperty($instance, 'routes');
$reflectionProperty->setAccessible(true);
$routes = $reflectionProperty->getValue($instance);

// Find api/v1/books routes
$catalogRoutes = array_filter($routes, function($route) {
    return strpos($route['uri'], '/api/v1/books') === 0;
});

echo "Found " . count($catalogRoutes) . " catalog routes:\n\n";
foreach ($catalogRoutes as $route) {
    echo "Method: " . $route['method'] . "\n";
    echo "URI: " . $route['uri'] . "\n";
    echo "Action: " . json_encode($route['action']) . "\n";
    echo "\n";
}
