<?php

namespace Cyron\Scheduling;

use Cyron\Queue\Job;

class Schedule
{
    protected $tasks = [];

    public function command($command, array $arguments = [], $description = null)
    {
        $task = new ScheduledTask('command', $command, $arguments, $description);
        $this->tasks[] = $task;
        return $task;
    }

    public function call(callable $callback, $description = null)
    {
        $task = new ScheduledTask('callback', $callback, [], $description);
        $this->tasks[] = $task;
        return $task;
    }

    public function job(Job $job, $description = null)
    {
        $task = new ScheduledTask('job', $job, [], $description ?: get_class($job));
        $this->tasks[] = $task;
        return $task;
    }

    public function tasks()
    {
        return $this->tasks;
    }
}
