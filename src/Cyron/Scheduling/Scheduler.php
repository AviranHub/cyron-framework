<?php

namespace Cyron\Scheduling;

use Cyron\Queue\Queue;

class Scheduler
{
    protected $schedule;
    protected $statePath;

    public function __construct(Schedule $schedule, $statePath)
    {
        $this->schedule = $schedule;
        $this->statePath = $statePath;
    }

    public function list()
    {
        return array_map(function (ScheduledTask $task) {
            return [
                'description' => $task->getDescription(),
                'frequency' => $task->getFrequency(),
                'type' => $task->getType(),
            ];
        }, $this->schedule->tasks());
    }

    public function run($now = null, ?callable $output = null)
    {
        $now = $now ?: time();
        $output = $output ?: static function ($message) {
            echo $message . PHP_EOL;
        };
        $state = $this->readState();
        $executed = 0;
        foreach ($this->schedule->tasks() as $index => $task) {
            $key = (string) $index;
            $lastRun = isset($state[$key]['last_run']) ? (int) $state[$key]['last_run'] : null;
            if (!$task->isDue($now, $lastRun)) {
                continue;
            }

            $output('Running [' . $task->getDescription() . ']');
            $this->execute($task);
            $state[$key] = [
                'last_run' => $now,
                'description' => $task->getDescription(),
            ];
            $executed++;
        }

        $this->writeState($state);
        return $executed;
    }

    protected function execute(ScheduledTask $task)
    {
        if ($task->getType() === 'callback') {
            call_user_func($task->getTarget());
            return;
        }

        if ($task->getType() === 'job') {
            Queue::push($task->getTarget());
            return;
        }

        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(BASE_PATH . '/zeno');
        $command .= ' ' . escapeshellarg($task->getTarget());
        foreach ($task->getArguments() as $argument) {
            $command .= ' ' . escapeshellarg((string) $argument);
        }

        passthru($command, $exitCode);
        if ($exitCode !== 0) {
            throw new \RuntimeException('Scheduled command failed: ' . $task->getTarget());
        }
    }

    protected function readState()
    {
        if (!is_file($this->statePath)) {
            return [];
        }

        $state = json_decode(file_get_contents($this->statePath), true);
        return is_array($state) ? $state : [];
    }

    protected function writeState(array $state)
    {
        $directory = dirname($this->statePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($this->statePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
