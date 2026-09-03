<?php

namespace App;

if (!class_exists('Cyron\\Http\\Response')) {
    require_once dirname(__DIR__) . '/src/Cyron/Http/Response.php';
}

class Response extends \Cyron\Http\Response
{
}
