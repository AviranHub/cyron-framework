<?php

namespace App\Database;

use App\Core\Env;
use mysqli;

class Db
{
    protected static $instance = null;
    protected $mysqli;

    private function __construct()
    {
        $this->mysqli = new mysqli(
            Env::get('DB_HOST', '127.0.0.1'),
            Env::get('DB_USERNAME', ''),
            Env::get('DB_PASSWORD', ''),
            Env::get('DB_DATABASE', Env::get('DB_NAME', 'cyron')),
            (int) Env::get('DB_PORT', 3306)
        );

        if ($this->mysqli->connect_error) {
            throw new \RuntimeException('Database connection failed.');
        }

        $this->mysqli->set_charset('utf8mb4');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Db();
        }
        return self::$instance->mysqli;
    }
}
