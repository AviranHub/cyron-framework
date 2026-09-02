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

Route::get('/__missing/{id}', function($id){ return 'hit'; });
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/__not-found';
ob_start();Route::run();$out=ob_get_clean();
if(http_response_code()!==404){fwrite(STDERR,'missing route did not return 404');exit(2);}

Route::fallback(fn()=>'fallback-ok');
$_SERVER['REQUEST_URI']='/__still-missing';
http_response_code(200);
ob_start();Route::run();$out=ob_get_clean();
if(http_response_code()!==404 || $out!=='fallback-ok'){fwrite(STDERR,'fallback failed: '.http_response_code().' '.$out);exit(3);}
echo 'ERROR_404_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_404_').'.php';file_put_contents($tmp,$script);
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'ERROR_404_OK')===false){echo "FAIL: 404/fallback runtime smoke failed (exit=$code)\n$text\n";exit(1);}
echo "PASS: unmatched routes return 404 and fallback handlers execute at runtime\n";
