<?php
require_once __DIR__ . '/../Colors.php';

class MakeMiddleware
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new middleware class";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Enter middleware name (e.g., Auth or Admin/CheckRole)");
        }

        $name = str_replace(['\\', '/'], '/', $name);
        $parts = explode('/', $name);
        $className = array_pop($parts);
        $namespace = 'App\\Http\\Middlewares';
        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }
        $path = 'app/Http/Middlewares/' . $name . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        if (file_exists($path)) {
            echo Colors::error("Middleware already exists: {$path}\n");
            return;
        }

        $stub = $this->getStub($className, $namespace);
        file_put_contents($path, $stub);
        echo Colors::green("✓ Middleware created: {$path}\n");
    }

    protected function getStub($className, $namespace)
    {
        return <<<PHP
<?php

namespace {$namespace};

use App\Http\Middleware;

class {$className} extends Middleware
{
    public function handle(\$request, \$next)
    {
        // قبل از اجرای کنترلر
        // if (!Auth::check()) return redirect()->route('login');
        
        \$response = \$next(\$request);
        
        // بعد از اجرای کنترلر
        return \$response;
    }
}
PHP;
    }
}