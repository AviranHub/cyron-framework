<?php

namespace App\Database;

use App\Database\Db;

class Migration
{
    public static function createTable($tableName, $fields)
    {
        $db = Db::getInstance();

        $db->set_charset("utf8");
        // بررسی وجود جدول
        $result = $db->query("SHOW TABLES LIKE '{$tableName}'");

        if ($result->num_rows > 0) {
            return; // جدول وجود دارد
        }

        // ساختن SQL برای ایجاد جدول
        $fieldDefs = [];
        foreach ($fields as $field => $type) {
            $fieldDefs[] = "$field $type";
        }
        $fieldString = implode(', ', $fieldDefs);

        $sql = "CREATE TABLE $tableName ($fieldString) DEFAULT CHARSET=utf8mb4";

        if ($db->query($sql) === TRUE) {
            echo "Table '{$tableName}' created successfully.\n";
        } else {
            echo "Error creating table '{$tableName}': " . $db->error . "\n";
        }
    }
    public static function dropTable($tableName)
    {
        $db = Db::getInstance();
        $db->query("DROP TABLE IF EXISTS `$tableName`");
    }
}
