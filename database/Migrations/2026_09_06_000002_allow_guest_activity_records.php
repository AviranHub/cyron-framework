<?php

namespace App\Database\Migrations;

use Cyron\Database\Db;
use Cyron\Database\Migration;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();
        $sql = 'ALTER TABLE `user_activities` MODIFY `user_id` BIGINT UNSIGNED NULL';
        if (!$db->query($sql) && strpos($db->error, 'Duplicate') === false) {
            throw new \RuntimeException('Unable to allow guest activity records.');
        }
    }

    public static function down()
    {
        Db::getInstance()->query('ALTER TABLE `user_activities` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
    }
};