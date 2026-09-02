<?php

class MakeController {
    protected $input;
    
    public function __construct($input) {
        $this->input = $input;
    }
    
    public static function getDescription() {
        return "Create a new controller class";
    }
    
    public function execute() {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Enter controller name (e.g., Auth/RegisterController)");
        }
        
        // تبدیل جداکننده‌ها به اسلش یکسان
        $name = str_replace(['\\', '/'], '/', $name);
        $parts = explode('/', $name);
        $className = array_pop($parts);
        $namespace = 'App\\Http\\Controllers';
        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }
        $path = 'app/Http/Controllers/' . $name . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        if (file_exists($path)) {
            echo "Controller already exists!\n";
            return;
        }
        
        $stub = $this->getStub();
        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            $stub
        );
        // رفع {{class|lower}} در stub
        $content = str_replace('{{class|lower}}', strtolower($className), $content);
        
        file_put_contents($path, $content);
        echo "✓ Controller created: {$path}\n";
    }
    
    protected function getStub() {
        return <<<PHP
<?php

namespace {{namespace}};

use App\Http\Controller;

class {{class}} extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function index()
    {
        return view('{{class|lower}}.index');
    }
}
PHP;
    }
}