<?php

namespace App\Database;

if (!class_exists('Cyron\\Database\\Db')) {
    require_once dirname(__DIR__, 2) . '/src/Cyron/Database/Db.php';
}

class Db extends \Cyron\Database\Db
{
}
