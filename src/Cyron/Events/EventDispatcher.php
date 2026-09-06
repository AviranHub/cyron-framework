<?php

namespace Cyron\Events;

use Cyron\Queue\Queue;

class EventDispatcher
{
    protected $listeners = [];

    public function listen($event, $listener, $queued = false)
    {
        if (!is_callable($listener) && !(is_string($listener) && class_exists($listener))) {
            throw new \InvalidArgumentException('Event listener must be callable or a listener class.');
        }

        if ($queued && (!is_string($listener) || !method_exists($listener, 'handle'))) {
            throw new \InvalidArgumentException('Queued listeners must be classes with a handle() method.');
        }

        $this->listeners[$this->eventName($event)][] = [
            'listener' => $listener,
            'queued' => (bool) $queued,
        ];

        return $this;
    }

    public function dispatch($event, ...$arguments)
    {
        $name = $this->eventName($event);
        $payload = is_object($event) ? $event : $arguments;
        $results = [];

        foreach ($this->listeners[$name] ?? [] as $registration) {
            if ($registration['queued']) {
                Queue::push(new ListenerJob($registration['listener'], $payload));
                $results[] = null;
                continue;
            }

            $listener = $registration['listener'];
            if (is_string($listener) && class_exists($listener) && method_exists($listener, 'handle')) {
                $results[] = (new $listener())->handle($payload);
            } else {
                $results[] = is_object($event)
                    ? $listener($event)
                    : $listener(...$arguments);
            }
        }

        return $results;
    }

    public function listeners($event = null)
    {
        if ($event === null) {
            return $this->listeners;
        }

        return $this->listeners[$this->eventName($event)] ?? [];
    }

    protected function eventName($event)
    {
        return is_object($event) ? get_class($event) : (string) $event;
    }
}
