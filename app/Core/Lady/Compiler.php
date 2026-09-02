<?php
// App/Core/Lady/Compiler.php

namespace App\Core\Lady;

class Compiler
{
    protected Parser $parser;
    protected string $cachePath;

    protected array $sections = [];
    protected array $sectionsAny = [];
    protected ?string $layout = null;

    public function __construct(Parser $parser, string $cachePath)
    {
        $this->parser = $parser;
        $this->cachePath = rtrim($cachePath, '/');
    }

    /**
     * کامپایل یک فایل ویو به فایل PHP قابل اجرا
     */
    public function compile(string $viewPath): string
    {
        $this->layout = null;
        $this->sections = [];

        $content = file_get_contents($viewPath);
        // error_log(" |||||||||||  ".$viewPath);

        // استخراج @extends و @section و حذف آنها
        $content = $this->processExtends($content);
        $content = $this->processSections($content);

        // تبدیل دستورات سفارشی لیدی (پیش از parse)
        $content = $this->compileLadyDirectives($content);

        // تبدیل ساختارهای اصلی شرط، حلقه و echo
        $content = $this->parser->parse($content);

        // اگر layout وجود دارد، کد نهایی را با جمع‌آوری بخش‌ها بپوشان
        if ($this->layout) {
            $content = $this->wrapWithLayout($content);
        }

        // ذخیره در کش
        $compiledPath = $this->getCompilePath($viewPath);
        file_put_contents($compiledPath, $content);

        // error_log(" = = = {$compiledPath}");

        return $compiledPath;
    }

    protected function processExtends(string $content): string
    {
        if (preg_match('/@extends\([\'"](.+?)[\'"]\)/', $content, $match)) {
            $this->layout = $match[1];
            $content = preg_replace('/@extends\([\'"](.+?)[\'"]\)/', '', $content);
        }
        return $content;
    }

    // protected function processSections(string $content): string
    // {
    //     $pattern = '/@section\(\s*\'(.+?)\'\s*\)(.*?)@endsection/s';
    //     return preg_replace_callback($pattern, function ($matches) {
    //         $this->sections[$matches[1]] = $matches[2];
    //         return '';
    //     }, $content);
    // }

    protected function processSections(string $content): string
    {
        /* $content = preg_replace('/@section\(\s*\'(.+?)\',\s*\'(.+?)\'\s*\)/', '@section(\'$1\') $2 @endsection', $content); */
        $content = preg_replace_callback("/@section\s*\(\s*('[^']*'|\"[^\"]*\"|\w+)\s*,\s*('[^']*'|\"[^\"]*\"|[^)]+?)\s*\)/s", function ($matches) {
            $this->sectionsAny[$matches[1]] = $matches[2];
            return '';
            // dd($matches);
        }, $content);

        $pattern = '/@section\(\s*\'(.+?)\'\s*\)(.*?)@endsection/s';
        return preg_replace_callback($pattern, function ($matches) {
            $this->sections[$matches[1]] = $matches[2];
            return '';
        }, $content);
    }

