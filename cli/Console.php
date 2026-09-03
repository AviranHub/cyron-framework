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

            'dev:seed' => 'DevSeedCommand',
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
            if (!$firstGroup) echo "\n";
            $firstGroup = false;
            echo Colors::brightCyan("  ┌─ ") . Colors::brightCyan($category) . "\n";
            $total = count($commands);
            $index = 0;
            foreach ($commands as $command => $class) {
                $index++;
                $prefix = ($index === $total) ? "  └─" : "  ├─";
                $desc = $this->getCommandDescription($class);
                echo Colors::brightCyan($prefix) . "    " . Colors::brightGreen(str_pad($command, 24)) . " " . Colors::dim($desc) . "\n";
            }
        }
        echo "\n";
        $this->showExamples();
        $this->showFooter();
    }

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
            if (str_starts_with($command, 'make:')) $groups['Make Commands'][$command] = $class;
            elseif (str_starts_with($command, 'migrate')) $groups['Database Commands'][$command] = $class;
            elseif (str_starts_with($command, 'route:') || str_starts_with($command, 'dev:')) $groups['Development Commands'][$command] = $class;
            elseif (str_starts_with($command, 'auth:')) $groups['Auth Commands'][$command] = $class;
            elseif (str_starts_with($command, 'admin:')) $groups['Admin Commands'][$command] = $class;
            elseif (str_starts_with($command, 'plugin:')) $groups['Plugin Commands'][$command] = $class;
            elseif (in_array($command, ['fake', 'run', 'storage:link', 'cache:clear', 'key:generate'])) $groups['Other Commands'][$command] = $class;
            else $groups['Other Commands'][$command] = $class;
        }
        return array_filter($groups);
    }

    protected function getCommandDescription($className)
    {
        if (method_exists($className, 'getDescription')) return $className::getDescription();
        return '';
    }

    protected function showHeader()
    {
        echo "\n";
        echo Colors::red100("       ██████╗██╗   ██╗██████╗  ██████╗ ███╗   ██╗            ") . "\n";
        echo Colors::red200("      ██╔════╝╚██╗ ██╔╝██╔══██╗██╔═══██╗████╗  ██║            ") . "\n";
        echo Colors::red300("      ██║      ╚████╔╝ ██████╔╝██║   ██║██╔██╗ ██║            ") . "\n";
        echo Colors::red400("      ██║       ╚██╔╝  ██╔══██╗██║   ██║██║╚██╗██║            ") . "\n";
        echo Colors::red500("      ╚██████╗   ██║   ██║  ██║╚██████╔╝██║ ╚████║            ") . "\n";
        echo Colors::red600("       ╚═════╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═══╝            ") . "\n\n";
        echo Colors::red900("  ╔═══════════════════════════════════════════════════════╗") . "\n";
        echo Colors::red900("  ║") . Colors::red400("          CYRON PHP FRAMEWORK - Power in Code          ") . Colors::red900("║") . "\n";
        echo Colors::red900("  ║") . "         Lightweight • Fast • Secure • Modern          " . Colors::red900("║") . "\n";
        echo Colors::red900("  ║") . Colors::green400("                   Created by: Aviran                  ") . Colors::red900("║") . "\n";
        echo Colors::red900("  ╚═══════════════════════════════════════════════════════╝") . "\n\n";
    }

    protected function showExamples()
    {
        echo Colors::brightCyan("  ┌─ Examples:\n");
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno make:controller HomeController") . "\n";
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno make:model Product") . "\n";
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno migrate") . "\n";
        echo Colors::brightCyan("  ├─") . "    " . Colors::white("php zeno dev:seed") . "\n";
        echo Colors::brightCyan("  └─") . "    " . Colors::white("php zeno run --port=8080") . "\n\n";
    }

    protected function showFooter()
    {
        echo Colors::brightBlue("  ╔══════════════════════════════════════════════════════════╗\n");
        echo Colors::brightBlue("  ║") . Colors::blue300(" For more information, visit: https://framework.com/docs  ") . Colors::brightBlue("║\n");
        echo Colors::brightBlue("  ╚══════════════════════════════════════════════════════════╝\n\n");
    }
}
