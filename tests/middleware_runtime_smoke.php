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

class SmokeMiddleware {
    public static array $events=[];
    public function handle($request,$next){
        self::$events[]='before';
        $response=$next($request);
        self::$events[]='after';
        return '['.$response.']';
    }
}
Route::globalMiddleware(SmokeMiddleware::class);
Route::get('/__middleware/{id}',fn($id)=>'action:'.$id);
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/__middleware/9';
ob_start();Route::run();$out=ob_get_clean();
if($out!=='[action:9]' || SmokeMiddleware::$events!==['before','after']){
 fwrite(STDERR,'middleware pipeline failed: '.$out.' / '.json_encode(SmokeMiddleware::$events));exit(2);
}
echo 'MIDDLEWARE_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_mw_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'MIDDLEWARE_OK')===false){echo "FAIL: middleware runtime smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: middleware executes before/after action and wraps response at runtime\n";
