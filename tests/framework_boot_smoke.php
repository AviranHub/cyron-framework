<?php
declare(strict_types=1);

$root=dirname(__DIR__);
$script="<?php\n".<<<PHP
define('BASE_PATH', %s);
define('APP_PATH', BASE_PATH.'/app');
define('PUBLIC_PATH', BASE_PATH.'/public');
define('RESOURCES_PATH', BASE_PATH.'/resources');
define('ROUTES_PATH', BASE_PATH.'/routes');
define('STORAGE_PATH', BASE_PATH.'/storage');
require APP_PATH.'/Core/Varaibles.php';
require APP_PATH.'/database/Model.php';
require APP_PATH.'/autoload.php';
echo "BOOT_OK";
PHP;
$script=sprintf($script,var_export($root,true));
$tmp=tempnam(sys_get_temp_dir(),'cyron_boot_').'.php';
file_put_contents($tmp,$script);
$command=escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1';
exec($command,$output,$code);
@unlink($tmp);
$result=trim(implode("\n",$output));
if($code!==0 || $result!=='BOOT_OK'){
 echo "FAIL: core autoload boot smoke failed\n".$result."\n";exit(1);
}
echo "PASS: core constants and autoloader boot successfully\n";
