<?php

declare(strict_types=1);

require_once __DIR__.'/../app/Core/Http/Security/ProductionGuard.php';
require_once __DIR__.'/../app/Core/Validation/Rule.php';
require_once __DIR__.'/../app/Core/Validation/Rules/Unique.php';
require_once __DIR__.'/../app/Core/Validation/Rules/Exists.php';

use App\Core\Http\Security\ProductionGuard;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Exists;

$failed = 0;
function expectTrue(bool $condition, string $name): void
{
    global $failed;
    if (!$condition) {
        $failed++;
        echo "FAIL: $name\n";
    } else {
        echo "PASS: $name\n";
    }
}

putenv('APP_ENV=production');
putenv('APP_URL=https://example.test');
putenv('APP_KEY=0123456789abcdef0123456789abcdef0123456789abcdef');
putenv('APP_DEBUG=true');
try {
    ProductionGuard::validate();
    expectTrue(false, 'production debug rejected');
} catch (RuntimeException $e) {
    expectTrue(true, 'production debug rejected');
}

putenv('APP_DEBUG=false');
try {
    ProductionGuard::validate();
    expectTrue(true, 'production safe config accepted');
} catch (Throwable $e) {
    expectTrue(false, 'production safe config accepted');
}

foreach (
    [
        [Unique::class, 'users;DROP TABLE users'],
        [Unique::class, 'users', 'email;DROP'],
        [Exists::class, 'users;DROP TABLE users'],
        [Exists::class, 'users', 'email;DROP'],
    ] as $case
) {
    try {
        $rule = count($case) === 2
            ? new ($case[0])($case[1], 'email')
            : new ($case[0])($case[1], $case[2]);
        $rule->passes('email', 'x@example.test', []);
        expectTrue(false, 'unsafe SQL identifier rejected');
    } catch (InvalidArgumentException $e) {
        expectTrue(true, 'unsafe SQL identifier rejected');
    }
}

exit($failed ? 1 : 0);
