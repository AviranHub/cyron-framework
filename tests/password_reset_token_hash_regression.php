<?php
declare(strict_types=1);

$forgot=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/ForgotPasswordController.php');
$reset=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/ResetPasswordController.php');

$hashStatement='$tokenHash = hash(\'sha256\', $token)';

if(strpos($forgot,$hashStatement)===false){echo "FAIL: reset token not hashed before storage\n";exit(1);}
if(strpos($reset,$hashStatement)===false){echo "FAIL: reset token not hashed before lookup\n";exit(1);}
if(strpos($forgot,'bind_param(\'ss\', $email, $tokenHash)')===false){echo "FAIL: raw reset token stored\n";exit(1);}

echo "PASS: password reset tokens are stored and verified as hashes\n";
