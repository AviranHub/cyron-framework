<?php

require_once __DIR__ . '/Colors.php';

class Console
{
    protected $input;
    protected $commands = [];

    public function __construct()
    {
        Colors::enable();
        $this->input = new Input();
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->commands = [
            'make:controller' => 'MakeController',
            'make:model' => 'MakeModel',
            'make:migration' => 'MakeMigration',
            'make:middleware' => 'MakeMiddleware',
            'make:fake' => 'MakeFakeCommand',
            'make:component' => 'MakeComponentCommand',
            'make:action' => 'MakeActionCommand',
            'make:command' => 'MakeCommand',
            'fake' => 'FakeCommand',
            'migrate' => 'MigrateCommand',
            'migrate:rollback' => 'MigrateRollbackCommand',
            'migrate:fresh' => 'FreshCommand',
            'route:list' => 'RouteListCommand',
            'plugin:list' => 'PluginListCommand',
            'admin:publish' => 'AdminPublishCommand',
            'admin:unpublish' => 'AdminUnpublishCommand',
            'auth:publish' => 'AuthPublishCommand',
            'auth:unpublish' => 'AuthUnpublishCommand',
            'make:plugin' => 'MakePluginCommand',
            'plugin:publish' => 'PluginPublishCommand',
            'plugin:unpublish' => 'PluginUnpublishCommand',
            'plugin:install' => 'PluginInstallCommand',
            'run' => 'RunCommand',
            'storage:link' => 'StorageLinkCommand',
            'sitemap:generate' => 'SitemapGenerateCommand',
            'cache:clear' => 'CacheClearCommand',
            'key:generate' => 'KeyGenerateCommand',
        ];
    }

    public function run()
    {
        $commandName = $this->input->getCommand();

        if ($commandName === 'list' || $commandName === '' || $commandName === 'help') {
            $this->showHelp();
            return;
        }

        if (!isset($this->commands[$commandName])) {
            echo Colors::error("Command '{$commandName}' not found.") . "\n\n";
            $this->showHelp();
            return;
        }

        $commandClass = $this->commands[$commandName];

        if (class_exists($commandClass)) {
            $instance = new $commandClass($this->input);
            $instance->execute();
        } else {
            echo Colors::error("Command '{$commandName}' is not implemented yet.") . "\n";
        }
    }

    protected function showHelp()
    {
        $this->showHeader();

        echo Colors::brightYellow("\n  Usage:") . "\n";
        echo Colors::white("    php zeno [command] [options]") . "\n\n";

        echo Colors::brightYellow("  Available Commands:") . "\n\n";

        $groups = $this->groupCommands();
        $firstGroup = true;

        foreach ($groups as $category => $commands) {
            if (!$firstGroup) {
                echo "\n";
            }
            $firstGroup = false;

            // خط عنوان دسته با ┌─
            echo Colors::brightCyan("  ┌─ ") . Colors::brightCyan($category) . "\n";

            $total = count($commands);
            $index = 0;
            foreach ($commands as $command => $class) {
                $index++;
                $prefix = ($index === $total) ? "  └─" : "  ├─";
                $desc = $this->getCommandDescription($class);
                $line = Colors::brightCyan($prefix) . "    " . Colors::brightGreen(str_pad($command, 24)) . " " . Colors::dim($desc);
                echo $line . "\n";
            }
        }

        echo "\n";
        $this->showExamples();
        $this->showFooter();
    }

    /**
     * دسته‌بندی خودکار کامندها بر اساس پیشوند
     */
    protected function groupCommands()
    {
        $groups = [
            'Make Commands' => [],
            'Database Commands' => [],
            'Development Commands' => [],
            'Auth Commands' => [],
            'Admin Commands' => [],
            'Plugin Commands' => [],
            'Other Commands' => [],
        ];

        foreach ($this->commands as $command => $class) {
            if (str_starts_with($command, 'make:')) {
                $groups['Make Commands'][$command] = $class;
            } elseif (str_starts_with($command, 'migrate')) {
                $groups['Database Commands'][$command] = $class;
            } elseif (str_starts_with($command, 'route:')) {
                $groups['Development Commands'][$command] = $class;
            } elseif (str_starts_with($command, 'auth:')) {
                $groups['Auth Commands'][$command] = $class;
            } elseif (str_starts_with($command, 'admin:')) {
                $groups['Admin Commands'][$command] = $class;
            } elseif (str_starts_with($command, 'plugin:')) {
                $groups['Plugin Commands'][$command] = $class;
            } elseif (in_array($command, ['fake', 'run', 'storage:link', 'cache:clear', 'key:generate'])) {
                $groups['Other Commands'][$command] = $class;
            } else {
                $groups['Other Commands'][$command] = $class;
            }
        }

        // حذف دسته‌های خالی
        return array_filter($groups);
    }

    /**
     * دریافت توضیحات کامند از متد getDescription کلاس (در صورت وجود)
     */
    protected function getCommandDescription($className)
    {
        if (method_exists($className, 'getDescription')) {
            return $className::getDescription();
        }
        return '';
    }
    // protected function showHelp()
    // {
    //     $this->showHeader();

