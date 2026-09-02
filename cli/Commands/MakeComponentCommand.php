<?php
// cli/Commands/MakeComponentCommand.php

require_once __DIR__ . '/../Colors.php';

class MakeComponentCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new component view (and optionally register it)";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Component name (e.g., BookSlider, Alert)");
        }
        $name = ucfirst(trim($name));
        $kebab = $this->toKebabCase($name);

        $viewPath = RESOURCES_PATH . "/Views/components/{$kebab}.lady.php";
        if (file_exists($viewPath)) {
            echo Colors::error("Component already exists: {$viewPath}\n");
            return;
        }

        $dir = dirname($viewPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $stub = <<<PHP
<div class="component-{$kebab}">
    <h2>{{ \$title ?? '{$name} Component' }}</h2>
    <div class="content">
        {{ \$slot }}
    </div>
</div>
PHP;
        file_put_contents($viewPath, $stub);
        echo Colors::green("✓ Component view created: {$viewPath}\n");

        // پیشنهاد ثبت خودکار
        $register = $this->input->confirm("Do you want to register this component in ComponentManager? (y/n)", true);
        if ($register) {
            $this->registerComponent($kebab);
            echo Colors::green("✓ Component '{$kebab}' registered in ComponentManager.\n");
        }

        echo Colors::dim("\nYou can now use:\n");
        echo Colors::dim("  @component('{$kebab}', ['title' => '...'])\n");
        echo Colors::dim("    <p>Your content</p>\n");
        echo Colors::dim("  @endcomponent\n");
        echo Colors::dim("  or\n");
        echo Colors::dim("  <x-{$kebab} title=\"...\">...</x-{$kebab}>\n");
    }

    protected function toKebabCase(string $str): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $str));
    }

    protected function registerComponent(string $alias)
    {
        // فایل bootstrap.php را پیدا کرده و در آن خط ثبت را اضافه کن
        $bootstrapPath = BASE_PATH . '/app/bootstrap.php';
        if (!file_exists($bootstrapPath)) {
            echo Colors::yellow("Could not auto-register. Please manually add: component()->register('{$alias}', 'components.{$alias}');\n");
            return;
        }

        $line = "component()->register('{$alias}', 'components.{$alias}');";
        $content = file_get_contents($bootstrapPath);
        if (strpos($content, $line) === false) {
            // قبل از آخرین  یا انتهای فایل اضافه کن
            $content = preg_replace('/\?>\s*$/', '', $content);
            $content .= "\n{$line}\n?>";
            file_put_contents($bootstrapPath, $content);
        }
    }
}

