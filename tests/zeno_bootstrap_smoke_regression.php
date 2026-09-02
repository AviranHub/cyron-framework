<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$entry=$root.'/zeno.php';
$required=['app/autoload.php','app/database/Model.php','cli/Console.php','cli/Input.php'];
if(!is_file($entry)){echo "FAIL: zeno.php missing\n";exit(1);}
$code=file_get_contents($entry);
foreach($required as $path){if(strpos($code,$path)===false){echo "FAIL: CLI bootstrap missing $path\n";exit(1);}}
echo "PASS: zeno CLI bootstrap contains core runtime dependencies\n";
