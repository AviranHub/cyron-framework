<?php

declare(strict_types=1);

$autoload = file_get_contents(__DIR__ . '/../app/autoload.php');
if (substr_count($autoload, 'require_once $file;') < 2) {
    echo "FAIL: manual App/Cyron autoloaders are not idempotent\n";
    exit(1);
}

$audit = file_get_contents(__DIR__ . '/../src/Cyron/Audit.php');
if (substr_count($audit, 'class Audit') !== 1) {
    echo "FAIL: Audit class definition is duplicated\n";
    exit(1);
}

echo "PASS: Audit class autoloading is idempotent\n";