<?php

namespace App\Database\Migrations;

use App\Database\Migration;
use Cyron\Database\Db;

return new class extends Migration
{
    public static function up()
    {
        $db = Db::getInstance();
        $column = $db->query("SHOW COLUMNS FROM `transactions` LIKE 'subscription_id'");
        if (!$column || $column->num_rows === 0) {
            if ($db->query("ALTER TABLE `transactions` ADD COLUMN `subscription_id` BIGINT UNSIGNED NULL AFTER `payable_id`") !== true) {
                throw new \RuntimeException('Transaction migration failed: ' . $db->error);
            }
        }

        $foreign = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'subscription_id' AND REFERENCED_TABLE_NAME = 'subscriptions'");
        if ($foreign && $foreign->num_rows === 0) {
            if ($db->query("ALTER TABLE `transactions` ADD CONSTRAINT `transactions_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE SET NULL") !== true) {
                throw new \RuntimeException('Transaction foreign key migration failed: ' . $db->error);
            }
        }

        if (!$db->query("ALTER TABLE `transactions` ADD INDEX `transactions_subscription_id_index` (`subscription_id`)") && !str_contains(strtolower($db->error), 'duplicate')) {
            throw new \RuntimeException('Transaction index migration failed: ' . $db->error);
        }
    }

    public static function down()
    {
        $db = Db::getInstance();
        $foreign = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'subscription_id' AND REFERENCED_TABLE_NAME = 'subscriptions'");
        if ($foreign && ($row = $foreign->fetch_assoc())) {
            $name = $row['CONSTRAINT_NAME'];
            $db->query("ALTER TABLE `transactions` DROP FOREIGN KEY `{$name}`");
        }

        $column = $db->query("SHOW COLUMNS FROM `transactions` LIKE 'subscription_id'");
        if ($column && $column->num_rows > 0) {
            $db->query("ALTER TABLE `transactions` DROP COLUMN `subscription_id`");
        }
    }
};
