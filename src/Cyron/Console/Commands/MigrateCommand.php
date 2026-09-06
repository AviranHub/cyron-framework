<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;

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
        \Cyron\Database\Migrator::run();
        echo Colors::gray500("  ✓ Migrations completed!\n");
    }
}
