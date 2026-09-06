<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use Cyron\Database\Db;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();
        $columns = [
            'title' => "VARCHAR(191) NOT NULL",
            'slug' => "VARCHAR(191) NOT NULL",
            'excerpt' => "TEXT NULL",
            'content' => "LONGTEXT NULL",
            'cover' => "VARCHAR(255) NULL",
            'author_id' => "BIGINT UNSIGNED NULL",
            'category_id' => "BIGINT UNSIGNED NULL",
            'status' => "VARCHAR(32) NOT NULL DEFAULT 'draft'",
            'published_at' => "TIMESTAMP NULL",
        ];

        foreach ($columns as $column => $definition) {
            if (!self::hasColumn($db, 'articles', $column)) {
                self::execute($db, "ALTER TABLE `articles` ADD COLUMN `{$column}` {$definition}");
            }
        }
        self::execute($db, "ALTER TABLE `articles` ADD INDEX `articles_status_published_at_index` (`status`, `published_at`)");
    }

    public static function down()
    {
        $db = Db::getInstance();
        foreach (['published_at', 'status', 'category_id', 'author_id', 'cover', 'content', 'excerpt', 'slug', 'title'] as $column) {
            if (self::hasColumn($db, 'articles', $column)) {
                self::execute($db, "ALTER TABLE `articles` DROP COLUMN `{$column}`");
            }
        }
    }

    private static function hasColumn($db, string $table, string $column): bool
    {
        $result = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '" . $db->real_escape_string($column) . "'");
        return $result !== false && $result->num_rows > 0;
    }

    private static function execute($db, string $sql): void
    {
        if ($db->query($sql) !== true && stripos($sql, 'ADD INDEX') === false) {
            throw new \RuntimeException('Article migration failed: ' . $db->error);
        }
    }
};
