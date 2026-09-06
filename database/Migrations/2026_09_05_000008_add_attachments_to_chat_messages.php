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
            'attachment_path' => 'VARCHAR(255) NULL',
            'attachment_name' => 'VARCHAR(255) NULL',
            'attachment_mime' => 'VARCHAR(100) NULL',
        ];
        foreach ($columns as $column => $definition) {
            $check = $db->query("SHOW COLUMNS FROM `chat_messages` LIKE '{$column}'");
            if (!$check || $check->num_rows === 0) {
                $db->query("ALTER TABLE `chat_messages` ADD COLUMN `{$column}` {$definition}");
            }
        }
    }

    public static function down()
    {
        $db = Db::getInstance();
        foreach (['attachment_path', 'attachment_name', 'attachment_mime'] as $column) {
            $check = $db->query("SHOW COLUMNS FROM `chat_messages` LIKE '{$column}'");
            if ($check && $check->num_rows > 0) $db->query("ALTER TABLE `chat_messages` DROP COLUMN `{$column}`");
        }
    }
};