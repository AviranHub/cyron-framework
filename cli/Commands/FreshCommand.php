<?php
require_once __DIR__ . '/../Colors.php';

class FreshCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Drop all tables and re-run migrations (with optional seeding)";
    }

    public function execute()
    {
        echo Colors::gray300("  Dropping all tables...\n");
        $this->dropAllTables();
        echo Colors::green("  ✓ All tables dropped.\n");

        echo Colors::gray300("  Running migrations...\n");
        \App\Database\Migrator::run();
        echo Colors::green("  ✓ Migrations completed.\n");

        // اگر گزینه seed فعال باشد
        if ($this->input->getOption('seed')) {
            echo Colors::gray300("  Seeding database...\n");
            $this->runSeeders();
            echo Colors::green("  ✓ Seeding completed.\n");
        }

        echo Colors::brightGreen("\n✓ Database refreshed successfully.\n");
    }

    /**
     * حذف تمام جداول دیتابیس (با غیرفعال کردن موقت بررسی کلید خارجی)
     */
    protected function dropAllTables()
    {
        $db = \App\Database\Db::getInstance();

        // غیرفعال کردن بررسی کلید خارجی
        $db->query("SET FOREIGN_KEY_CHECKS = 0");

        // دریافت لیست تمام جداول
        $result = $db->query("SHOW TABLES");
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        // حذف هر جدول
        foreach ($tables as $table) {
            $db->query("DROP TABLE IF EXISTS `$table`");
        }

        // فعال کردن مجدد بررسی کلید خارجی
        $db->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    /**
     * اجرای سیدرها (مشابه php zeno db:seed)
     */
    protected function runSeeders()
    {
        // اگر کلاس DatabaseSeeder وجود دارد، آن را اجرا کن
        $seederPath = BASE_PATH . '/database/seeders/DatabaseSeeder.php';
        if (file_exists($seederPath)) {
            require_once $seederPath;
            if (class_exists('DatabaseSeeder')) {
                (new \DatabaseSeeder())->run();
                return;
            }
        }

        // اگر فایل اصلی وجود ندارد، پیام بده
        echo Colors::yellow("  No DatabaseSeeder found. Skipping seed.\n");
    }
}