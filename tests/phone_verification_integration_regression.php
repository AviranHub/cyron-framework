<?php
declare(strict_types=1);
$routes=file_get_contents(__DIR__.'/../app/Modules/Auth/routes.php');
$controller=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/PhoneVerificationController.php');
foreach(['phone.verify','phone.verify.submit','phone.send'] as $needle){if(strpos($routes,$needle)===false){echo "FAIL: missing phone route $needle\n";exit(1);}}
if(strpos($controller,"route('dashboard')")!==false){echo "FAIL: obsolete dashboard route after phone verification\n";exit(1);}
if(strpos($controller,"route('user.dashboard')")===false){echo "FAIL: official dashboard route missing\n";exit(1);}
echo "PASS: phone verification flow is routed consistently\n";
