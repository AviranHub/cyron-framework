<?php
class MakeFakeCommand
{
    protected $input;

    public function __construct($input) { $this->input = $input; }

    public static function getDescription() { return "Create a fake data class for a model"; }

    public function execute()
    {
        $model = $this->input->getArgument(1);
        if (!$model) $model = $this->input->ask("Model name (e.g., Book)");

        $fakeClass = $model . 'Fake';
        $path = "app/Fakes/{$fakeClass}.php";
        if (!is_dir('app/Fakes')) mkdir('app/Fakes', 0777, true);

        if (file_exists($path)) {
            echo "Fake already exists: {$path}\n";
            return;
        }

        $stub = <<<PHP
<?php
namespace App\Fakes;

use App\Core\Faker;
use App\Models\\{$model};

class {$fakeClass}
{
    public static function definition()
    {
        return [
            // مثال:
            // 'name' => Faker::name(),
            // 'email' => Faker::email(),
            // 'slug' => Faker::slug(Faker::word()),
        ];
    }

    public static function create(\$count = 1)
    {
        for (\$i = 0; \$i < \$count; \$i++) {
            \$data = static::definition();
            {$model}::create(\$data);
        }
    }
}
PHP;
        file_put_contents($path, $stub);
        echo "✓ Fake class created: {$path}\n";
    }
}