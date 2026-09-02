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

class ParameterMiddleware {
    public static ?string $received=null;
    public function __construct(string $ability){self::$received=$ability;}
    public function handle($request,$next){return $next($request);}
}
class BlockingMiddleware {
    public static bool $actionReached=false;
    public function handle($request,$next){return 'blocked';}
}
Route::get('/__param',fn()=>'param')->middleware(ParameterMiddleware::class.':book.edit');
Route::get('/__blocked',function(){BlockingMiddleware::$actionReached=true;return 'action';})->middleware(BlockingMiddleware::class);

$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/__param';
ob_start();Route::run();$out=ob_get_clean();
if($out!=='param' || ParameterMiddleware::$received!=='book.edit'){fwrite(STDERR,'parameter middleware failed');exit(2);}

$_SERVER['REQUEST_URI']='/__blocked';
ob_start();Route::run();$out=ob_get_clean();
if($out!=='blocked' || BlockingMiddleware::$actionReached){fwrite(STDERR,'short circuit failed');exit(3);}
echo 'MIDDLEWARE_ADVANCED_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_mw_adv_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'MIDDLEWARE_ADVANCED_OK')===false){echo "FAIL: advanced middleware smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: parameterized middleware and request short-circuit work at runtime\n";
