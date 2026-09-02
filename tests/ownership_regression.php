<?php

declare(strict_types=1);

require_once __DIR__.'/../app/Core/Authorization/Ownership.php';

use App\Core\Authorization\Ownership;

$owner = (object) ['id' => 10];
$other = (object) ['id' => 11];
$resource = (object) ['id' => 5, 'user_id' => 10];

if (!Ownership::owns($resource, $owner)) {
    echo "FAIL: owner was denied\n";
    exit(1);
}

if (Ownership::owns($resource, $other)) {
    echo "FAIL: non-owner was allowed\n";
    exit(1);
}

if (Ownership::owns((object) ['id' => 6], $owner)) {
    echo "FAIL: resource without owner key was allowed\n";
    exit(1);
}

echo "PASS: ownership checks use the resolved resource owner\n";
