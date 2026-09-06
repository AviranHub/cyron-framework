<?php

namespace Cyron\Database;

class Migrator
{
    protected static $migrationsTable = 'migrations';

    public static function run($path = null)
    {
        self::ensureMigrationsTable();
        $executed = self::getExecutedMigrations();
        $path = $path ?: self::defaultPath();
        $files = glob(rtrim($path, '/\\') . '/*.php') ?: [];
        $batch = self::getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = pathinfo($file, PATHINFO_FILENAME);
            if (in_array($migrationName, $executed, true)) {
                continue;
            }

            $migration = require $file;
            if (!$migration instanceof Migration) {
                echo "✗ Invalid migration file: {$migrationName}\n";
                continue;
            }

            call_user_func([$migration, 'up']);
            self::recordMigration($migrationName, $batch);
            echo "✓ Migrated: {$migrationName}\n";
        }
    }

    public static function rollback($steps = 1, $path = null)
    {
        self::ensureMigrationsTable();
        $lastBatch = self::getLastBatchNumber();
        if ($lastBatch < 1) {
            return;
        }

        $targetBatch = max(1, $lastBatch - max(1, (int) $steps) + 1);
        $path = $path ?: self::defaultPath();
        foreach (self::getMigrationsByBatch($targetBatch) as $migration) {
            $file = rtrim($path, '/\\') . '/' . $migration['migration'] . '.php';
            if (!is_file($file)) {
                continue;
            }

            $instance = require $file;
            if ($instance instanceof Migration) {
                call_user_func([$instance, 'down']);
                self::deleteMigration($migration['id']);
                echo "✓ Rolled back: {$migration['migration']}\n";
            }
        }
    }

    protected static function defaultPath()
    {
        return defined('BASE_PATH') ? BASE_PATH . '/database/Migrations' : dirname(__DIR__, 3) . '/database/Migrations';
    }

    protected static function ensureMigrationsTable()
    {
        $db = Db::getInstance();
        $result = $db->query("SHOW TABLES LIKE 'migrations'");
        if ($result && $result->num_rows > 0) {
            return;
        }

        $db->query("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255) NOT NULL, batch INT NOT NULL, executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    }

    protected static function getExecutedMigrations()
    {
        $result = Db::getInstance()->query('SELECT migration FROM migrations ORDER BY id');
        $migrations = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $migrations[] = $row['migration'];
        }
        return $migrations;
    }

    protected static function getNextBatchNumber()
    {
        $result = Db::getInstance()->query('SELECT MAX(batch) AS max_batch FROM migrations');
        $row = $result->fetch_assoc();
        return ((int) ($row['max_batch'] ?? 0)) + 1;
    }

    protected static function getLastBatchNumber()
    {
        $result = Db::getInstance()->query('SELECT MAX(batch) AS max_batch FROM migrations');
        $row = $result->fetch_assoc();
        return (int) ($row['max_batch'] ?? 0);
    }

    protected static function getMigrationsByBatch($batch)
    {
        $stmt = Db::getInstance()->prepare('SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC');
        $stmt->bind_param('i', $batch);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    protected static function recordMigration($name, $batch)
    {
        $stmt = Db::getInstance()->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
        $stmt->bind_param('si', $name, $batch);
        $stmt->execute();
    }

    protected static function deleteMigration($id)
    {
        $stmt = Db::getInstance()->prepare('DELETE FROM migrations WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
}
