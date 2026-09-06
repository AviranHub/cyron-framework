# Scheduled Tasks

Cyron runs application automation through the framework scheduler. Define tasks in `app/Console/Schedule.php`:

```php
<?php

use Cyron\Scheduling\Schedule;

return static function (Schedule $schedule) {
    $schedule->command('sitemap:generate')->dailyAt('02:00');

    $schedule->call(function () {
        // Application maintenance work.
    }, 'Hourly maintenance')->hourly();
};
```

Available frequencies are `everyMinute()`, `hourly()`, `daily()`, and `dailyAt('HH:MM')`.

Add one cron entry on the server:

```cron
* * * * * cd /path/to/application && php zeno schedule:run >> storage/logs/scheduler.log 2>&1
```

Useful commands:

```bash
php zeno schedule:list
php zeno schedule:run
```

The scheduler stores the last execution time in `storage/schedule/state.json` to prevent a task from running repeatedly within its configured interval.