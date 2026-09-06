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
            'user_id' => 'BIGINT UNSIGNED NULL',
            'session_id' => 'VARCHAR(191) NULL',
            'subject_type' => 'VARCHAR(191) NULL',
            'subject_id' => 'BIGINT UNSIGNED NULL',
            'ip_address' => 'VARCHAR(45) NULL',
            'user_agent' => 'TEXT NULL',
            'viewed_at' => 'TIMESTAMP NULL',
        ];
        foreach ($columns as $column => $definition) {
            $check = $db->query("SHOW COLUMNS FROM `views` LIKE '{$column}'");
            if (!$check || $check->num_rows === 0) $db->query("ALTER TABLE `views` ADD COLUMN `{$column}` {$definition}");
        }
        $db->query("ALTER TABLE `views` ADD INDEX `views_subject_index` (`subject_type`, `subject_id`)");
        $db->query("ALTER TABLE `views` ADD INDEX `views_session_index` (`session_id`, `viewed_at`)");
    }

    public static function down()
    {
        // Tracking columns are intentionally retained during rollback to protect existing analytics data.
    }
};