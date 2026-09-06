<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Cyron/Database/SqlGuard.php';
require_once __DIR__ . '/../src/Cyron/Database/Builder.php';

use Cyron\Database\Builder;

$deprecations = [];
set_error_handler(static function (int $severity, string $message) use (&$deprecations): bool {
    if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
        $deprecations[] = $message;
        return true;
    }
    return false;
});

try {
    $builder = (new Builder('login_attempts'))->where('key', '=', 'fingerprint');
} finally {
    restore_error_handler();
}

if ($deprecations !== []) {
    echo "FAIL: string column was treated as a callable\n";
    exit(1);
}

$reflection = new ReflectionClass($builder);
$conditions = $reflection->getProperty('conditions');
$conditions->setAccessible(true);
$compiled = $conditions->getValue($builder)[0][0] ?? '';

if ($compiled !== '`key` = ?') {
    echo "FAIL: key column condition was not compiled correctly\n";
    exit(1);
}

echo "PASS: string column names are not invoked as callables\n";
