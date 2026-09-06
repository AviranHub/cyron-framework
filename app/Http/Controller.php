<?php

namespace App\Http;

if (!class_exists('Cyron\\Http\\Controller')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Http/Controller.php';
}

class Controller extends \Cyron\Http\Controller
{
}
