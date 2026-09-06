<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;
use Cyron\Scheduling\Schedule;
use Cyron\Scheduling\Scheduler;

class ScheduleRunCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'Run due scheduled tasks';
    }

    public function execute()
    {
        $schedule = $this->loadSchedule();
        $scheduler = new Scheduler($schedule, STORAGE_PATH . '/schedule/state.json');
        $executed = $scheduler->run(null, function ($message) {
            echo Colors::green($message) . PHP_EOL;
        });

        echo Colors::brightGreen("Scheduled tasks completed: {$executed}\n");
    }

    protected function loadSchedule()
    {
        $schedule = new Schedule();
        $path = APP_PATH . '/Console/Schedule.php';
        if (!is_file($path)) {
            return $schedule;
        }

        $definition = require $path;
        if (is_callable($definition)) {
            $definition($schedule);
        } elseif ($definition instanceof Schedule) {
            return $definition;
        }

        return $schedule;
    }
}
