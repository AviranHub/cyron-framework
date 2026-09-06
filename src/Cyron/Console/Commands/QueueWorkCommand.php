<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;
use Cyron\Queue\Worker;

class QueueWorkCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'Process queued jobs (use --once or --daemon)';
    }

    public function execute()
    {
        $queue = $this->input->getOption('queue', 'default');
        $jobs = (int) $this->input->getOption('jobs', 1);
        $attempts = (int) $this->input->getOption('tries', 3);
        $daemon = $this->input->hasOption('daemon');
        $worker = new Worker();
        $output = function ($message) {
            echo Colors::green($message) . PHP_EOL;
        };

        if ($daemon) {
            $worker->daemon($queue, max(1, $attempts), (int) $this->input->getOption('sleep', 5), $output);
            return;
        }

        $processed = $worker->work($queue, max(1, $jobs), max(1, $attempts), $output);

        echo Colors::brightGreen("Processed jobs: {$processed}\n");
    }
}
