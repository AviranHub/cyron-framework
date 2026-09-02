<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Http/Security/ProductionGuard.php';

use App\Core\Http\Security\ProductionGuard;

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
    expectTrue(false, 'production debug must fail');
} catch (RuntimeException $e) {
    expectTrue(true, 'production debug must fail');
}

putenv('APP_DEBUG=false');
try {
    ProductionGuard::validate();
    expectTrue(true, 'production without debug passes');
} catch (Throwable $e) {
    expectTrue(false, 'production without debug passes');
}

exit($failed ? 1 : 0);
