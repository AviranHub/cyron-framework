<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$script = <<<'PHP'
<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

require BASE_PATH . '/app/Core/Log/drivers/DriverInterface.php';
require BASE_PATH . '/app/Core/Log/drivers/FileDriver.php';
require BASE_PATH . '/app/Core/Log/LogManager.php';
require BASE_PATH . '/app/Core/Exceptions/HttpException.php';
require BASE_PATH . '/app/Core/Exceptions/Handler.php';

use App\Core\Exceptions\Handler;

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/debug-handler-smoke';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CyronTest';

try {
    throw new RuntimeException('Debug smoke failure');
} catch (Throwable $e) {
    Handler::setDebug(true);
    Handler::handle($e);
}
PHP;

$tmp = tempnam(sys_get_temp_dir(), 'cyron_debug_') . '.php';
file_put_contents($tmp, str_replace("define('BASE_PATH', __DIR__);", "define('BASE_PATH', " . var_export($root, true) . ");", $script));

exec(escapeshellarg(PHP_BINARY) . ' -d display_errors=1 ' . escapeshellarg($tmp) . ' 2>&1', $out, $code);
@unlink($tmp);

$html = implode("\n", $out);
$required = ['Cyron Debug', 'RuntimeException', 'Debug smoke failure', 'Source', 'Request', 'Stack Trace'];
$missing = [];

foreach ($required as $needle) {
    if (strpos($html, $needle) === false) {
        $missing[] = $needle;
    }
}

if ($code !== 1 || $missing) {
    echo "FAIL: debug handler did not render expected developer error page\n";
    echo 'exit=' . $code . "\n";
    if ($missing) {
        echo 'missing=' . implode(', ', $missing) . "\n";
    }
    exit(1);
}

echo "PASS: debug handler renders source, request context, and stack trace\n";
