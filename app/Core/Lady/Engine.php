<?php
// App/Core/Lady/Engine.php

namespace App\Core\Lady;

class Engine
{
    protected Compiler $compiler;
    protected array $pushes = [];
    protected array $pushStack = [];
    protected string $cachePath;
    protected array $paths = [];      // مسیرهای جستجوی ویوها

    public function __construct(Compiler $compiler, string $cachePath, array $paths = [])
    {
        $this->compiler = $compiler;
        $this->cachePath = rtrim($cachePath, '/');
        $this->paths = $paths;
    }


    protected function injectErrorScript($html, $fieldErrors)
    {
        $script = $this->generateErrorScript($fieldErrors);
        // اسکریپت را قبل از </body> تزریق کن
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
                // قرار دادن بعد از input
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
        }
    }
})();
</script>
JS;
    }

    /**
     * رندر ویو با نام نقطه‌دار (مثلاً 'home' یا 'admin.dashboard')
     */
    public function renderView(string $viewName, array $data = []): string
    {
        if (isset($_SESSION['_flash'])) {
            $data = array_merge($data, $_SESSION['_flash']);
            unset($_SESSION['_flash']);
        }
        $compiledPath = $this->getCompiledPath($viewName);
        return $this->renderFromPath($compiledPath, $data);
    }

    /**
     * رندر از یک فایل کامپایل شده (مسیر فیزیکی)
     */
    // public function renderFromPath(string $compiledPath, array $data = []): string
    // {
    //     extract($data);
    //     $__data = $data;
    //     ob_start();
    //     $__env = $this;
    //     include $compiledPath;
    //     $content = ob_get_clean();

    //     // اضافه کردن خودکار اسکریپت خطاها در صورت وجود
    //     if (isset($__data['errors']) && $__data['errors'] instanceof \App\Http\ErrorBag) {
    //         $fieldErrors = $__data['errors']->fieldErrors();
    //         if (!empty($fieldErrors)) {
    //             $content = $this->injectErrorScript($content, $fieldErrors);
    //         }
    //     }

    //     return $content;
    // }
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
            // خطا را به Exception Handler اصلی بفرست
            \App\Core\Exceptions\Handler::handle($e);
            exit;
        }

        $content = ob_get_clean();

        // بررسی وجود خطاهای PHP (مانند undefined variable) که استثنا نشده‌اند
        $lastError = error_get_last();
        if ($lastError && $lastError['type'] === E_ERROR) {
            // تبدیل خطای مرگبار به استثنا
            $errorException = new \ErrorException(
                $lastError['message'],
                0,
                $lastError['type'],
                $lastError['file'],
                $lastError['line']
            );
            \App\Core\Exceptions\Handler::handle($errorException);
            exit;
        }

        // اضافه کردن خودکار اسکریپت خطاها (قبلاً نوشته شده)
        if (isset($__data['errors']) && $__data['errors'] instanceof \App\Http\ErrorBag) {
            $fieldErrors = $__data['errors']->fieldErrors();
            if (!empty($fieldErrors)) {
                $content = $this->injectErrorScript($content, $fieldErrors);
            }
        }

        return $content;
    }


    /**
     * رندر با مسیر فایل منبع (برای سازگاری)
     */
    public function render(string $viewPath, array $data = []): string
    {
        $compiledPath = $this->compiler->compile($viewPath);
        return $this->renderFromPath($compiledPath, $data);
    }

    /**
     * پیدا کردن و کامپایل کردن ویو بر اساس نام، برگرداندن مسیر کش
     */
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
        // error_log("source file : {$sourceFile}");

        if (!$sourceFile) {
            throw new \Exception("View source not found: {$viewName} (searched in " . implode(', ', $paths) . ")");
        }

        $cacheKey = md5($sourceFile);
        $cachedPath = $this->cachePath . '/' . $cacheKey . '.php';

        // بررسی نیاز به کامپایل مجدد (در صورت تغییر فایل منبع)
        if (file_exists($cachedPath) && filemtime($cachedPath) >= filemtime($sourceFile)) {
            // error_log("\n - return cashed : {$cachedPath} - \n");
            return $cachedPath;
        }
        // error_log("------ Compile : {$sourceFile}");

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

        // error_log("source file : {$sourceFile}");

        if (!$sourceFile) {
            throw new \Exception("View source not found: {$templateName} (searched in " . implode(', ', $this->paths) . ")");
        }

        $cacheKey = md5($templateName);
        $cachedPath = $this->cachePath . '/' . $cacheKey . '.php';

        // بررسی نیاز به کامپایل مجدد (در صورت تغییر فایل منبع)
        if (file_exists($cachedPath) && filemtime($cachedPath) >= filemtime($sourceFile)) {
            return $cachedPath;
        }
    }

    /**
     * شروع یک push (برای stack)
     */
    public function startPush(string $name): void
    {
        $this->pushStack[] = $name;
        ob_start();
    }

    /**
     * پایان push و ذخیره محتوا
     */
    public function endPush(): void
    {
        $name = array_pop($this->pushStack);
        $content = ob_get_clean();
        if (!isset($this->pushes[$name])) {
            $this->pushes[$name] = [];
        }
        $this->pushes[$name][] = $content;
    }

    /**
     * خروجی تمام push‌های یک stack
     */
    public function renderPush(string $name): void
    {
        if (isset($this->pushes[$name])) {
            echo implode('', $this->pushes[$name]);
        }
    }
}
