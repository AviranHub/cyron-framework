<?php
declare(strict_types=1);
$otp=file_get_contents(__DIR__.'/../app/Core/Authentication/PhoneVerification.php');
$dash=file_get_contents(__DIR__.'/../app/Http/Controllers/User/DashboardController.php');
foreach(['random_int','hash(\'sha256\', $code)','codeHash'] as $n){if(strpos($otp,$n)===false){echo "FAIL: OTP hardening missing $n\n";exit(1);}}
foreach(['min:8','SessionRegistry::revokeUser'] as $n){if(strpos($dash,$n)===false){echo "FAIL: password change hardening missing $n\n";exit(1);}}
echo "PASS: OTP and change-password hardening is integrated\n";
