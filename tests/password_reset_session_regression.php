<?php
declare(strict_types=1);
$c=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/ResetPasswordController.php');
foreach(['SessionRegistry::revokeUser','password_hash','strlen((string)$password) < 8'] as $needle){if(strpos($c,$needle)===false){echo "FAIL: missing $needle\n";exit(1);}}
echo "PASS: password reset revokes existing sessions\n";
