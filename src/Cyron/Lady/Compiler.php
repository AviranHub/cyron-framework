<?php

namespace Cyron\Lady;

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

    public function compile(string $viewPath): string
    {
        $this->layout = null;
        $this->sections = [];

        $content = file_get_contents($viewPath);
        $content = $this->processExtends($content);
        $content = $this->processSections($content);
        $content = $this->compileLadyDirectives($content);
        $content = $this->parser->parse($content);

        if ($this->layout) {
            $content = $this->wrapWithLayout($content);
        }

        $compiledPath = $this->getCompilePath($viewPath);
        file_put_contents($compiledPath, $content);

        return $compiledPath;
    }

    protected function processExtends(string $content): string
    {
        if (preg_match('/@extends\([\'\"](.+?)[\'\"]\)/', $content, $match)) {
            $this->layout = $match[1];
            $content = preg_replace('/@extends\([\'\"](.+?)[\'\"]\)/', '', $content);
        }
        return $content;
    }

    protected function processSections(string $content): string
    {
        $content = preg_replace_callback("/@section\s*\(\s*('[^']*'|\"[^\"]*\"|\w+)\s*,\s*('[^']*'|\"[^\"]*\"|[^)]+?)\s*\)/s", function ($matches) {
            $this->sectionsAny[$matches[1]] = $matches[2];
            return '';
        }, $content);

        $pattern = '/@section\(\s*\'(.*?)\'\s*\)(.*?)@endsection/s';
        return preg_replace_callback($pattern, function ($matches) {
            $this->sections[$matches[1]] = $matches[2];
            return '';
        }, $content);
    }

    protected function compileLadyDirectives(string $content): string
    {
        $content = preg_replace('/@var\(\s*\'(.+?)\'\s*\)/', '<?= vars(\'$1\') ?>', $content);
        $content = preg_replace('/@storage\(\s*\'(.+?)\'\s*\)/', '<?= storage_url(\'$1\') ?>', $content);
        $content = preg_replace('/@asset\(\s*\'(.+?)\'\s*\)/', '<?= asset(\'$1\') ?>', $content);
        $content = preg_replace_callback('/@route\(\s*\'(.+?)\'\s*(?:,\s*(\[.*?\]))?\)/s', function ($m) {
            $params = $m[2] ?? '[]';
            return "<?= route('{$m[1]}', {$params}) ?>";
        }, $content);
        $content = preg_replace('/@csrf/', '<?= csrf_field() ?>', $content);
        $content = preg_replace('/@method\(\s*\'(.+?)\'\s*\)/', '<?= method_field(\'$1\') ?>', $content);
        $content = preg_replace('/@lang\(\s*\'(.+?)\'\s*\)/', '<?= __(\'$1\') ?>', $content);
        $content = preg_replace('/@choice\(\s*\'(.+?)\',\s*(\d+)\s*\)/', '<?= trans_choice(\'$1\', $2) ?>', $content);
        $content = preg_replace('/@title\(\s*\'(.+?)\'\s*\)/', '<?= show_title(\'$1\') ?>', $content);
        $content = preg_replace('/@set\(\s*[\'\"](.+?)[\'\"]\s*,\s*[\'\"](.+?)[\'\"]\s*\)/', '<?php $__set[\'$1\'] = \'$2\'; ?>', $content);
        $content = preg_replace('/@json\(\s*(.+?)\s*\)/', '<?= json_encode($1) ?>', $content);
        $content = preg_replace_callback('/@errors(.*?)@enderrors/s', function ($matches) {
            return '<?php if(isset($errors) && $errors->any()): ?>
<div class="alert alert-danger">
    <?php foreach($errors->all() as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>';
        }, $content);

        $content = preg_replace_callback('/@error\(\s*[\'\"](.+?)[\'\"]\s*\)(.*?)@enderror/s', function ($matches) {
            $field = $matches[1];
            $inner = $matches[2];
            $inner = preg_replace('/\{\{\s*\$message\s*\}\}/', '<?= $__errorMessage ?? "" ?>', $inner);
            return '<?php if(isset($errors) && method_exists($errors, "has") && $errors->has("' . $field . '")): 
        $__errorMessage = $errors->get("' . $field . '")[0] ?? ""; ?>' . $inner . '<?php endif; ?>';
        }, $content);

        $content = preg_replace('/@success/', '<?php if(isset($success)): ?>', $content);
        $content = preg_replace('/@endsuccess/', '<?php endif; ?>', $content);
        $content = preg_replace('/@can\(\s*\'(.+?)\'\s*\)/', '<?php if(auth()->user()->can(\'$1\')): ?>', $content);
        $content = preg_replace('/@cannot\(\s*\'(.+?)\'\s*\)/', '<?php if(!auth()->user()->can(\'$1\')): ?>', $content);
        $content = preg_replace('/@endcan/', '<?php endif; ?>', $content);
        $content = preg_replace('/@guest/', '<?php if(auth()->guest()): ?>', $content);
        $content = preg_replace('/@auth/', '<?php if(auth()->check()): ?>', $content);
        $content = preg_replace('/@endguest/', '<?php endif; ?>', $content);
        $content = preg_replace('/@endauth/', '<?php endif; ?>', $content);
        $content = preg_replace('/@env\(\s*\'(.+?)\'\s*\)/', '<?php if(app()->environment(\'$1\')): ?>', $content);
        $content = preg_replace('/@endenv/', '<?php endif; ?>', $content);
        $content = preg_replace_callback('/@include\(\s*\'(.+?)\'\s*\)/', function ($m) {
            $view = str_replace('.', '/', $m[1]);
            return "<?php include \$__env->getCompiledPath('{$view}'); ?>";
        }, $content);
        $content = preg_replace('/@push\(\s*\'(.+?)\'\s*\)/', '<?php $__env->startPush(\'$1\'); ?>', $content);
        $content = preg_replace('/@endpush/', '<?php $__env->endPush(); ?>', $content);
        $content = preg_replace('/@stack\(\s*\'(.+?)\'\s*\)/', '<?php $__env->renderPush(\'$1\'); ?>', $content);
        $content = preg_replace('/@yield\(\s*\'(.+?)\'\s*\)/', '<?php echo $__sections[\'$1\']($__data); ?>', $content);
        $content = preg_replace('/\{\{\s*section_(\w+)\s*\}\}/', '<?php echo $__sections[\'$1\'](); ?>', $content);
        $content = preg_replace_callback('/@use\(\s*\'(.+?)\'\s*\)/', function ($m) {
            return "<?php component()->inherit('{$m[1]}'); ?>";
        }, $content);
        $content = preg_replace_callback('/@props\(\s*\[(.*?)\]\s*\)/s', function ($matches) {
            $propsString = $matches[1];
            $phpCode = $this->compilePropsToPhp($propsString);
            return "<?php {$phpCode} ?>";
        }, $content);
        $content = preg_replace_callback('/@component\(\s*\'(.+?)\'\s*(?:,\s*(.+?))?\)/', function ($m) {
            $name = $m[1];
            $argsString = $m[2] ?? '';
            $parsed = $this->parseComponentArgs($argsString);
            return "<?php component()->start('{$name}', {$parsed['props']}, {$parsed['attributes']}); ?>";
        }, $content);
        $content = preg_replace_callback('/@slot\(\s*\'(.+?)\'\s*\)/', function ($m) {
            return '<?php component()->slot(\'' . $m[1] . '\'); ?>';
        }, $content);
        $content = preg_replace('/@endslot/', '<?php component()->endSlot(); ?>', $content);
        $content = preg_replace('/@endcomponent/', '<?php echo component()->end(); ?>', $content);

        return $content;
    }

    protected function compilePropsToPhp(string $propsString): string
    {
        preg_match_all('/([\'\"]?)([a-zA-Z_][a-zA-Z0-9_]*)\1\s*=>?\s*([^,]+)/', $propsString, $matches, PREG_SET_ORDER);

        $lines = [];
        foreach ($matches as $match) {
            $key = $match[2];
            $value = trim($match[3]);
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

        return implode("\n", $lines);
    }

    protected function parseComponentArgs(string $argsString): array
    {
        $props = [];
        $attributes = [];
        if (trim($argsString) === '') {
            return ['props' => '[]', 'attributes' => '[]'];
        }

        preg_match_all('/([a-zA-Z_][a-zA-Z0-9_-]*)\s*=>\s*([^,]+)(?:,|$)/', $argsString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $props[] = "'{$match[1]}' => {$match[2]}";
        }

        preg_match_all('/([a-zA-Z_][a-zA-Z0-9_-]*)\s*:\s*([^,]+)(?:,|$)/', $argsString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $attributes[] = "'{$match[1]}' => {$match[2]}";
        }

        return ['props' => '[' . implode(', ', $props) . ']', 'attributes' => '[' . implode(', ', $attributes) . ']'];
    }

    protected function wrapWithLayout(string $content): string
    {
        $layoutFile = $this->layout;
        $layoutPath = null;
        $searchPaths = [RESOURCES_PATH . '/Layouts', RESOURCES_PATH . '/Views', RESOURCES_PATH];
        foreach ($searchPaths as $dir) {
            $candidate = rtrim($dir, '/') . '/' . ltrim(str_replace('.', '/', $layoutFile), '/') . '.lady.php';
            if (file_exists($candidate)) {
                $layoutPath = $candidate;
                break;
            }
            $candidate = rtrim($dir, '/') . '/' . ltrim(str_replace('.', '/', $layoutFile), '/') . '.php';
            if (file_exists($candidate)) {
                $layoutPath = $candidate;
                break;
            }
        }

        if ($layoutPath === null) {
            return $content;
        }

            $layoutCode = file_get_contents($layoutPath);
            $layoutCode = preg_replace('/@yield\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php echo $__layoutContent[\'$1\'] ?? ""; ?>', $layoutCode);
            $layoutCode = $this->compileLadyDirectives($layoutCode);
            $layoutCode = $this->parser->parse($layoutCode);

            $sectionCode = '';
            foreach ($this->sections as $name => $section) {
                $section = $this->compileLadyDirectives($section);
                $section = $this->parser->parse($section);
                $sectionCode .= "<?php ob_start(); ?>" . $section . "<?php \$__layoutContent['{$name}'] = ob_get_clean(); ?>";
            }

            return "<?php \n\$__layoutContent = []; \n?>" . $sectionCode . $layoutCode;
    }

    protected function getCompilePath(string $viewPath): string
    {
        $hash = md5($viewPath);
        return $this->cachePath . '/' . $hash . '.php';
    }
}
