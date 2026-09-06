<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use Cyron\Database\Db;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();

        if (!self::hasColumn($db, 'shelves', 'user_id')) {
            self::execute($db, "ALTER TABLE `shelves` ADD COLUMN `user_id` BIGINT UNSIGNED NOT NULL AFTER `id`");
            self::execute($db, "ALTER TABLE `shelves` ADD CONSTRAINT `fk_shelves_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
        }

        if (!self::hasColumn($db, 'shelves', 'name')) {
            self::execute($db, "ALTER TABLE `shelves` ADD COLUMN `name` VARCHAR(191) NOT NULL AFTER `user_id`");
        }

        if (!self::hasColumn($db, 'reading_progresses', 'user_id')) {
            self::execute($db, "ALTER TABLE `reading_progresses` ADD COLUMN `user_id` BIGINT UNSIGNED NOT NULL AFTER `id`");
            self::execute($db, "ALTER TABLE `reading_progresses` ADD CONSTRAINT `fk_reading_progresses_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
        }

        if (!self::hasColumn($db, 'reading_progresses', 'book_id')) {
            self::execute($db, "ALTER TABLE `reading_progresses` ADD COLUMN `book_id` BIGINT UNSIGNED NOT NULL AFTER `user_id`");
            self::execute($db, "ALTER TABLE `reading_progresses` ADD CONSTRAINT `fk_reading_progresses_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE");
        }

        if (!self::hasColumn($db, 'reading_progresses', 'last_page')) {
            self::execute($db, "ALTER TABLE `reading_progresses` ADD COLUMN `last_page` INT NOT NULL DEFAULT 1 AFTER `book_id`");
        }
    }

    public static function down()
    {
        $db = Db::getInstance();

        if (self::hasColumn($db, 'reading_progresses', 'book_id')) {
            self::execute($db, "ALTER TABLE `reading_progresses` DROP FOREIGN KEY `fk_reading_progresses_book_id`");
        }
        if (self::hasColumn($db, 'reading_progresses', 'user_id')) {
            self::execute($db, "ALTER TABLE `reading_progresses` DROP FOREIGN KEY `fk_reading_progresses_user_id`");
        }
        if (self::hasColumn($db, 'shelves', 'user_id')) {
            self::execute($db, "ALTER TABLE `shelves` DROP FOREIGN KEY `fk_shelves_user_id`");
        }

        foreach ([
            ['reading_progresses', 'last_page'],
            ['reading_progresses', 'book_id'],
            ['reading_progresses', 'user_id'],
            ['shelves', 'name'],
            ['shelves', 'user_id'],
        ] as [$table, $column]) {
            if (self::hasColumn($db, $table, $column)) {
                self::execute($db, "ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
            }
        }
    }

    private static function hasColumn($db, string $table, string $column): bool
    {
        $table = $db->real_escape_string($table);
        $column = $db->real_escape_string($column);
        $result = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $result !== false && $result->num_rows > 0;
    }

    private static function execute($db, string $sql): void
    {
        if ($db->query($sql) !== true) {
            throw new \RuntimeException('Library migration failed: ' . $db->error);
        }
    }
};
