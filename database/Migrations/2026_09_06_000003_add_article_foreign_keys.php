<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use Cyron\Database\Db;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();

        self::addForeignKeyIfMissing(
            $db,
            'articles',
            'author_id',
            'users',
            'articles_author_id_foreign',
            'SET NULL'
        );

        self::addForeignKeyIfMissing(
            $db,
            'articles',
            'category_id',
            'article_categories',
            'articles_category_id_foreign',
            'SET NULL'
        );
    }

    public static function down()
    {
        $db = Db::getInstance();
        self::dropForeignKeyIfExists($db, 'articles', 'articles_category_id_foreign');
        self::dropForeignKeyIfExists($db, 'articles', 'articles_author_id_foreign');
    }

    private static function addForeignKeyIfMissing($db, string $table, string $column, string $referencedTable, string $constraint, string $onDelete): void
    {
        $schema = $db->real_escape_string((string) $db->query('SELECT DATABASE()')->fetch_row()[0]);
        $tableEscaped = $db->real_escape_string($table);
        $constraintEscaped = $db->real_escape_string($constraint);

        $result = $db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
            "WHERE TABLE_SCHEMA = '{$schema}' " .
            "AND TABLE_NAME = '{$tableEscaped}' " .
            "AND CONSTRAINT_NAME = '{$constraintEscaped}'"
        );

        if ($result && $result->num_rows > 0) {
            return;
        }

        $sql = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` " .
            "FOREIGN KEY (`{$column}`) REFERENCES `{$referencedTable}`(`id`) ON DELETE {$onDelete}";

        if ($db->query($sql) !== true) {
            throw new \RuntimeException("Article foreign key migration failed: {$db->error}");
        }
    }

    private static function dropForeignKeyIfExists($db, string $table, string $constraint): void
    {
        $schemaResult = $db->query('SELECT DATABASE()');
        $schema = $schemaResult ? $schemaResult->fetch_row()[0] : null;
        if (!$schema) {
            return;
        }

        $schema = $db->real_escape_string((string) $schema);
        $tableEscaped = $db->real_escape_string($table);
        $constraintEscaped = $db->real_escape_string($constraint);

        $result = $db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
            "WHERE TABLE_SCHEMA = '{$schema}' " .
            "AND TABLE_NAME = '{$tableEscaped}' " .
            "AND CONSTRAINT_NAME = '{$constraintEscaped}'"
        );

        if ($result && $result->num_rows > 0) {
            if ($db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`") !== true) {
                throw new \RuntimeException("Article foreign key rollback failed: {$db->error}");
            }
        }
    }
};
