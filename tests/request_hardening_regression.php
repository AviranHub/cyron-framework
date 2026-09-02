<?php

declare(strict_types=1);

require_once __DIR__.'/../app/Http/Middleware.php';
require_once __DIR__.'/../app/Http/Middlewares/RequestHardeningMiddleware.php';

use App\Http\Middlewares\RequestHardeningMiddleware;

$middleware = new RequestHardeningMiddleware();
$next = static fn($request) => 'NEXT';

$_SERVER['REQUEST_METHOD'] = 'TRACE';
$_SERVER['HTTP_HOST'] = 'example.test';
unset($_SERVER['CONTENT_LENGTH']);
if ($middleware->handle((object) [], $next) !== 'Method Not Allowed') {
    echo "FAIL: TRACE request was not blocked\n";
    exit(1);
}

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = 'bad host!';
if ($middleware->handle((object) [], $next) !== 'Invalid Host header') {
    echo "FAIL: invalid Host header was not blocked\n";
    exit(1);
}

$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['CONTENT_LENGTH'] = 'not-a-number';
if ($middleware->handle((object) [], $next) !== 'Invalid Content-Length header') {
    echo "FAIL: invalid Content-Length was not blocked\n";
    exit(1);
}

$_SERVER['CONTENT_LENGTH'] = '10';
if ($middleware->handle((object) [], $next) !== 'NEXT') {
    echo "FAIL: valid request did not reach next middleware\n";
    exit(1);
}

echo "PASS: request hardening regression checks passed\n";
