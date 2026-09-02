<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$entry=$root.'/zeno.php';
$php=escapeshellarg(PHP_BINARY);
$cases=[
    '' => ['CYRON PHP FRAMEWORK',0],
    'help' => ['Available Commands',0],
    'definitely:not:a:command' => ['Command \'definitely:not:a:command\' not found.',0],
];
foreach($cases as $args=>[$needle,$expected]){
    $cmd=$php.' '.escapeshellarg($entry).($args!==''?' '.$args:'').' 2>&1';
    exec($cmd,$out,$code);
    $text=implode("\n",$out);$out=[];
    if($code!==$expected || strpos($text,$needle)===false){
        echo "FAIL: CLI smoke for [$args] exit=$code\n$text\n";exit(1);
    }
}
echo "PASS: CLI command discovery/help/invalid-command smoke tests succeed\n";
