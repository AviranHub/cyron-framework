<?php
declare(strict_types=1);

/*
 * End-to-end HTTP smoke: boot the real public entrypoint and verify that the
 * application can load the real bootstrap, routes and middleware stack.
 */
$root=dirname(__DIR__);
$script=<<<'PHP'
$_SERVER['REQUEST_METHOD']='GET';
$_SERVER['REQUEST_URI']='/raw';
$_SERVER['SCRIPT_NAME']='/index.php';
$_SERVER['SCRIPT_FILENAME']=__DIR__.'/public/index.php';
ob_start();
require __DIR__.'/public/index.php';
$out=ob_get_clean();
if(strpos($out,'Raw output - framework is working!')===false){
    fwrite(STDERR,"unexpected application output:\n".$out);
    exit(2);
}
echo 'E2E_HTTP_OK';
PHP;
$tmp=tempnam(sys_get_temp_dir(),'cyron_e2e_').'.php';
file_put_contents($tmp,str_replace('__DIR__',$root,$script));
exec(escapeshellarg(PHP_BINARY).' -d display_errors=1 '.escapeshellarg($tmp).' 2>&1',$out,$code);
@unlink($tmp);
$text=implode("\n",$out);
if($code!==0 || strpos($text,'E2E_HTTP_OK')===false){
    echo "FAIL: end-to-end HTTP application smoke failed (exit=$code)\n$text\n";
    exit(1);
}
echo "PASS: real public entrypoint boots and serves a real application route\n";
