<?php

namespace Cyron\Database;

class Migration
{
    public static function createTable($tableName, $fields)
    {
        $db = Db::getInstance();
        $db->set_charset('utf8');
        $result = $db->query("SHOW TABLES LIKE '{$tableName}'");
        if ($result->num_rows > 0) return;

        $fieldDefs = [];
        foreach ($fields as $field => $type) $fieldDefs[] = "$field $type";
        $sql = "CREATE TABLE $tableName (" . implode(', ', $fieldDefs) . ") DEFAULT CHARSET=utf8mb4";
        if ($db->query($sql) === true) echo "Table '{$tableName}' created successfully.\n";
        else echo "Error creating table '{$tableName}': " . $db->error . "\n";
    }

    public static function dropTable($tableName): void
    {
        Db::getInstance()->query("DROP TABLE IF EXISTS `{$tableName}`");
    }
}
