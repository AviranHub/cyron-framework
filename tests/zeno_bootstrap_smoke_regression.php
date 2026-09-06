<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$entry=$root.'/zeno';
$required=['app/autoload.php','src/Cyron/Database/Model.php','src/Cyron/Console/Console.php','src/Cyron/Console/Input.php'];
if(!is_file($entry)){echo "FAIL: zeno entrypoint missing\n";exit(1);}
$code=file_get_contents($entry);
foreach($required as $path){if(strpos($code,$path)===false){echo "FAIL: CLI bootstrap missing $path\n";exit(1);}}
echo "PASS: zeno CLI bootstrap contains core runtime dependencies\n";
