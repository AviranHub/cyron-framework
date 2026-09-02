<?php

namespace App\Database;

use App\Database\Db;

class Migrator
{
    protected static $migrationsTable = 'migrations';
    protected static $migrationPath = 'app/database/Migrations/';

    public static function run()
    {
        self::ensureMigrationsTable();

        $executed = self::getExecutedMigrations(); // نام فایل‌های اجرا شده
        $files = glob(self::$migrationPath . '*.php');
        $batch = self::getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            if (in_array($migrationName, $executed)) continue;

            // بارگذاری فایل و دریافت نمونه (anonymous class)
            $migration = require $file;
            if ($migration instanceof \App\Database\Migration) {
                $migration->up();
                self::recordMigration($migrationName, $batch);
                echo "✓ Migrated: $migrationName\n";
            } else {
                echo "✗ Invalid migration file: $migrationName (must return a Migration instance)\n";
            }
        }
    }


    public static function rollback($steps = 1)
    {
        self::ensureMigrationsTable();
        $lastBatch = self::getLastBatchNumber();
        $targetBatch = $lastBatch - $steps + 1;
        if ($targetBatch < 1) $targetBatch = 1;

        $migrations = self::getMigrationsByBatch($targetBatch);
        foreach ($migrations as $migration) {
            $file = self::$migrationPath . $migration['migration'] . '.php';
            if (!file_exists($file)) continue;

            $migrationInstance = require $file;
            if ($migrationInstance instanceof \App\Database\Migration) {
                $migrationInstance->down();
                self::deleteMigration($migration['id']);
                echo "✓ Rolled back: " . $migration['migration'] . "\n";
            }
        }
    }

    protected static function ensureMigrationsTable()
    {
        $db = Db::getInstance();
        $result = $db->query("SHOW TABLES LIKE 'migrations'");
        if ($result->num_rows == 0) {
            // ساخت جدول با SQL مستقیم
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            if ($db->query($sql)) {
                echo "Table 'migrations' created successfully.\n";
            } else {
                echo "Error creating migrations table: " . $db->error . "\n";
            }
        }
    }

    protected static function getExecutedMigrations()
    {
        $db = Db::getInstance();
        $res = $db->query("SELECT migration FROM migrations ORDER BY id");
        $list = [];
        while ($row = $res->fetch_assoc()) {
            $list[] = $row['migration'];
        }
        return $list;
    }

    protected static function getNextBatchNumber()
    {
        $db = Db::getInstance();
        $res = $db->query("SELECT MAX(batch) as max FROM migrations");
        $row = $res->fetch_assoc();
        return ($row['max'] ?? 0) + 1;
    }

    protected static function getLastBatchNumber()
    {
        $db = Db::getInstance();
        $res = $db->query("SELECT MAX(batch) as max FROM migrations");
        $row = $res->fetch_assoc();
        return $row['max'] ?? 0;
    }

    protected static function getMigrationsByBatch($batch)
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC");
        $stmt->bind_param('i', $batch);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    protected static function recordMigration($name, $batch)
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $batch);
        $stmt->execute();
    }

    protected static function deleteMigration($id)
    {
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM migrations WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}
