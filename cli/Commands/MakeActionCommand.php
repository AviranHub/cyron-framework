<?php
// cli/Commands/MakeActionCommand.php

require_once __DIR__ . '/../Colors.php';

class MakeActionCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "Create a new Action class (like Laravel 13 Actions)";
    }

    public function execute()
    {
        // ۱. دریافت نام اکشن
        $name = $this->input->getArgument(1);
        if (!$name) {
            $name = $this->input->ask("Enter action name (e.g., Post/LikePost or LikePost)");
        }

        // ۲. پردازش نام و مسیر
        $name = str_replace(['\\', '/'], '/', $name);
        $parts = explode('/', $name);
        $className = array_pop($parts);
        $namespace = 'App\\Actions';
        if (!empty($parts)) {
            $namespace .= '\\' . implode('\\', $parts);
        }
        $path = 'app/Actions/' . $name . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // ۳. بررسی وجود فایل
        if (file_exists($path)) {
            echo Colors::error("Action already exists: {$path}\n");
            return;
        }

        // ۴. دریافت نوع اکشن (اختیاری)
        $type = $this->input->choice(
            "What type of action is this?",
            ['simple', 'with-validation', 'with-model'],
            0
        );

        // ۵. ساخت محتوای فایل بر اساس نوع
        $stub = $this->getStub($className, $namespace, $type);

        // ۶. ذخیره فایل
        file_put_contents($path, $stub);
        echo Colors::green("✓ Action created: {$path}\n");

        // ۷. پیشنهاد ثبت در کنترلر یا مسیر
        $register = $this->input->confirm("\nDo you want to register this action in a controller? (y/n)", false);
        if ($register) {
            $this->suggestControllerUsage($className, $namespace);
        }

        // ۸. نمایش راهنما
        echo Colors::dim("\nYou can now use this action in your controller:\n");
        echo Colors::dim("  return (new \\{$namespace}\\{$className}())->execute(\$request->all());\n");
        echo Colors::dim("  or using helper: return action(\\{$namespace}\\{$className}::class, \$request->all());\n");
    }

    /**
     * دریافت استاب بر اساس نوع اکشن
     */
    protected function getStub($className, $namespace, $type)
    {
        $baseStub = <<<PHP
<?php

namespace {$namespace};

use App\Actions\BaseAction;

class {$className} extends BaseAction
{
    public function execute(array \$data = [])
    {
        // ۱. احراز هویت
        \$user = \$this->user();
        if (!\$user) {
            return \$this->unauthorized();
        }

PHP;

        switch ($type) {
            case 'with-validation':
                $stub = $baseStub . $this->getValidationStub();
                break;
            case 'with-model':
                $stub = $baseStub . $this->getModelStub($className);
                break;
            default:
                $stub = $baseStub . $this->getSimpleStub();
        }

        $stub .= "\n        // پاسخ نهایی\n        return \$this->success([\n            'message' => 'Action executed successfully',\n        ]);\n    }\n}";

        return $stub;
    }

    protected function getSimpleStub()
    {
        return <<<PHP

        // ۲. منطق اصلی اکشن
        // مثال: ذخیره یک رکورد در دیتابیس
        // \$model = YourModel::create(\$data);

PHP;
    }

    protected function getValidationStub()
    {
        return <<<PHP

        // ۲. اعتبارسنجی
        \$rules = [
            // 'field' => 'required|string|max:255',
        ];
        \$errors = \$this->validate(\$data, \$rules);
        if (\$errors) {
            return \$this->validationError(\$errors);
        }

        // ۳. منطق اصلی اکشن

PHP;
    }

    protected function getModelStub($className)
    {
        // حدس زدن نام مدل بر اساس نام اکشن
        $modelName = str_replace('Action', '', $className);
        $modelName = str_replace(['Like', 'Unlike', 'Bookmark'], ['Post', 'Post', 'Post'], $modelName);
        if (empty($modelName)) $modelName = 'YourModel';

        return <<<PHP

        // ۲. دریافت مدل
        \$id = (int) (\$data['id'] ?? 0);
        if (!\$id) {
            return \$this->error('شناسه نامعتبر');
        }

        \$model = \\App\\Models\\{$modelName}::find(\$id);
        if (!\$model) {
            return \$this->error('{$modelName} یافت نشد', 404);
        }

        // ۳. منطق اصلی اکشن (مثلاً لایک یا بوکمارک)

PHP;
    }

    /**
     * پیشنهاد استفاده در کنترلر
     */
    protected function suggestControllerUsage($className, $namespace)
    {
        $controllerName = $this->input->ask("Enter controller name (e.g., AjaxController)");
        if (!$controllerName) return;

        $methodName = strtolower(str_replace('Action', '', $className));
        if (empty($methodName)) $methodName = 'execute';

        echo Colors::yellow("\nAdd this method to your controller:\n");
        echo Colors::dim(<<<PHP
public function {$methodName}(Request \$request)
{
    return action(\\{$namespace}\\{$className}::class, \$request->all());
}
PHP
        );
        echo Colors::dim("\nAnd add route:\n");
        echo Colors::dim("Route::post('/ajax/{$methodName}', [AjaxController::class, '{$methodName}'])->name('ajax.{$methodName}')->middleware(AuthMiddleware::class);\n");
    }

    /**
     * متدهای کمکی برای اعتبارسنجی و خطا (در کلاس BaseAction باید وجود داشته باشد)
     */
    // این متدها در BaseAction پیاده‌سازی می‌شوند
}