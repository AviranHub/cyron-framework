<?php
namespace App\Database;

if (!class_exists('Cyron\\Database\\Collection')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Database/Collection.php';
}

class Collection extends \Cyron\Database\Collection
{
}