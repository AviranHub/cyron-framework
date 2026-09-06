<?php

namespace App\Database\Migrations;

use Cyron\Database\Migration;
use Cyron\Database\Db;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();
        foreach ([
            'ALTER TABLE `user_activities` ADD INDEX `user_activities_occurred_at_index` (`occurred_at`)',
            'ALTER TABLE `user_activities` ADD INDEX `user_activities_category_occurred_at_index` (`category`, `occurred_at`)',
            'ALTER TABLE `user_activities` ADD INDEX `user_activities_action_occurred_at_index` (`action`, `occurred_at`)',
        ] as $sql) {
            if (!$db->query($sql) && strpos($db->error, 'Duplicate key name') === false) {
                throw new \RuntimeException('Unable to add analytics index.');
            }
        }
    }

    public static function down()
    {
        $db = Db::getInstance();
        foreach ([
            'user_activities_occurred_at_index',
            'user_activities_category_occurred_at_index',
            'user_activities_action_occurred_at_index',
        ] as $index) {
            $db->query('ALTER TABLE `user_activities` DROP INDEX `' . $index . '`');
        }
    }
};