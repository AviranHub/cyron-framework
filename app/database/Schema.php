<?php

namespace App\Database;

use App\Database\Db;

class Schema
{
    public static function create(string $table, callable $callback): void
    {
        // $builder = new TableBuilder($table);
        // $callback($builder);
        // $sql = $builder->build();

        // $db = Db::getInstance();
        // if ($db->query($sql) === true) {
        //     echo "Table '{$table}' created successfully.\n";
        // } else {
        //     echo "Error creating table '{$table}': " . $db->error . "\n";
        // }

        $builder = new TableBuilder($table);
        $callback($builder);
        $sql = $builder->build();

        // نمایش SQL برای دیباگ
        error_log( "\n================== SQL FOR TABLE `{$table}` ==================\n");
        error_log( $sql . "\n");
        error_log( "==========================================================\n");

        $db = Db::getInstance();
        if ($db->query($sql) === true) {
            error_log( "Table '{$table}' created successfully.\n");
        } else {
            error_log( "Error creating table '{$table}': " . $db->error . "\n");
        }
    }

    public static function drop(string $table): void
    {
        $db = Db::getInstance();
        $db->query("DROP TABLE IF EXISTS `{$table}`");
        echo "Table '{$table}' dropped.\n";
    }

    public static function dropIfExists(string $table): void
    {
        $db = Db::getInstance();
        $result = $db->query("SHOW TABLES LIKE '{$table}'");
        if ($result->num_rows > 0) {
            $db->query("DROP TABLE `{$table}`");
            echo "Table '{$table}' dropped.\n";
        }
    }
}
