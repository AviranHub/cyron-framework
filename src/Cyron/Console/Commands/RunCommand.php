<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;

class RunCommand {
    protected $input;
    
    public function __construct($input) {
        $this->input = $input;
        Colors::enable();
    }
    
    public static function getDescription() {
        return "Start PHP development server (default host: 127.0.0.1)";
    }
    
    public function execute() {
        $port = $this->input->getOption('port') ?: 8000;
        $host = $this->input->getOption('host') ?? '127.0.0.1';
        
        // اگر کاربر --host بدهد بدون مقدار، مقدار پیش‌فرض 0.0.0.0 در نظر گرفته شود
        if ($this->input->hasOption('host') && $host === true) {
            $host = '0.0.0.0';
        }
        
        echo Colors::orange300("    \n");
        echo Colors::orange300("    Starting development server on http://{$host}:{$port}\n");
        if ($host === '0.0.0.0') {
            // نمایش آدرس قابل دسترس در شبکه محلی
            $localIp = $this->getLocalIp();
            if ($localIp) {
                echo Colors::green("    Accessible on your network: http://{$localIp}:{$port}\n");
            }
        }
        echo Colors::red300("    Press Ctrl+C to stop\n");
        echo Colors::red300("    \n");
        
        $documentRoot = BASE_PATH . DIRECTORY_SEPARATOR . 'public';
        $command = sprintf(
            '%s -S %s:%s -t %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($documentRoot)
        );

        passthru($command, $exitCode);
        return $exitCode;
    }
    
    /**
     * دریافت آدرس IP محلی (برای شبکه داخلی)
     */
    protected function getLocalIp() {
        // روش ساده: اجرای دستور ipconfig (ویندوز) یا ifconfig (لینوکس/مک)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // ویندوز
            $output = shell_exec('ipconfig');
            if (preg_match('/IPv4 Address[ .]+: ([0-9.]+)/', $output, $matches)) {
                return $matches[1];
            }
        } else {
            // لینوکس/مک
            $output = shell_exec('hostname -I');
            if ($output) {
                $ips = explode(' ', trim($output));
                return $ips[0];
            }
        }
        return null;
    }
}