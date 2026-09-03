<?php
namespace App\Core\Lady;

class Parser
{
    public function parse(string $content): string
    {
        // Preserve @verbatim blocks from every compiler pass.
        $verbatim = [];
        $content = preg_replace_callback('/@verbatim\s*(.*?)\s*@endverbatim/s', function ($matches) use (&$verbatim) {
            $key = '__LADY_VERBATIM_' . count($verbatim) . '__';
            $verbatim[$key] = $matches[1];
            return $key;
        }, $content);

        $content = $this->compileComments($content);
        $content = $this->compileSwitchBlocks($content);
        $content = $this->compileComponentTags($content);
        $content = $this->compileRawPhp($content);
        $content = $this->compileConditionalsAndLoops($content);
        $content = $this->compileEchos($content);

        foreach ($verbatim as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

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

    protected function compileEchos(string $content): string
    {
        $content = preg_replace('/\{\{\s*(.+?)\s*\}}/s', '<?= htmlspecialchars($1 ?? "", ENT_QUOTES, "UTF-8") ?>', $content);
        $content = preg_replace('/\{\!!\s*(.+?)\s*!!\}/s', '<?= $1 ?>', $content);
        return $content;
    }

    protected function compileRawPhp(string $content): string
    {
        return preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1; ?>', $content);
    }

    protected function compileComments(string $content): string
    {
        return preg_replace('/\{\{--.*?--\}\}/s', '', $content);
    }

    protected function compileSwitchBlocks(string $content): string
    {
        $pattern = '/@switch\s*\(\s*(.+?)\s*\)(.*?)@endswitch/s';
        return preg_replace_callback($pattern, function ($matches) {
            $condition = $matches[1];
            $body = $matches[2];
            $body = preg_replace_callback('/@case\s*\(\s*(.+?)\s*\)/s', function ($m) {
                return 'case ' . $m[1] . ": echo '";
            }, $body);
            $d = str_contains($body, '@default') ? "';" : "";
            $body = preg_replace('/@default\b/', "default: echo '", $body);
            $body = preg_replace('/@break\s*;?\s*/', "'; break;", $body);
            return "<?php switch ({$condition}): {$body} $d endswitch; ?>";
        }, $content);
    }

    protected function compileComponentTags(string $content): string
    {
        $pattern = '/<x-([a-z][a-z0-9\-]*)\s*((?:[^>\'"]|"[^"]*"|\'[^\']*\')*)>(.*?)<\/x-\1>/is';
        return preg_replace_callback($pattern, function ($matches) {
            $name = $matches[1];
            $attrString = $matches[2];
            $slot = $matches[3];
            $result = $this->parseComponentAttributes($attrString);
            return "<?php component()->start('{$name}', {$result['props']}, {$result['attributes']}); ?>{$slot}<?php echo component()->end(); ?>";
        }, $content);
    }

    protected function parseComponentAttributes(string $attrString): array
    {
        $props = [];
        $attributes = [];
        preg_match_all('/([a-z][a-z0-9\-]*)(?:\s*=\s*(["\'])(.*?)\2)?/i', $attrString, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $name = $m[1];
            $value = $m[3] ?? 'true';
            $isDynamic = strpos($attrString, ':' . $name) !== false;
            if ($isDynamic) {
                $props[] = "'{$name}' => {$value}";
            } else {
                $attributes[$name] = $value;
            }
        }
        $attrsArray = [];
        foreach ($attributes as $k => $v) {
            $attrsArray[] = "'{$k}' => '" . addslashes($v) . "'";
        }
        return ['props' => '[' . implode(', ', $props) . ']', 'attributes' => '[' . implode(', ', $attrsArray) . ']'];
    }
}
