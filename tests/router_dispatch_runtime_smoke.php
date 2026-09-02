<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$script=<<<'PHP'
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH.'/app');
require APP_PATH.'/Core/Varaibles.php';
require APP_PATH.'/autoload.php';
require APP_PATH.'/router.php';
use App\Route;

Route::get('/__dispatch/{id}', fn($id) => 'closure:'.$id)->name('dispatch.closure');

$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/__dispatch/42';
ob_start();
Route::run();
$out=ob_get_clean();
if($out!=='closure:42'){fwrite(STDERR,'closure dispatch failed: '.$out);exit(2);}

class CyronDispatchSmokeController {
    public function show($id){ return 'controller:'.$id; }
}
Route::get('/__controller/{id}', [CyronDispatchSmokeController::class,'show'])->name('dispatch.controller');
$_SERVER['REQUEST_URI']='/__controller/7';
ob_start();
Route::run();
$out=ob_get_clean();
if($out!=='controller:7'){fwrite(STDERR,'controller dispatch failed: '.$out);exit(3);}
echo 'DISPATCH_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_dispatch_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'DISPATCH_OK')===false){echo "FAIL: route dispatch smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: closure and controller routes dispatch with parameters at runtime\n";
