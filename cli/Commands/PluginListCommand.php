<?php
// cli/PluginListCommand.php

require_once __DIR__ . '/../Colors.php';

class PluginListCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "List all plugins with their status";
    }

    public function execute()
    {
        require_once BASE_PATH . '/app/Core/Plugin/PluginManager.php';
        \App\Core\Plugin\PluginManager::setPath(APP_PATH . '/Plugins');
        \App\Core\Plugin\PluginManager::discover();

        $allPlugins = \App\Core\Plugin\PluginManager::all();
        if (empty($allPlugins)) {
            echo Colors::yellow("No plugins found.\n");
            return;
        }

        // عرض ثابت ستون‌ها (می‌توانید عددها را تغییر دهید)
        $nameColWidth = 20;
        $statusColWidth = 12;
        $versionColWidth = 10;
        $namespaceColWidth = 28;
        $descColWidth = 40;

        // محاسبه عرض کل جدول
        $totalWidth = $nameColWidth + $statusColWidth + $versionColWidth + $namespaceColWidth + $descColWidth + 11; // +11 برای جداکننده‌های " | "

        // هدر
        echo "\n";
        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");
        
        $header = sprintf(
            "  %-{$nameColWidth}s | %-{$statusColWidth}s | %-{$versionColWidth}s | %-{$namespaceColWidth}s | %-{$descColWidth}s",
            Colors::brightWhite("Name"),
            Colors::brightWhite("Status"),
            Colors::brightWhite("Version"),
            Colors::brightWhite("Namespace"),
            Colors::brightWhite("Description")
        );
        echo $header . "\n";
        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");

        // داده‌ها
        foreach ($allPlugins as $name => $info) {
            $statusText = \App\Core\Plugin\PluginManager::isActive($name) ? '● Active' : '○ Inactive';
            $statusDisplay = \App\Core\Plugin\PluginManager::isActive($name) 
                ? Colors::green($statusText) 
                : Colors::red($statusText);
            
            $version = $info['version'] ?? '1.0.0';
            $namespace = "Plugins\\{$name}";
            $description = $info['description'] ?? '';

            // برش اگر太长
            if (strlen($description) > $descColWidth - 3) {
                $description = substr($description, 0, $descColWidth - 6) . '...';
            }

            // محاسبه طول واقعی بدون کدهای رنگی برای padding
            $statusLen = $this->visibleLength($statusDisplay);
            $statusPadding = $statusColWidth - $statusLen;
            if ($statusPadding < 0) $statusPadding = 0;

            echo sprintf(
                "  %-{$nameColWidth}s | %s%s | %-{$versionColWidth}s | %-{$namespaceColWidth}s | %-{$descColWidth}s\n",
                $name,
                $statusDisplay,
                str_repeat(' ', $statusPadding),
                $version,
                $namespace,
                $description
            );
        }

        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");
        echo Colors::brightGreen("  Total plugins: " . count($allPlugins)) . "\n\n";
    }

    private function visibleLength($str)
    {
        return strlen(preg_replace('/\x1b\[[0-9;]*m/', '', (string)$str));
    }
}