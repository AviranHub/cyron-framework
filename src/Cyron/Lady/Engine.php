<?php

namespace Cyron\Lady;

use Cyron\Lady\Compiler;

class Engine
{
    protected Compiler $compiler;
    protected array $pushes = [];
    protected array $pushStack = [];
    protected string $cachePath;
    protected array $paths = [];

    public function __construct(Compiler $compiler, string $cachePath, array $paths = [])
    {
        $this->compiler = $compiler;
        $this->cachePath = rtrim($cachePath, '/');
        $this->paths = $paths;
    }

    protected function injectErrorScript($html, $fieldErrors)
    {
        $script = $this->generateErrorScript($fieldErrors);
        return str_replace('</body>', $script . '</body>', $html);
    }

    protected function generateErrorScript($fieldErrors)
    {
        $jsonErrors = json_encode($fieldErrors);
        return <<<JS
<script>
(function() {
    var fieldErrors = {$jsonErrors};
    for (var fieldName in fieldErrors) {
        if (fieldErrors.hasOwnProperty(fieldName)) {
            var errorMessage = fieldErrors[fieldName][0];
            var input = document.querySelector('[name="' + fieldName + '"]');
            if (input) {
                var errorDiv = document.createElement('div');
                errorDiv.className = 'auto-error-message text-red-500 text-sm mt-1';
                errorDiv.innerText = errorMessage;
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
        }
    }
})();
</script>
JS;
    }

    public function renderView(string $viewName, array $data = []): string
    {
        if (isset($_SESSION['_flash'])) {
            $data = array_merge($data, $_SESSION['_flash']);
            unset($_SESSION['_flash']);
        }
        $compiledPath = $this->getCompiledPath($viewName);
        return $this->renderFromPath($compiledPath, $data);
    }

    public function renderFromPath(string $compiledPath, array $data = []): string
    {
        extract($data);
        $__data = $data;
        ob_start();
        $__env = $this;

        try {
            include $compiledPath;
        } catch (\Throwable $e) {
            ob_end_clean();
            \Cyron\Exceptions\Handler::handle($e);
            exit;
        }

        $content = ob_get_clean();

        $lastError = error_get_last();
        if ($lastError && $lastError['type'] === E_ERROR) {
            $errorException = new \ErrorException(
                $lastError['message'],
                0,
                $lastError['type'],
                $lastError['file'],
                $lastError['line']
            );
            \Cyron\Exceptions\Handler::handle($errorException);
            exit;
        }

        if (isset($__data['errors']) && $__data['errors'] instanceof \Cyron\Http\ErrorBag) {
            $fieldErrors = $__data['errors']->fieldErrors();
            if (!empty($fieldErrors)) {
                $content = $this->injectErrorScript($content, $fieldErrors);
            }
        }

        return $content;
    }

    public function render(string $viewPath, array $data = []): string
    {
        $compiledPath = $this->compiler->compile($viewPath);
        return $this->renderFromPath($compiledPath, $data);
    }

    public function getCompiledPath(string $viewName): string
    {
        $relativePath = str_replace('.', '/', $viewName);
        $paths = [];
        foreach ($this->paths as $path) {
            $paths[] = $path . '/' . $relativePath . '.lady.php';
            $paths[] = $path . '/' . $relativePath . '.php';
        }
        $sourceFile = null;
        foreach ($paths as $basePath) {
            if (file_exists($basePath)) {
                $sourceFile = $basePath;
                break;
            }
        }

        if (!$sourceFile) {
            throw new \Exception("View source not found: {$viewName} (searched in " . implode(', ', $paths) . ")");
        }

        $cacheKey = md5($sourceFile);
        $cachedPath = $this->cachePath . '/' . $cacheKey . '.php';

        if (file_exists($cachedPath) && filemtime($cachedPath) >= filemtime($sourceFile)) {
            return $cachedPath;
        }

        return $this->compiler->compile($sourceFile);
    }

    public function findCompiledPath(string $templateName)
    {
        $relativePath = str_replace('.', '/', $templateName);
        $paths = [RESOURCES_PATH . '/' . $relativePath . '.lady.php', RESOURCES_PATH . '/' . $relativePath . '.php'];
        $sourceFile = null;
        foreach ($paths as $basePath) {
            if (file_exists($basePath)) {
                $sourceFile = $basePath;
                break;
            }
        }

        if (!$sourceFile) {
            throw new \Exception("View source not found: {$templateName} (searched in " . implode(', ', $this->paths) . ")");
        }

        $cacheKey = md5($templateName);
        $cachedPath = $this->cachePath . '/' . $cacheKey . '.php';

        if (file_exists($cachedPath) && filemtime($cachedPath) >= filemtime($sourceFile)) {
            return $cachedPath;
        }
    }

    public function startPush(string $name): void
    {
        $this->pushStack[] = $name;
        ob_start();
    }

    public function endPush(): void
    {
        $name = array_pop($this->pushStack);
        $content = ob_get_clean();
        if (!isset($this->pushes[$name])) {
            $this->pushes[$name] = [];
        }
        $this->pushes[$name][] = $content;
    }

    public function renderPush(string $name): void
    {
        if (isset($this->pushes[$name])) {
            echo implode('', $this->pushes[$name]);
        }
    }
}
