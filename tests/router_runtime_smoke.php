<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$script=<<<'PHP'
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH.'/app');
require APP_PATH.'/Core/Varaibles.php';
require APP_PATH.'/autoload.php';
require APP_PATH.'/router.php';
use AppRoute;
Route::get('/__smoke', fn() => 'ok')->name('smoke.route');
Route::prefix('api')->group(function(){ Route::get('/health', fn() => 'healthy')->name('api.health'); });
$routes=Route::getRoutes(); $named=Route::getNamedRoutes();
if(count($routes)!==2 || ($named['smoke.route']??null)!=='/__smoke' || ($named['api.health']??null)!=='/api/health'){exit(2);}
echo 'ROUTES_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_routes_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'ROUTES_OK')===false){echo "FAIL: router registration smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: router registers named and grouped routes at runtime\n";
