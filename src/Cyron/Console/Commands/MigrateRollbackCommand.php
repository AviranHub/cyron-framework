<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;

class MigrateRollbackCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Rollback the last database migration batch";
    }

    public function execute()
    {
        $steps = (int) $this->input->getOption('step', 1);
        if ($steps < 1) {
            $steps = 1;
        }

        echo Colors::gray300("  Rolling back migrations...\n");
        \Cyron\Database\Migrator::rollback($steps);
        echo Colors::green("  ✓ Rollback completed.\n");
    }
}
