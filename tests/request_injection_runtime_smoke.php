<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$script=<<<'PHP'
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH.'/app');
require APP_PATH.'/Core/Env.php';
require APP_PATH.'/autoload.php';
require APP_PATH.'/router.php';
use App\Route;
use App\Request;

class RequestInjectionSmokeController {
    public function show(Request $request, $id) {
        return ($request instanceof Request ? 'request' : 'missing').':'.$id;
    }
}
Route::get('/__request/{id}', [RequestInjectionSmokeController::class,'show']);
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/__request/55';
ob_start();Route::run();$out=ob_get_clean();
if($out!=='request:55'){fwrite(STDERR,'request injection failed: '.$out);exit(2);}
echo 'REQUEST_INJECTION_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_req_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'REQUEST_INJECTION_OK')===false){echo "FAIL: request injection runtime smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: controller Request injection and route parameters work at runtime\n";
