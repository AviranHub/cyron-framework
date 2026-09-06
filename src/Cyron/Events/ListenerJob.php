<?php

namespace Cyron\Events;

use Cyron\Queue\Job;

class ListenerJob implements Job
{
    protected $listener;
    protected $payload;

    public function __construct($listener, $payload)
    {
        $this->listener = $listener;
        $this->payload = $payload;
    }

    public function handle()
    {
        $listener = $this->listener;
        if (!class_exists($listener) || !method_exists($listener, 'handle')) {
            throw new \RuntimeException('Event listener class is not available.');
        }

        return (new $listener())->handle($this->payload);
    }
}
