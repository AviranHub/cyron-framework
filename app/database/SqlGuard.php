<?php
namespace App\Database;

if (!class_exists('Cyron\\Database\\SqlGuard')) {
	require_once dirname(__DIR__, 2) . '/src/Cyron/Database/SqlGuard.php';
}

class SqlGuard extends \Cyron\Database\SqlGuard
{
}
