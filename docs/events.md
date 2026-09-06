# Events

Cyron provides an event dispatcher for application events. Events may be strings or objects.

## Synchronous listeners

```php
use Cyron\Events\Event;

Event::listen('user.registered', function ($userId) {
    // Run immediately.
});

Event::dispatch('user.registered', $userId);
```

Object events are useful when the event has several properties:

```php
class UserRegistered
{
    public function __construct(public int $userId) {}
}

Event::listen(UserRegistered::class, function (UserRegistered $event) {
    // Run immediately.
});

Event::dispatch(new UserRegistered($userId));
```

## Queued listeners

Queued listeners must be classes with a `handle($event)` method. They are stored in the database queue and processed by a worker:

```php
Event::listen(UserRegistered::class, SendWelcomeEmail::class, true);

Event::dispatch(new UserRegistered($userId));
```

Run the worker after creating the `jobs` table:

```bash
php zeno migrate
php zeno queue:work --daemon
```

The helper `event($event, ...$arguments)` is also available for dispatching.
