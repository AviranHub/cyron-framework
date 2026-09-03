<?php
class FakeCommand
{
    protected $input;

    public function __construct($input) { $this->input = $input; }

    public static function getDescription() { return "Generate fake data for a model automatically"; }

    public function execute()
    {
        $modelName = $this->input->getArgument(1);
        $count = (int)($this->input->getArgument(2) ?: 10);

        if (!$modelName) {
            echo "Usage: php zeno fake <Model> [count]\n";
            return;
        }

        $modelClass = "App\\Models\\{$modelName}";
        if (!class_exists($modelClass)) {
            echo "Model not found: {$modelClass}\n";
            return;
        }

        // بررسی وجود متد getTableColumns در مدل
        if (!method_exists($modelClass, 'getTableColumns')) {
            echo "Model must extend App\\Database\\Model with getTableColumns method.\n";
            return;
        }

        $columns = $modelClass::getTableColumns();
        $reflection = new ReflectionClass($modelClass);
        $fillableProperty = $reflection->getProperty('fillable');
        $fillableProperty->setAccessible(true);
        $fillable = $fillableProperty->getValue();

        echo "Generating {$count} fake records for {$modelName}...\n";

        for ($i = 0; $i < $count; $i++) {
            $data = [];
            foreach ($columns as $col) {
                $field = $col['Field'];
                // رد کردن فیلدهای خودکار (id, created_at, updated_at, deleted_at)
                if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at'])) continue;
                // اگر مدل فیلدهای fillable دارد، فقط آنها را پر کن
                if (!empty($fillable) && !in_array($field, $fillable)) continue;

                if (str_ends_with($field, '_id') && ($col['Null'] ?? 'NO') === 'YES') {
                    $data[$field] = null;
                    continue;
                }
                
                $type = $col['Type'];
                $data[$field] = \App\Core\Faker::typeValue($type, $field);
                if ($field === 'slug') {
                    $data[$field] .= '-' . bin2hex(random_bytes(3));
                }
            }
            $modelClass::create($data);
        }

        echo "Done. {$count} records inserted.\n";
    }
}