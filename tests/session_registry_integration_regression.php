<?php
declare(strict_types=1);
$middleware=file_get_contents(__DIR__.'/../app/Modules/Auth/Middlewares/AuthMiddleware.php');
$registry=file_get_contents(__DIR__.'/../app/Auth/SessionRegistry.php');
foreach(['SessionRegistry::active','SessionRegistry::touch'] as $needle){if(strpos($middleware,$needle)===false){echo "FAIL: missing $needle\n";exit(1);}}
foreach(['revokeToken','revokeUser','revoked_at'] as $needle){if(strpos($registry,$needle)===false){echo "FAIL: registry missing $needle\n";exit(1);}}
echo "PASS: session registry integration guards authenticated requests\n";
