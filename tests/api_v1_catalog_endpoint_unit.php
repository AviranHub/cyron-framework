<?php
declare(strict_types=1);

$root = dirname(__DIR__);
define('BASE_PATH', $root);
define('APP_PATH', $root . '/app');
define('PUBLIC_PATH', $root . '/public');
define('RESOURCES_PATH', $root . '/resources');
define('ROUTES_PATH', $root . '/routes');
define('STORAGE_PATH', $root . '/storage');
require $root . '/vendor/autoload.php';
require APP_PATH . '/autoload.php';
require APP_PATH . '/bootstrap.php';

use App\Http\Controllers\Api\V1\CatalogController;
use Cyron\Http\Request;
use Cyron\Http\Response;

// Simulate a GET request to /api/v1/books
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['per_page' => '10'];
$_POST = [];
$_FILES = [];
$_SERVER['HTTP_ACCEPT'] = 'application/json';

$request = new Request();
$controller = new CatalogController();

try {
    $response = $controller->books($request);
    if ($response instanceof Response) {
        echo "Status: " . $response->getStatusCode() . "\n";
        $content = $response->getContent();
        if (is_string($content)) {
            echo "Content: " . $content . "\n";
        } else {
            echo "Content (non-string): " . gettype($content) . "\n";
        }
    } else {
        echo "FAIL: Controller did not return Response\n";
        exit(1);
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e::class . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "PASS: API v1 catalog endpoint executes without exception\n";
