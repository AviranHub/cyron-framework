<?php

namespace App\Http;

if (!class_exists('Cyron\\Http\\Middleware')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Http/Middleware.php';
}

abstract class Middleware extends \Cyron\Http\Middleware
{
}