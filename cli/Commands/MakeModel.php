<?php
require_once __DIR__ . '/../Colors.php';

class MakeModel
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new model class (with optional migration)";
    }

    public function execute()
    {
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Enter model name (e.g., Book or Admin/User)");
        }

        $name = str_replace(['\\', '/'], '/', $name);
        $parts = explode('/', $name);
        $className = array_pop($parts);
        $namespace = 'App\\Models';
        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }
        $path = 'app/Models/' . $name . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        if (file_exists($path)) {
            echo Colors::error("Model already exists: {$path}\n");
            return;
        }

        // ✅ اصلاح: ابتدا به snake_case، سپس جمع‌بندی
        $tableName = $this->pluralize($this->toSnakeCase($className));
        
        $fillable = $this->input->ask("Enter fillable fields (comma separated)", "");
        $content = $this->getModelStub($className, $namespace, $tableName, $fillable);
        file_put_contents($path, $content);
        echo Colors::green("✓ Model created: {$path}\n");

        $withMigration = $this->input->getOption('migration') || $this->input->getOption('m');
        if (!$withMigration) {
            $answer = $this->input->confirm("Do you want to create a migration for this model?");
            $withMigration = ($answer === 'y' || $answer === 'yes');
        }

        if ($withMigration) {
            $migrationName = "create_{$tableName}_table";
            system("php zeno make:migration {$migrationName}");
        }
    }

    /**
     * تبدیل نام مدل به snake_case
     * مثال: ForumCategory → forum_category
     */
    protected function toSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $string));
    }

    /**
     * جمع‌بندی صحیح واژه‌های انگلیسی
     */
    protected function pluralize($word)
    {
        $irregular = [
            'child'  => 'children',
            'person' => 'people',
            'man'    => 'men',
            'woman'  => 'women',
            'mouse'  => 'mice',
            'goose'  => 'geese',
            'foot'   => 'feet',
            'tooth'  => 'teeth',
            'ox'     => 'oxen',
            'sheep'  => 'sheep',
            'fish'   => 'fish',
            'deer'   => 'deer',
            'series' => 'series',
            'species'=> 'species',
            'news'   => 'news',
        ];

        if (isset($irregular[$word])) {
            return $irregular[$word];
        }

        if (preg_match('/(s|ss|sh|ch|x|z)$/i', $word)) {
            return $word . 'es';
        }

        if (preg_match('/([^aeiou])y$/i', $word, $matches)) {
            return preg_replace('/y$/i', 'ies', $word);
        }

        if (preg_match('/fe?$/i', $word)) {
            return preg_replace('/fe?$/i', 'ves', $word);
        }

        return $word . 's';
    }

    protected function getModelStub($className, $namespace, $tableName, $fillable)
    {
        $fillableArray = $fillable ? "'" . str_replace(',', "', '", $fillable) . "'" : '';
        return <<<PHP
<?php

namespace {$namespace};

use App\Database\Model;

class {$className} extends Model
{
    protected static \$table = '{$tableName}';
    protected static \$fillable = [{$fillableArray}];
}
PHP;
    }
}