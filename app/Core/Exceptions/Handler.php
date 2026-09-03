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
        $exception = self::escape(get_class($e));
        $message = self::escape($e->getMessage() !== '' ? $e->getMessage() : 'No exception message provided.');
        $file = self::escape(self::projectPath($e->getFile()));
        $line = (int) $e->getLine();
        $source = self::renderSourceSnippet($e->getFile(), $line);
        $trace = self::renderTrace($e);
        $request = self::renderRequestContext();

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$exception} - Cyron Debug</title>
    <style>
        :root { color-scheme: dark; --bg: #111827; --panel: #18212f; --muted: #94a3b8; --text: #e5e7eb; --border: #334155; --accent: #ef4444; --line: #243244; --code: #0b1220; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .shell { max-width: 1180px; margin: 0 auto; padding: 32px 20px 48px; }
        .hero { border: 1px solid var(--border); background: var(--panel); border-radius: 8px; overflow: hidden; }
        .bar { height: 4px; background: var(--accent); }
        .hero-body { padding: 24px; }
        .eyebrow { color: var(--muted); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        h1 { margin: 10px 0 12px; font-size: clamp(24px, 4vw, 38px); line-height: 1.15; letter-spacing: 0; overflow-wrap: anywhere; }
        .message { color: #fecaca; font-size: 16px; overflow-wrap: anywhere; }
        .location { margin-top: 18px; color: #bfdbfe; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; overflow-wrap: anywhere; }
        .grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 16px; margin-top: 16px; }
        .panel { border: 1px solid var(--border); background: var(--panel); border-radius: 8px; overflow: hidden; }
        .panel h2 { margin: 0; padding: 14px 18px; border-bottom: 1px solid var(--border); font-size: 14px; letter-spacing: 0; }
        pre { margin: 0; overflow-x: auto; background: var(--code); }
        .source-row { display: grid; grid-template-columns: 64px minmax(0, 1fr); min-width: 760px; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.55; }
        .source-row.active { background: rgba(239, 68, 68, .16); }
        .line-no { color: var(--muted); text-align: right; padding: 0 14px; user-select: none; border-right: 1px solid var(--line); }
        .line-code { padding: 0 16px; white-space: pre; }
        .trace-list { margin: 0; padding: 0; list-style: none; }
        .trace-frame { padding: 14px 18px; border-bottom: 1px solid var(--border); }
        .trace-frame:last-child { border-bottom: 0; }
        .trace-call { font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; color: #dbeafe; overflow-wrap: anywhere; }
        .trace-file { margin-top: 6px; color: var(--muted); font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 12px; overflow-wrap: anywhere; }
        .context { width: 100%; border-collapse: collapse; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; }
        .context th, .context td { padding: 10px 14px; border-bottom: 1px solid var(--border); text-align: left; vertical-align: top; }
        .context th { width: 170px; color: var(--muted); font-weight: 500; }
        .context tr:last-child th, .context tr:last-child td { border-bottom: 0; }
        @media (min-width: 980px) { .grid { grid-template-columns: minmax(0, 1.25fr) minmax(340px, .75fr); } .wide { grid-column: 1 / -1; } }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div class="bar"></div>
            <div class="hero-body">
                <div class="eyebrow">Cyron Debug</div>
                <h1>{$exception}</h1>
                <div class="message">{$message}</div>
                <div class="location">{$file}:{$line}</div>
            </div>
        </section>

        <div class="grid">
            <section class="panel">
                <h2>Source</h2>
                <pre>{$source}</pre>
            </section>
            <section class="panel">
                <h2>Request</h2>
                {$request}
            </section>
            <section class="panel wide">
                <h2>Stack Trace</h2>
                {$trace}
            </section>
        </div>
    </main>
</body>
</html>
HTML;
        exit(1);
    }

    protected static function renderSourceSnippet(string $file, int $line, int $radius = 8): string
    {
        if (!is_file($file) || !is_readable($file)) {
            return '<div class="source-row"><span class="line-no">--</span><span class="line-code">Source file is not readable.</span></div>';
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return '<div class="source-row"><span class="line-no">--</span><span class="line-code">Source file could not be loaded.</span></div>';
        }

        $start = max(1, $line - $radius);
        $end = min(count($lines), $line + $radius);
        $html = '';

        for ($i = $start; $i <= $end; $i++) {
            $active = $i === $line ? ' active' : '';
            $code = self::escape($lines[$i - 1] ?? '');
            $html .= '<div class="source-row' . $active . '"><span class="line-no">' . $i . '</span><span class="line-code">' . $code . '</span></div>';
        }

        return $html;
    }

    protected static function renderTrace(\Throwable $e): string
    {
        $frames = [];
        $frames[] = [
            'call' => 'throw ' . get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        foreach ($e->getTrace() as $frame) {
            $call = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '{main}') . '()';
            $frames[] = [
                'call' => $call,
                'file' => $frame['file'] ?? null,
                'line' => $frame['line'] ?? null,
            ];
        }

        $html = '<ol class="trace-list">';
        foreach ($frames as $index => $frame) {
            $file = $frame['file'] ? self::projectPath((string) $frame['file']) : '[internal]';
            $line = $frame['line'] ? ':' . (int) $frame['line'] : '';
            $html .= '<li class="trace-frame">';
            $html .= '<div class="trace-call">#' . $index . ' ' . self::escape($frame['call']) . '</div>';
            $html .= '<div class="trace-file">' . self::escape($file . $line) . '</div>';
            $html .= '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    protected static function renderRequestContext(): string
    {
        $rows = [
            'Method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'URL' => self::currentUrl(),
            'IP' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'User Agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'Environment' => function_exists('vars') ? (string) vars('APP_ENV') : 'unknown',
            'PHP' => PHP_VERSION,
        ];

        $html = '<table class="context"><tbody>';
        foreach ($rows as $key => $value) {
            $html .= '<tr><th>' . self::escape($key) . '</th><td>' . self::escape((string) $value) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    protected static function currentUrl(): string
    {
        if (PHP_SAPI === 'cli') {
            return 'CLI';
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    protected static function projectPath(string $path): string
    {
        if (defined('BASE_PATH')) {
            $base = rtrim(str_replace('\\', '/', BASE_PATH), '/');
            $normalized = str_replace('\\', '/', $path);
            if (str_starts_with($normalized, $base)) {
                return ltrim(substr($normalized, strlen($base)), '/');
            }
        }

        return $path;
    }

    protected static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
