<?php
declare(strict_types=1);
$c=file_get_contents(__DIR__.'/../app/Http/Controllers/Auth/RegisterController.php');
foreach(['AuthenticationPipeline::succeeded','Auth::markLogin','session_id()','user.dashboard'] as $needle){if(strpos($c,$needle)===false){echo "FAIL: missing $needle\n";exit(1);}}
if(strpos($c,"route('dashboard')")!==false){echo "FAIL: obsolete dashboard route remains\n";exit(1);}
echo "PASS: registration joins the unified identity pipeline\n";
