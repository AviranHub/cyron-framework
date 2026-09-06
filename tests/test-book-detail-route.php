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

// Simulate a request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/v1/books/test-slug';

// Load routes
require ROUTES_PATH . DIRECTORY_SEPARATOR . 'api.php';

// Try to run the route
try {
    Route::run();
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
