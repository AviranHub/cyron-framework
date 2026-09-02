<?php
// cli/MakePluginCommand.php

require_once __DIR__ . '/../Colors.php';

class MakePluginCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new plugin structure";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Plugin name:");
        }
        $name = ucfirst(trim($name));
        $lowerName = strtolower($name);

        // مسیر اصلی پلاگین (طبق استاندارد فریمورک)
        $pluginDir = APP_PATH . "/Plugins/{$name}";
        if (is_dir($pluginDir)) {
            echo Colors::error("Plugin '{$name}' already exists at {$pluginDir}\n");
            return;
        }

        // ایجاد پوشه‌های اصلی
        mkdir($pluginDir, 0777, true);
        mkdir($pluginDir . '/Controllers', 0777, true);
        mkdir($pluginDir . '/Models', 0777, true);
        mkdir($pluginDir . '/Views', 0777, true);
        mkdir($pluginDir . '/config', 0777, true);
        mkdir($pluginDir . '/assets', 0777, true);
        mkdir($pluginDir . '/Migrations', 0777, true);

        // ========== 1. ساخت فایل plugin.json (با publish paths خودکار شامل نام پلاگین) ==========
        $publish = [
            'controllers' => "Http/Controllers/{$name}",
            'views'       => "views/{$lowerName}",
            'routes'      => "{$lowerName}.php",
            'assets'      => "assets/{$lowerName}",
            'config'      => "{$lowerName}.php"
        ];

        $manifest = [
            'name'        => $name,
            'version'     => '1.0.0',
            'author'      => 'Cyron',
            'description' => "Description for {$name} plugin",
            'publish'     => $publish
        ];

        // وابستگی‌ها (اختیاری)
        $hasDeps = $this->input->ask("\nDoes this plugin have dependencies? (y/n): ", 'n');
        $dependencies = [];
        if (strtolower($hasDeps) === 'y') {
            while (true) {
                $depName = $this->input->ask("Dependency plugin name (empty to finish): ");
                if (empty($depName)) break;
                $depVersion = $this->input->ask("Version constraint (e.g., >=1.0.0): ", ">=1.0.0");
                $dependencies[$depName] = $depVersion;
            }
        }
        $manifest['dependencies'] = $dependencies;

        file_put_contents($pluginDir . '/plugin.json', json_encode($manifest, JSON_PRETTY_PRINT));

        // ========== 2. فایل Plugin.php (کلاس اصلی پلاگین) ==========
        $pluginClassStub = <<<PHP
<?php
namespace Plugins\\{$name};

use App\Core\Plugin\Plugin as BasePlugin;

class Plugin extends BasePlugin
{
    protected function registerHooks(): void
    {
        // مثال: ثبت یک هوک برای رندر کردن ویو
        // \$this->listen('{$lowerName}.render', function(\$data = []) {
        //     return \$this->view('index', \$data);
        // });
    }
}
PHP;
        file_put_contents($pluginDir . '/Plugin.php', $pluginClassStub);

        // ========== 3. یک کنترلر نمونه با نام پلاگین ==========
        $controllerStub = <<<PHP
<?php
namespace Plugins\\{$name}\Controllers;

use App\Http\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        return view('{$lowerName}.index', [
            'title' => '{$name} Plugin'
        ]);
    }

    public function show(\$id)
    {
        // منطق نمایش یک آیتم
        return "Show item 1 from {$name} plugin";
    }
}
PHP;
        file_put_contents($pluginDir . "/Controllers/{$name}Controller.php", $controllerStub);

        // ========== 4. یک مدل نمونه با نام پلاگین (اختیاری) ==========
        $modelStub = <<<PHP
<?php
namespace Plugins\\{$name}\Models;

use App\Database\Model;

class {$name} extends Model
{
    protected static \$table = '{$lowerName}s';
    protected static \$fillable = ['title', 'content'];
}
PHP;
        file_put_contents($pluginDir . "/Models/{$name}.php", $modelStub);

        // ========== 5. یک ویو نمونه (index.lady.php) ==========
        $viewStub = <<<HTML
@extends('layouts.app')

@section('content')
    <h1>{{ \$title ?? '{$name} Plugin' }}</h1>
    <p>Welcome to the {$name} plugin.</p>
@endsection
HTML;
        file_put_contents($pluginDir . "/Views/index.lady.php", $viewStub);

        // ========== 6. فایل routes.php با پیشوند نام پلاگین ==========
        $routesStub = <<<PHP
<?php

Route::prefix('{$lowerName}')->group(function () {
    Route::get('/', [\Plugins\\{$name}\Controllers\\{$name}Controller::class, 'index'])->name('{$lowerName}.index');
    Route::get('/{id}', [\Plugins\\{$name}\Controllers\\{$name}Controller::class, 'show'])->name('{$lowerName}.show');
});
PHP;
        file_put_contents($pluginDir . '/routes.php', $routesStub);

        // ========== 7. فایل کانفیگ نمونه با نام پلاگین ==========
        $configStub = <<<PHP
<?php
// Config file for {$name} plugin
return [
    'version' => '1.0.0',
    'settings' => [
        'example_setting' => 'default_value',
    ]
];
PHP;
        file_put_contents($pluginDir . '/config/plugin_config.php', $configStub);

        // ========== 8. یک فایل asset نمونه (فقط placeholder) ==========
        $cssStub = "/* {$name} plugin styles */\n.plugin-{$lowerName} { color: blue; }";
        file_put_contents($pluginDir . '/assets/style.css', $cssStub);

        // ========== 9. یک مایگریشن نمونه با نام پلاگین ==========
        $timestamp = date('Y_m_d_His');
        $migrationName = "create_{$name}_tables";
        $migrationStub = <<<PHP
<?php
namespace Plugins\\{$name}\Migrations;

use App\Database\Schema;
use App\Database\TableBuilder;

class {$migrationName}
{
    public static function up()
    {
        Schema::create('{$lowerName}s', function (TableBuilder \$table) {
            \$table->id();
            \$table->string('title', 191);
            \$table->text('content')->nullable();
            \$table->timestamps(true);
        });
    }

    public static function down()
    {
        Schema::dropIfExists('{$lowerName}s');
    }
}
PHP;
        file_put_contents($pluginDir . "/Migrations/{$timestamp}_{$migrationName}.php", $migrationStub);

        // ========== 10. یک فایل .gitkeep برای پوشه assets ==========
        file_put_contents($pluginDir . '/assets/.gitkeep', '');

        // ========== نتیجه نهایی ==========
        echo Colors::green("\n✓ Plugin '{$name}' created successfully at: app/Plugins/{$name}\n");
        echo Colors::dim("Structure:\n");
        echo Colors::dim("  - Controllers/{$name}Controller.php\n");
        echo Colors::dim("  - Models/{$name}Model.php\n");
        echo Colors::dim("  - Views/index.lady.php\n");
        echo Colors::dim("  - config/plugin_config.php (will be published to app/Config/{$lowerName}.php)\n");
        echo Colors::dim("  - Migrations/{$timestamp}_{$migrationName}.php\n");
        echo Colors::dim("  - routes.php (prefix: /{$lowerName})\n");
        echo Colors::dim("  - assets/style.css\n");
        echo Colors::dim("\nNext steps:\n");
        echo Colors::dim("  1. Review and edit plugin.json if needed\n");
        echo Colors::dim("  2. Run: php zeno plugin:install {$name}\n");
        echo Colors::dim("  3. After installation, you can access: /{$lowerName}\n");
    }
}