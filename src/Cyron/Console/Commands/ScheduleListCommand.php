<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;
use Cyron\Scheduling\Schedule;
use Cyron\Scheduling\Scheduler;

class ScheduleListCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'List configured scheduled tasks';
    }

    public function execute()
    {
        $schedule = new Schedule();
        $path = APP_PATH . '/Console/Schedule.php';
        if (is_file($path)) {
            $definition = require $path;
            if (is_callable($definition)) {
                $definition($schedule);
            } elseif ($definition instanceof Schedule) {
                $schedule = $definition;
            }
        }

        $tasks = (new Scheduler($schedule, STORAGE_PATH . '/schedule/state.json'))->list();
        if (!$tasks) {
            echo Colors::yellow("No scheduled tasks configured.\n");
            return;
        }

        foreach ($tasks as $index => $task) {
            echo sprintf("%d. %-12s %s\n", $index + 1, $task['frequency'], $task['description']);
        }
    }
}
