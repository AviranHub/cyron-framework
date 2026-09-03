<?php

namespace App\Database;

if (!class_exists('Cyron\\Database\\Paginator')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Database/Paginator.php';
}

class Paginator extends \Cyron\Database\Paginator
{
}