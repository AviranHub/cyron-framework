<?php
declare(strict_types=1);
$c=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/LoginController.php');
$need=['LoginManager::attempt','LoginManager::completeTwoFactor','LoginManager::logout','showTwoFactorForm','verifyTwoFactor'];
foreach($need as $n){if(strpos($c,$n)===false){echo "FAIL: missing $n\n";exit(1);}}
if(strpos($c,'Auth::attempt')!==false){echo "FAIL: legacy Auth::attempt bypass remains\n";exit(1);}
echo "PASS: official login controller uses unified authentication flow\n";
