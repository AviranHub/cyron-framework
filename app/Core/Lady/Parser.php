<?php
// App/Core/Lady/Parser.php

namespace App\Core\Lady;

class Parser
{
    /**
     * تبدیل محتوای قالب به کد PHP
     */
    public function parse(string $content): string
    {
        // 1. حذف کامنت‌های لیدی (باید اولین مرحله باشد)
        $content = $this->compileComments($content);

        $content = $this->compileSwitchBlocks($content);

        $content = $this->compileComponentTags($content);

        // 2. تبدیل بلاک‌های @php ... @endphp
        $content = $this->compileRawPhp($content);

        // 3. تبدیل شرط‌ها و حلقه‌ها
        $content = $this->compileConditionalsAndLoops($content);

        // 4. تبدیل خروجی‌های {{ }} و {!! !!}
        $content = $this->compileEchos($content);


        return $content;
    }

    protected function compileConditionalsAndLoops(string $content): string
    {
        $patterns = [
            '/@if\s*\(\s*((?>[^()]+|\((?1)\))*)\s*\)/' => '<?php if ($1): ?>',
            '/@elseif\s*\(\s*(.+?)\s*\)/' => '<?php elseif ($1): ?>',
            '/@else/' => '<?php else: ?>',
            '/@endif/' => '<?php endif; ?>',
            '/@foreach\s*\(\s*(.+?)\s+as\s+(.+?)\s*\)/' => '<?php foreach ($1 as $2): ?>',
            '/@foreach\s*\(\s*(.+?)\s*\)/' => '<?php foreach ($1): ?>',
            '/@endforeach/' => '<?php endforeach; ?>',
            '/@for\s*\(\s*(.+?)\s*\)/' => '<?php for ($1): ?>',
            '/@endfor/' => '<?php endfor; ?>',
            '/@while\s*\(\s*(.+?)\s*\)/' => '<?php while ($1): ?>',
            '/@endwhile/' => '<?php endwhile; ?>',
            '/@isset\s*\(\s*(.+?)\s*\)/' => '<?php if (isset($1)): ?>',
            '/@endisset/' => '<?php endif; ?>',
            '/@unless\s*\(\s*(.+?)\s*\)/' => '<?php if (! ($1)): ?>',
            '/@endunless/' => '<?php endif; ?>',
            '/@empty\s*\(\s*(.+?)\s*\)/' => '<?php if (empty($1)): ?>',
            '/@endempty/' => '<?php endif; ?>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    /* protected function compileEchos(string $content): string
    {
        // {{ $var }} => escaped
        $content = preg_replace('/\{\{\s*(.+?)\s*\}}/s', '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>', $content);
        
        // {!! $var !!} => raw
        $content = preg_replace('/\{\!!\s*(.+?)\s*!!\}/s', '<?= $1 ?>', $content);
        
        return $content;
    } */
    protected function compileEchos(string $content): string
    {
        // {{ $var }} => escaped with null check
        $content = preg_replace('/\{\{\s*(.+?)\s*\}}/s', '<?= htmlspecialchars($1 ?? "", ENT_QUOTES, "UTF-8") ?>', $content);

        // {!! $var !!} => raw
        $content = preg_replace('/\{\!!\s*(.+?)\s*!!\}/s', '<?= $1 ?>', $content);

        return $content;
    }

    protected function compileRawPhp(string $content): string
    {
        return preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1; ?>', $content);
    }

    protected function compileComments(string $content): string
    {
        // حذف کامنت‌های لیدی {{-- comment --}}
        return preg_replace('/\{\{--.*?--\}\}/s', '', $content);
    }

    /**
     * پردازش بلوک‌های @switch ... @endswitch
     */
    protected function compileSwitchBlocks(string $content): string
    {
        $pattern = '/@switch\s*\(\s*(.+?)\s*\)(.*?)@endswitch/s';
        return preg_replace_callback($pattern, function ($matches) {
            $condition = $matches[1];
            $body = $matches[2];

            // 1. تبدیل @case('value')
            $body = preg_replace_callback('/@case\s*\(\s*(.+?)\s*\)/s', function ($m) {
                return 'case ' . $m[1] . ": echo '";
            }, $body);
            if (str_contains($body, '@default')){
                $d = "';";
            }else{
                $d = "";
            }
            // 2. تبدیل @default
            $body = preg_replace('/@default\b/', "default: echo '", $body);

            // 3. تبدیل @break (با یا بدون نقطه‌ویرگول)
            $body = preg_replace('/@break\s*;?\s*/', "'; break;", $body);

            // 4. حذف هر php  اضافی که ممکن است تداخل ایجاد کند (اختیاری)
            // اما فعلاً نیازی نیست.
            
            return "<?php switch ({$condition}): {$body} $d endswitch; ?>";
        }, $content);
    }
    // protected function compileComponentTags(string $content): string
    // {
    //     $pattern = '/<x-([a-z][a-z0-9\-]*)\s*([^>]*)>(.*?)<\/x-\1>/is';
    //     return preg_replace_callback($pattern, function ($matches) {
    //         $name = $matches[1];
    //         $attrString = $matches[2];
    //         $slot = $matches[3];

    //         // تبدیل ویژگی‌ها به رشته آرگومان named
    //         $attrs = $this->convertAttrsToNamedArgs($attrString);

    //         // تبدیل به @component معادل
    //         return "@component('{$name}', {$attrs}) {$slot} @endcomponent";
    //     }, $content);
    // }

    // protected function convertAttrsToNamedArgs(string $attrString): string
    // {
    //     // مشابه parseNamedArguments اما برای ویژگی‌های داینامیک :)
    //     preg_match_all('/(\s*)([a-z][a-z0-9\-]*)(?:\s*=\s*(["\'])(.*?)\3)?/i', $attrString, $matches, PREG_SET_ORDER);
    //     $args = [];
    //     foreach ($matches as $m) {
    //         $name = $m[2];
    //         $value = $m[4] ?? 'true';
    //         if (strpos($attrString, ':' . $name) !== false) {
    //             $args[] = "{$name}={$value}"; // بدون کوتیشن
    //         } else {
    //             $args[] = "{$name}=\"{$value}\"";
    //         }
    //     }
    //     return implode(', ', $args);
    // }

    protected function compileComponentTags(string $content): string
    {
        // $pattern = '/<x-([a-z][a-z0-9\-]*)\s*([^>]*)>(.*?)<\/x-\1>/is';
        $pattern = '/<x-([a-z][a-z0-9\-]*)\s*((?:[^>\'"]|"[^"]*"|\'[^\']*\')*)>(.*?)<\/x-\1>/is';
        return preg_replace_callback($pattern, function ($matches) {
            $name = $matches[1];
            $attrString = $matches[2];
            $slot = $matches[3];

            // تبدیل ویژگی‌ها به آرایه PHP
            // $attrs = $this->parseComponentAttributes($attrString);

            // تبدیل مستقیم به PHP (بدون @component)
            /*return "<?php component()->start('{$name}', {$attrs}); ?>{$slot}<?php echo component()->end(); ?>";
        }, $content);*/
            $result = $this->parseComponentAttributes($attrString);
            $props = $result['props'];
            $attributes = $result['attributes'];

            return "<?php component()->start('{$name}', {$props}, {$attributes}); ?>{$slot}<?php echo component()->end(); ?>";
        }, $content);
    }

    // protected function parseComponentAttributes(string $attrString): string
    // {
    //     $attrs = [];
    //     preg_match_all('/([a-z][a-z0-9\-]*)(?:\s*=\s*(["\'])(.*?)\2)?/i', $attrString, $matches, PREG_SET_ORDER);
    //     foreach ($matches as $m) {
    //         $name = $m[1];
    //         $value = $m[3] ?? 'true';
    //         if (strpos($attrString, ':' . $name) !== false) {
    //             // ویژگی داینامیک: مقدار را بدون کوتیشن نگه دار
    //             $attrs[] = "'{$name}' => {$value}";
    //         } else {
    //             $attrs[] = "'{$name}' => '" . addslashes($value) . "'";
    //         }
    //     }
    //     return '[' . implode(', ', $attrs) . ']';
    // }
    // protected function parseComponentAttributes(string $attrString): string
    // {
    //     $attrs = [];
    //     preg_match_all('/([a-z][a-z0-9\-]*)(?:\s*=\s*(["\'])(.*?)\2)?/i', $attrString, $matches, PREG_SET_ORDER);

    //     foreach ($matches as $m) {
    //         $name = $m[1];
    //         $value = $m[3] ?? 'true';

    //         // تشخیص ویژگی داینامیک (با : شروع می‌شود)
    //         // باید بررسی کنیم که آیا در attrString، :name وجود دارد
    //         $isDynamic = preg_match('/:\s*' . preg_quote($name, '/') . '\s*=/', $attrString);

    //         if ($isDynamic) {
    //             // ویژگی داینامیک: مقدار را به صورت عبارت PHP نگه دار
    //             $attrs[] = "'{$name}' => {$value}";
    //         } else {
    //             // ویژگی استاتیک: مقدار را در کوتیشن قرار بده
    //             $attrs[] = "'{$name}' => '" . addslashes($value) . "'";
    //         }
    //     }
    //     error_log('[ -----------------Parser Attr---------------------- ]');
    //     error_log('[' . $attrString . ']');
    //     error_log('[' . implode(', ', $attrs) . ']');
    //     error_log('[ -----------------Parser Attr---------------------- ]');
    //     return '[' . implode(', ', $attrs) . ']';
    // }

    protected function parseComponentAttributes(string $attrString): array
    {
        $props = [];
        $attributes = [];

        // الگوی مشابه parseComponentAttributes ولی با تشخیص :
        preg_match_all('/([a-z][a-z0-9\-]*)(?:\s*=\s*(["\'])(.*?)\2)?/i', $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $name = $m[1];
            $value = $m[3] ?? 'true';

            // تشخیص ویژگی داینامیک (با :) 
            $isDynamic = strpos($attrString, ':' . $name) !== false;

            if ($isDynamic) {
                // ویژگی داینامیک => به عنوان prop با مقدار خام PHP
                $props[] = "'{$name}' => {$value}";
            } else {
                // ویژگی استاتیک => به عنوان attribute معمولی
                if ($name === 'class') {
                    // کلاس را به صورت ویژه نگه می‌داریم
                    $attributes['class'] = $value;
                } else {
                    $attributes[$name] = $value;
                }
            }
        }

        // تبدیل attributes به رشته آرایه PHP
        $attrsArray = [];
        foreach ($attributes as $k => $v) {
            $attrsArray[] = "'{$k}' => '" . addslashes($v) . "'";
        }

        return [
            'props' => '[' . implode(', ', $props) . ']',
            'attributes' => '[' . implode(', ', $attrsArray) . ']'
        ];
    }
}
