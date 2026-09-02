<?php

namespace App\Database;

use mysqli;

class Db {
    protected static $instance = null;
    protected $mysqli;

    private function __construct() {
        $this->mysqli = new mysqli('localhost', DB_USERNAME, DB_PASSWORD, DB_NAME);

        if ($this->mysqli->connect_error) {
            throw new \RuntimeException('Database connection failed.');
        }
        $this->mysqli->set_charset('utf8mb4');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Db();
        }
        return self::$instance->mysqli;
    }
}
