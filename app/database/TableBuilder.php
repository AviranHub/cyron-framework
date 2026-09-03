<?php

namespace App\Database;

if (!class_exists('Cyron\\Database\\TableBuilder')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Database/TableBuilder.php';
}

class TableBuilder extends \Cyron\Database\TableBuilder
{
}
