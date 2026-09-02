<?php
declare(strict_types=1);

$root=dirname(__DIR__);
$files=[
    $root.'/app/database/Db.php',
    $root.'/app/database/Model.php',
];

foreach($files as $file){
    $code=file_get_contents($file);
    if($code===false){echo "FAIL: cannot read $file\n";exit(1);}
    if(strpos($code,'Database connection failed.')!==false && !str_contains($code,'RuntimeException')){
        echo "FAIL: database errors must be explicit RuntimeException\n";exit(1);
    }
}

$model=file_get_contents($root.'/app/database/Model.php');
foreach(['public static function find($id)','public static function create($data)','public function save()','public function update($data)','public function delete()'] as $needle){
    if(strpos($model,$needle)===false){echo "FAIL: Model lifecycle method missing: $needle\n";exit(1);}
}

echo "PASS: database/model lifecycle surface is present for integration runtime\n";
