<?php
require_once __DIR__ . '/../Colors.php';

class MigrateCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Run database migrations";
    }

    public function execute()
    {
        echo Colors::gray300("  Running migrations...\n");
        \App\Database\Migrator::run();
        echo Colors::gray500("  ✓ Migrations completed!\n");
    }
}
