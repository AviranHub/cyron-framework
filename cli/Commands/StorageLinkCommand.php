<?php
require_once __DIR__ . '/../Colors.php';

class StorageLinkCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a symbolic link from public/storage to storage/public";
    }

    public function execute()
    {
        $target = BASE_PATH . '/storage/public';
        $link = PUBLIC_PATH . '/storage';

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
            echo Colors::gray300("  Created storage/public directory.\n");
        }

        // اگر لینک قبلاً وجود دارد
        if (file_exists($link)) {
            echo Colors::yellow("  Symbolic link already exists.\n");
            return;
        }

        // ایجاد لینک symbolic (ویندوز نیاز به Administrator دارد)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // ویندوز
            $command = "mklink /J \"{$link}\" \"{$target}\"";
            $output = shell_exec($command . ' 2>&1');
            if (strpos($output, 'created') !== false || strpos($output, 'created') !== false) {
                echo Colors::green("  ✓ Symbolic link created successfully.\n");
            } else {
                echo Colors::red("  Error: " . $output . "\n");
                echo Colors::yellow("  Tip: Run command prompt as Administrator.\n");
            }
        } else {
            // لینوکس/مک
            if (symlink($target, $link)) {
                echo Colors::green("  ✓ Symbolic link created successfully.\n");
            } else {
                echo Colors::red("  Error: Failed to create symbolic link.\n");
            }
        }
    }
}