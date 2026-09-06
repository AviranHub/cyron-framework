# Queue

Cyron provides a database-backed queue for work that should run outside the web request.

Create a job in `app/Jobs`:

```php
<?php

namespace App\Jobs;

use Cyron\Queue\Job;

class GenerateReport implements Job
{
    public function __construct(public int $userId)
    {
    }

    public function handle()
    {
        // Generate and store the report.
    }
}
```

Dispatch it from application code:

```php
use App\Jobs\GenerateReport;
use Cyron\Queue\Queue;

Queue::push(new GenerateReport($userId));
```

The queue table is created by the framework migration:

```bash
php zeno migrate
```

Run a worker manually or under a process supervisor:

```bash
php zeno queue:work --jobs=10 --tries=3
```

Inspect and retry failures:

```bash
php zeno queue:failed
php zeno queue:retry <id>
php zeno queue:retry all
```

A scheduled job can be dispatched from `app/Console/Schedule.php`:

```php
$schedule->job(new \App\Jobs\GenerateReport($userId))->daily();
```

The scheduled job is placed in the database queue when `schedule:run` executes. Keep a worker running to process it.
