<?php
declare(strict_types=1);

/*
 * Runtime smoke test for all application models.
 * The legacy autoloader recursively loads app/Models, so after bootstrap the
 * target model is already available. Loading the same file again caused false
 * "Cannot redeclare class" failures; therefore we only verify class existence.
 */
$root=dirname(__DIR__);
$models=glob($root.'/app/Models/*.php');
$failed=[];

foreach($models as $file){
    $relative=str_replace($root.DIRECTORY_SEPARATOR,'',$file);
    $class='App\\Models\\'.basename($file,'.php');
    $script="<?php\n"
        .'define("APP_PATH", '.var_export($root.'/app',true).");\n"
        .'require '.var_export($root.'/app/database/Model.php',true).";\n"
        .'require '.var_export($root.'/app/autoload.php',true).";\n"
        .'if (!class_exists('.var_export($class,true).')) { fwrite(STDERR, "Missing model class"); exit(1); }'."\n"
        .'echo "OK";'."\n";
    $tmp=tempnam(sys_get_temp_dir(),'cyron_model_').'.php';
    file_put_contents($tmp,$script);
    $command=escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1';
    exec($command,$output,$code);
    @unlink($tmp);
    if($code!==0){
        $failed[]=[$relative,implode("\n",$output)];
    }
    $output=[];
}

if($failed){
    foreach($failed as [$file,$error]){
        echo "FAIL: $file\n$error\n";
    }
    exit(1);
}
echo 'PASS: '.count($models)." application models load successfully\n";