    //     echo Colors::brightYellow("\n  Usage:") . "\n";
    //     echo Colors::white("    php zeno [command] [options]") . "\n\n";

    //     echo Colors::brightYellow("  Available Commands:") . "\n\n";

    //     // Make Commands
    //     echo Colors::brightCyan("  ┌─ make commands:\n");
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("make:controller") . "     " . Colors::dim("Create a new controller class") . "\n";
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("make:model") . "          " . Colors::dim("Create a new model class") . "\n";
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("make:migration") . "      " . Colors::dim("Create a new migration file") . "\n";
    //     echo Colors::brightCyan("  └─") . "    " . Colors::brightGreen("make:middleware") . "     " . Colors::dim("Create a new middleware class") . "\n";
    //     echo "\n";

    //     // Database Commands
    //     echo Colors::brightCyan("  ┌─ database commands:\n");
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("migrate") . "             " . Colors::dim("Run the database migrations") . "\n";
    //     echo Colors::brightCyan("  └─") . "    " . Colors::brightGreen("migrate:rollback") . "    " . Colors::dim("Rollback the last database migration") . "\n";
    //     echo "\n";

    //     // Development Commands
    //     echo Colors::brightCyan("  ┌─ development commands:\n");
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("run") . "               " . Colors::dim("Start the development Server") . "\n";
    //     echo Colors::brightCyan("  └─") . "    " . Colors::brightGreen("route:list") . "          " . Colors::dim("List all registered routes") . "\n";
    //     echo "\n";

    //     // Other Commands
    //     echo Colors::brightCyan("  ┌─ other commands:\n");
    //     echo Colors::brightCyan("  ├─") . "    " . Colors::brightGreen("cache:clear") . "         " . Colors::dim("Clear all cached data") . "\n";
    //     echo Colors::brightCyan("  └─") . "    " . Colors::brightGreen("key:generate") . "        " . Colors::dim("Generate a new application key") . "\n";
    //     echo "\n";

    //     // Examples
    //     $this->showExamples();

    //     $this->showFooter();
    // }

    protected function showHeader()
    {
        echo "\n";
        // echo Colors::red600("    ╔══════════════════════════════════════════════════════════════╗") . "\n";
        echo Colors::red50("                                                              ") . "\n";

        // خط اول لوگو
        echo Colors::red100("       ██████╗██╗   ██╗██████╗  ██████╗ ███╗   ██╗            ") . "\n";

        // خط دوم لوگو
        echo Colors::red200("      ██╔════╝╚██╗ ██╔╝██╔══██╗██╔═══██╗████╗  ██║            ") . "\n";

        // خط سوم لوگو
        echo Colors::red300("      ██║      ╚████╔╝ ██████╔╝██║   ██║██╔██╗ ██║            ") . "\n";

        // خط چهارم لوگو
        echo Colors::red400("      ██║       ╚██╔╝  ██╔══██╗██║   ██║██║╚██╗██║            ") . "\n";

        // خط پنجم لوگو
        echo Colors::red500("      ╚██████╗   ██║   ██║  ██║╚██████╔╝██║ ╚████║            ") . "\n";

        // خط ششم لوگو
        echo Colors::red600("       ╚═════╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═══╝            ") . "\n";

        // echo Colors::red600("    ║") . Colors::brightWhite("                                                              ") . Colors::brightYellow("║") . "\n";
        // echo Colors::red600("    ╠══════════════════════════════════════════════════════════════╣") . "\n";
        echo Colors::red600("                                                                    ") . "\n";
        echo Colors::red900("  ╔═══════════════════════════════════════════════════════╗") . "\n";

        // عنوان اصلی (گرادینت با ترکیب رنگ‌های ساده)
        $title = "          CYRON PHP FRAMEWORK - Power in Code          ";
        echo Colors::red900("  ║") . Colors::red400($title) . Colors::red900("║") . "\n";

        // خط دوم
        $subtitle = "         Lightweight • Fast • Secure • Modern          ";

        echo Colors::red900("  ║") . $subtitle . Colors::red900("║") . "\n";

        // خط سوم
        $author = "                   Created by: Aviran                  ";
        echo Colors::red900("  ║") . Colors::green400($author) . Colors::red900("║") . "\n";

        echo Colors::red900("  ╚═══════════════════════════════════════════════════════╝") . "\n";
        echo "\n";
    }

    protected function showExamples()
    {
        echo Colors::brightCyan("  ┌─ Examples:\n");
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno make:controller HomeController") . "\n";
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno make:model Product") . "\n";
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno migrate") . "\n";
        echo Colors::brightCyan("  └─") . "    " . Colors::white("php zeno run --port=8080") . "\n";
        echo "\n";
    }

    protected function showFooter()
    {
        echo Colors::brightBlue("  ╔══════════════════════════════════════════════════════════╗\n");
        echo Colors::brightBlue("  ║") . Colors::blue300(" For more information, visit: https://framework.com/docs  ") . Colors::brightBlue("║\n");
        echo Colors::brightBlue("  ╚══════════════════════════════════════════════════════════╝\n");
        echo "\n";
    }
}
