<?php

namespace Cyron\Database;

class Schema
{
    public static function create(string $table, callable $callback): void
    {
        $builder = new TableBuilder($table);
        $callback($builder);
        $sql = $builder->build();
        $db = Db::getInstance();

        if ($db->query($sql) !== true) {
            throw new \RuntimeException("Error creating table '{$table}': " . $db->error);
        }
    }

    public static function drop(string $table): void
    {
        $db = Db::getInstance();
        if ($db->query("DROP TABLE IF EXISTS `{$table}`") !== true) {
            throw new \RuntimeException("Error dropping table '{$table}': " . $db->error);
        }
    }

    public static function dropIfExists(string $table): void
    {
        $db = Db::getInstance();
        $result = $db->query("SHOW TABLES LIKE '{$table}'");
        if ($result && $result->num_rows > 0) {
            self::drop($table);
        }
    }
}
