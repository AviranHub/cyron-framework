<?php
namespace App\Core\Exceptions;

use App\Core\Log\LogManager;
use App\Core\Exceptions\HttpException;

class Handler
{
    protected static bool $debug = false;

    public static function setDebug(bool $debug): void
    {
        self::$debug = $debug;
    }

    public static function handle(\Throwable $e): void
    {
        LogManager::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // بررسی خطاهای HTTP خاص
        if ($e instanceof HttpException || method_exists($e, 'getStatusCode')) {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            self::renderHttpError($statusCode);
            return;
        }

        if (self::$debug) {
            self::renderDebug($e);
        } else {
            self::renderHttpError(500);
        }
    }

    protected static function renderHttpError(int $code): void
    {
        $viewPath = "errors/{$code}";
        $fallback = 'errors/500';
        
        http_response_code($code);
        
        if (function_exists('view') && view_exists($viewPath)) {
            echo view($viewPath);
        } elseif (function_exists('view') && view_exists($fallback)) {
            echo view($fallback);
        } else {
            $messages = [
                404 => 'Page Not Found',
                403 => 'Forbidden',
                419 => 'Session Expired',
                500 => 'Server Error',
            ];
            echo "<h1>{$code} - " . ($messages[$code] ?? 'Error') . "</h1>";
        }
        exit(1);
    }

    protected static function renderDebug(\Throwable $e): void
    {
        http_response_code(500);
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $line = (int) $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Debug Error</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 2rem; }
        .error-box { background: #2d2d2d; border-left: 4px solid #f48771; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .message { font-size: 1.1rem; color: #f48771; }
        .file-line { color: #569cd6; margin-top: 0.5rem; }
        .trace { background: #252526; padding: 1rem; overflow-x: auto; font-size: 0.8rem; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="message"><strong>Error:</strong> {$message}</div>
        <div class="file-line">{$file}:{$line}</div>
    </div>
    <div class="trace"><pre>{$trace}</pre></div>
</body>
</html>
HTML;
        exit(1);
    }
}