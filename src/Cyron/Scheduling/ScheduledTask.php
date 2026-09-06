<?php

namespace Cyron\Scheduling;

class ScheduledTask
{
    protected $type;
    protected $target;
    protected $arguments = [];
    protected $description;
    protected $frequency = 'everyMinute';

    public function __construct($type, $target, array $arguments = [], $description = null)
    {
        $this->type = $type;
        $this->target = $target;
        $this->arguments = $arguments;
        $this->description = $description ?: ($type === 'command' ? $target : 'Callback task');
    }

    public function everyMinute()
    {
        $this->frequency = 'everyMinute';
        return $this;
    }

    public function hourly()
    {
        $this->frequency = 'hourly';
        return $this;
    }

    public function daily()
    {
        $this->frequency = 'daily';
        return $this;
    }

    public function dailyAt($time)
    {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw new \InvalidArgumentException('Daily schedule time must use HH:MM format.');
        }

        $this->frequency = 'dailyAt:' . $time;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getFrequency()
    {
        return $this->frequency;
    }

    public function isDue($now, $lastRun = null)
    {
        if ($lastRun === null) {
            return true;
        }

        if ($this->frequency === 'everyMinute') {
            return $now - $lastRun >= 60;
        }

        if ($this->frequency === 'hourly') {
            return $now - $lastRun >= 3600;
        }

        if ($this->frequency === 'daily') {
            return date('Y-m-d', $now) !== date('Y-m-d', $lastRun);
        }

        if (str_starts_with($this->frequency, 'dailyAt:')) {
            $time = substr($this->frequency, 9);
            return date('H:i', $now) === $time && date('Y-m-d', $now) !== date('Y-m-d', $lastRun);
        }

        return false;
    }
}