    protected function compileLadyDirectives(string $content): string
    {
        // @var('key')
        $content = preg_replace('/@var\(\s*\'(.+?)\'\s*\)/', '<?= vars(\'$1\') ?>', $content);

        // @storage('path')
        $content = preg_replace('/@storage\(\s*\'(.+?)\'\s*\)/', '<?= storage_url(\'$1\') ?>', $content);

        // @asset('path')
        $content = preg_replace('/@asset\(\s*\'(.+?)\'\s*\)/', '<?= asset(\'$1\') ?>', $content);

        // @route('name', [...] )
        $content = preg_replace_callback('/@route\(\s*\'(.+?)\'\s*(?:,\s*(\[.*?\]))?\)/s', function ($m) {
            $params = $m[2] ?? '[]';
            return "<?= route('{$m[1]}', {$params}) ?>";
        }, $content);

        // @csrf
        $content = preg_replace('/@csrf/', '<?= csrf_field() ?>', $content);

        // @method('PUT')
        $content = preg_replace('/@method\(\s*\'(.+?)\'\s*\)/', '<?= method_field(\'$1\') ?>', $content);

        // @lang('file.key')
        $content = preg_replace('/@lang\(\s*\'(.+?)\'\s*\)/', '<?= __(\'$1\') ?>', $content);

        // @choice('file.key', count)
        $content = preg_replace('/@choice\(\s*\'(.+?)\',\s*(\d+)\s*\)/', '<?= trans_choice(\'$1\', $2) ?>', $content);

        // @title
        $content = preg_replace('/@title\(\s*\'(.+?)\'\s*\)/', '<?= show_title(\'$1\') ?>', $content);

        // @set('key', 'value')
        $content = preg_replace('/@set\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)/', '<?php $__set[\'$1\'] = \'$2\'; ?>', $content);

        // @json($data)
        $content = preg_replace('/@json\(\s*(.+?)\s*\)/', '<?= json_encode($1) ?>', $content);

        // @errors ... @enderrors (نمایش ساده خطاها)
        $content = preg_replace_callback('/@errors(.*?)@enderrors/s', function ($matches) {
            return '<?php if(isset($errors) && $errors->any()): ?>
<div class="alert alert-danger">
    <?php foreach($errors->all() as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>';
        }, $content);


        // @error('field') ... @enderror
        $content = preg_replace_callback('/@error\(\s*[\'"](.+?)[\'"]\s*\)(.*?)@enderror/s', function ($matches) {
            $field = $matches[1];
            $inner = $matches[2];

            // تبدیل {{ $message }} به محتوای خطای واقعی
            $inner = preg_replace('/\{\{\s*\$message\s*\}\}/', '<?= $__errorMessage ?? "" ?>', $inner);

            // بررسی وجود خطا برای فیلد مورد نظر و استخراج اولین پیام
            return '<?php if(isset($errors) && method_exists($errors, "has") && $errors->has("' . $field . '")): 
        $__errorMessage = $errors->get("' . $field . '")[0] ?? ""; ?>' . $inner . '<?php endif; ?>';
        }, $content);
        /*
        // @error('field') ... @enderror
        $content = preg_replace_callback('/@error\(\s*[\'"](.+?)[\'"]\s*\)(.*?)@enderror/s', function ($matches) {
            $field = $matches[1];
            $inner = $matches[2];
            $inner = preg_replace('/\{\{\s*\$message\s*\}\}/', '<?= $__errorMessage ?? "" ?>', $inner);
            return '<?php if(isset($errors) && method_exists($errors, "has") && $errors->has("' . $field . '")): 
        $__errorMessage = $errors->get("' . $field . '")[0] ?? ""; ?>' . $inner . '<?php endif; ?>';
        }, $content);
*/
        // @success ... @endsuccess
        $content = preg_replace('/@success/', '<?php if(isset($success)): ?>', $content);
        $content = preg_replace('/@endsuccess/', '<?php endif; ?>', $content);

        // @can / @cannot
        $content = preg_replace('/@can\(\s*\'(.+?)\'\s*\)/', '<?php if(auth()->user()->can(\'$1\')): ?>', $content);
        $content = preg_replace('/@cannot\(\s*\'(.+?)\'\s*\)/', '<?php if(!auth()->user()->can(\'$1\')): ?>', $content);
        $content = preg_replace('/@endcan/', '<?php endif; ?>', $content);

        // @guest / @auth
        $content = preg_replace('/@guest/', '<?php if(auth()->guest()): ?>', $content);
        $content = preg_replace('/@auth/', '<?php if(auth()->check()): ?>', $content);
        $content = preg_replace('/@endguest/', '<?php endif; ?>', $content);
        $content = preg_replace('/@endauth/', '<?php endif; ?>', $content);

        // @env('production')
        $content = preg_replace('/@env\(\s*\'(.+?)\'\s*\)/', '<?php if(app()->environment(\'$1\')): ?>', $content);
        $content = preg_replace('/@endenv/', '<?php endif; ?>', $content);

        // @include('view.name')
        $content = preg_replace_callback('/@include\(\s*\'(.+?)\'\s*\)/', function ($m) {
            $view = str_replace('.', '/', $m[1]);
            return "<?php include \$__env->getCompiledPath('{$view}'); ?>";
        }, $content);

        // @push / @endpush
        $content = preg_replace('/@push\(\s*\'(.+?)\'\s*\)/', '<?php $__env->startPush(\'$1\'); ?>', $content);
        $content = preg_replace('/@endpush/', '<?php $__env->endPush(); ?>', $content);

        // @stack('name')
        $content = preg_replace('/@stack\(\s*\'(.+?)\'\s*\)/', '<?php $__env->renderPush(\'$1\'); ?>', $content);

        // @yield('sectionName')
        /* $content = preg_replace('/@yield\(\s*\'(.+?)\'\s*\)/', '<?php echo $__sections[\'$1\'](); ?>', $content);*/
        $content = preg_replace('/@yield\(\s*\'(.+?)\'\s*\)/', '<?php echo $__sections[\'$1\']($__data); ?>', $content);

        // پشتیبانی از {{ section_name }} قدیمی (برای سازگاری با لایه‌های قدیمی)
        $content = preg_replace('/\{\{\s*section_(\w+)\s*\}\}/', '<?php echo $__sections[\'$1\'](); ?>', $content);

        // @use('ComponentName') برای وراثت (مشابه @extends در کامپوننت)
        $content = preg_replace_callback('/@use\(\s*\'(.+?)\'\s*\)/', function ($m) {
            return "<?php component()->inherit('{$m[1]}'); ?>";
        }, $content);

        // @props(['name' => 'default', 'active' => false])
        $content = preg_replace_callback('/@props\(\s*\[(.*?)\]\s*\)/s', function ($matches) {
            $propsString = $matches[1];
            $phpCode = $this->compilePropsToPhp($propsString);
            return "<?php {$phpCode} ?>";
        }, $content);


        // @component('name', ...)  با پشتیبانی از هر دو نوع آرگومان
        /*$content = preg_replace_callback('/@component\(\s*\'(.+?)\'\s*(?:,\s*(.+?))?\)/', function ($m) {
            $name = $m[1];
            $argsString = $m[2] ?? '';
            $attributes = $this->parseComponentArgs($argsString);
            return "<?php component()->start('{$name}', {$attributes}); ?>";
        }, $content);*/
        $content = preg_replace_callback('/@component\(\s*\'(.+?)\'\s*(?:,\s*(.+?))?\)/', function ($m) {
            $name = $m[1];
            $argsString = $m[2] ?? '';
            $parsed = $this->parseComponentArgs($argsString);
            return "<?php component()->start('{$name}', {$parsed['props']}, {$parsed['attributes']}); ?>";
        }, $content);

        // @slot
        $content = preg_replace_callback('/@slot\(\s*\'(.+?)\'\s*\)/', function ($m) {
            return '<?php component()->slot(\'' . $m[1] . '\'); ?>';
        }, $content);
        $content = preg_replace('/@endslot/', '<?php component()->endSlot(); ?>', $content);
        $content = preg_replace('/@endcomponent/', '<?php echo component()->end(); ?>', $content);


        return $content;
    }

    protected function compilePropsToPhp(string $propsString): string
    {
        // الگوی key => value یا key: value یا key = value
        // مثال: 'active' => false, 'href' => '#', 'class' => 'btn'
        preg_match_all('/([\'"]?)([a-zA-Z_][a-zA-Z0-9_]*)\1\s*=>?\s*([^,]+)/', $propsString, $matches, PREG_SET_ORDER);

        $lines = [];
        foreach ($matches as $match) {
            $key = $match[2];
            $value = trim($match[3]);

            // حذف نقل قول‌های اضافی از مقدار
            if (strpos($value, "'") === 0 || strpos($value, '"') === 0) {
                $value = substr($value, 1, -1);
                $lines[] = "\${$key} = \${$key} ?? \$attributes->get('$key') ?? '{$value}';";
            } elseif ($value === 'true') {
                $lines[] = "\${$key} = \${$key} ?? \$attributes->get('$key') ?? true;";
            } elseif ($value === 'false') {
                $lines[] = "\${$key} = \${$key} ?? \$attributes->get('$key') ?? false;";
            } elseif (is_numeric($value)) {
                $lines[] = "\${$key} = \${$key} ?? \$attributes->get('$key') ?? {$value};";
            } else {
                $lines[] = "\${$key} = \${$key} ?? \$attributes->get('$key') ?? {$value};";
            }
        }

        return implode(' ', $lines);
    }

    // protected function parseComponentArgs(string $str): string
    // {
    //     // اگر با [ شروع شد، همان آرایه است (فرمت قدیمی)
    //     if (str_starts_with(trim($str), '[')) {
    //         return $str;
    //     }

    //     // در غیر این صورت، آرگومان‌های named را تجزیه کن
    //     $attrs = [];
    //     // الگوی key="value" یا key='value' یا key=value
    //     preg_match_all('/([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(?:(["\'])(.*?)\2|([^\s,]+))/', $str, $matches, PREG_SET_ORDER);
    //     foreach ($matches as $m) {
    //         $key = $m[1];
    //         $value = isset($m[3]) ? $m[3] : ($m[4] ?? 'true');
    //         // اگر مقدار true/false بود، بدون کوتیشن
    //         if ($value === 'true') {
    //             $attrs[] = "'{$key}' => true";
    //         } elseif ($value === 'false') {
    //             $attrs[] = "'{$key}' => false";
    //         } elseif (is_numeric($value)) {
    //             $attrs[] = "'{$key}' => {$value}";
    //         } else {
    //             $attrs[] = "'{$key}' => '" . addslashes($value) . "'";
    //         }
    //     }
    //     return '[' . implode(', ', $attrs) . ']';
    // }

    /*protected function parseComponentArgs(string $str): string
    {
        if (str_starts_with(trim($str), '[')) {
            return $str;
        }

        $attrs = [];
        preg_match_all('/([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(?:(["\'])(.*?)\2|([^\s,]+))/', $str, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $key = $m[1];
            $value = isset($m[3]) ? $m[3] : ($m[4] ?? 'true');

            // تشخیص ویژگی داینامیک (با : در کل رشته)
            $isDynamic = preg_match('/:\s*' . preg_quote($key, '/') . '\s*=/', $str);

            if ($isDynamic) {
                $attrs[] = "'{$key}' => {$value}";
            } elseif ($value === 'true') {
                $attrs[] = "'{$key}' => true";
            } elseif ($value === 'false') {
                $attrs[] = "'{$key}' => false";
            } elseif (is_numeric($value)) {
                $attrs[] = "'{$key}' => {$value}";
            } else {
                $attrs[] = "'{$key}' => '" . addslashes($value) . "'";
            }
        }

        return '[' . implode(', ', $attrs) . ']';
    }*/

    protected function parseComponentArgs(string $str): array
    {
        $props = [];
        $attributes = [];

        // الگوی key="value" یا key=value
        preg_match_all('/([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(?:(["\'])(.*?)\2|([^\s,]+))/', $str, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $key = $m[1];
            $value = isset($m[3]) ? $m[3] : ($m[4] ?? 'true');

            // تشخیص ویژگی داینامیک (با :)
            $isDynamic = strpos($str, ':' . $key) !== false;

            if ($isDynamic) {
                // prop: مقدار خام PHP
                $props[] = "'{$key}' => {$value}";
            } else {
                // attribute: مقدار رشته‌ای
                $attributes[] = "'{$key}' => '" . addslashes($value) . "'";
            }
        }

        return [
            'props' => '[' . implode(', ', $props) . ']',
            'attributes' => '[' . implode(', ', $attributes) . ']'
        ];
    }

    protected function wrapWithLayout(string $childContent): string
    {
        $sectionsCode = '';
        foreach ($this->sectionsAny as $name => $content) {
            $sectionsCode .= "\$__sections[{$name}] = {$content};";
        }

        foreach ($this->sections as $name => $content) {
            $compiledSection = $this->compileLadyDirectives($content);
            $compiledSection = $this->parser->parse($compiledSection);
            $sectionsCode .= "\$__sections['{$name}'] = function(\$__data) { extract(\$__data); ?>{$compiledSection}<?php };";
        }
        $layoutView = str_replace('.', '/', $this->layout);
        return <<<PHP
<?php
\$__sections = [];
\$__set = [];
{$sectionsCode}
// ترکیب داده‌ها با مقادیر set
\$__data = array_merge(\$__data ?? [], \$__set);
include \$__env->getCompiledPath('{$layoutView}');
?>
PHP;
    }


    public function getCompilePath(string $viewPath): string
    {
        return $this->cachePath . '/' . md5($viewPath) . '.php';
    }

    public function hasLayout(): bool
    {
        return !is_null($this->layout);
    }

    public function getSections(): array
    {
        return $this->sections;
    }
}
