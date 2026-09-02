<?php
namespace App\Core\Log\drivers;

class FileDriver implements DriverInterface
{
    protected string $path;
    protected array $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    protected function write(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logLine = "[{$date}] {$level}: {$message}{$contextStr}" . PHP_EOL;

        $file = $this->path . '/' . date('Y-m-d') . '.log';
        file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
    }

    public function emergency(string $message, array $context = []): void { $this->write('EMERGENCY', $message, $context); }
    public function alert(string $message, array $context = []): void { $this->write('ALERT', $message, $context); }
    public function critical(string $message, array $context = []): void { $this->write('CRITICAL', $message, $context); }
    public function error(string $message, array $context = []): void { $this->write('ERROR', $message, $context); }
    public function warning(string $message, array $context = []): void { $this->write('WARNING', $message, $context); }
    public function notice(string $message, array $context = []): void { $this->write('NOTICE', $message, $context); }
    public function info(string $message, array $context = []): void { $this->write('INFO', $message, $context); }
    public function debug(string $message, array $context = []): void { $this->write('DEBUG', $message, $context); }
    public function log(string $level, string $message, array $context = []): void { $this->write(strtoupper($level), $message, $context); }
}