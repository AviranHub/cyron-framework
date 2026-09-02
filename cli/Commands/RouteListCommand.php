<?php

class RouteListCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
        Colors::enable();
    }

    public static function getDescription()
    {
        return "List all registered routes";
    }

    public function execute()
    {
        // بارگذاری روتر و روت‌ها
        require_once __DIR__ . '/../../app/router.php';
        require_once __DIR__ . '/../../routes/web.php';

        $router = \App\Route::getInstance();
        $reflection = new ReflectionClass($router);

        // دریافت آرایه روت‌ها
        $propRoutes = $reflection->getProperty('routes');
        $propRoutes->setAccessible(true);
        $routes = $propRoutes->getValue($router);

        // دریافت نام‌گذاری‌ها (اختیاری)
        $propNamed = $reflection->getProperty('namedRoutes');
        $propNamed->setAccessible(true);
        $namedRoutes = $propNamed->getValue($router);

        // عرض ستون‌ها (قابل تنظیم)
        $methodWidth = 6;
        $uriWidth = 40;
        $actionWidth = 70;
        $nameWidth = 30;
        $totalWidth = $methodWidth + $uriWidth + $actionWidth + $nameWidth + 6;

        echo "\n\n";
        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");
        printf(
            "  %-{$methodWidth}s | %-{$uriWidth}s | %-{$actionWidth}s | %-{$nameWidth}s\n",
            "METHOD",
            "URI",
            "ACTION",
            "NAME"
        );
        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");

        $total = 0;
        foreach ($routes as $route) {
            $method = $route['method'];
            $uri = $route['uri'];
            $action = $route['action'];
            $name = $route['name'] ?? '';

            // تبدیل action به رشته خوانا
            $actionStr = $this->formatAction($action);

            $coloredMethod = $this->colorMethod($method);
            $coloredUri = $this->highlightVariables($uri);
            $coloredAction = Colors::dim($actionStr);
            $coloredName = $name ? Colors::dim($name) : '';

            $this->printRow(
                $coloredMethod,
                $coloredUri,
                $coloredAction,
                $coloredName,
                $methodWidth,
                $uriWidth,
                $actionWidth,
                $nameWidth
            );
            $total++;
        }

        echo Colors::brightCyan("  " . str_repeat('─', $totalWidth) . "\n");
        echo Colors::brightGreen("  Total routes: " . $total) . "\n\n";
    }

    /**
     * تبدیل action به رشته قابل نمایش
     */
    protected function formatAction($action): string
    {
        if (is_array($action) && count($action) === 2) {
            // [Controller::class, method]
            $controller = is_object($action[0]) ? get_class($action[0]) : $action[0];
            // حذف prefix تکراری App\Http\Controllers\ (اگر وجود داشته باشد)
            $controller = str_replace('App\\Http\\Controllers\\', '', $controller);
            return Colors::purple400($controller) . Colors::pink500('@') . Colors::blue400($action[1]);
        }
        if (is_string($action)) {
            // view name
            return "view: {$action}";
        }
        if (is_callable($action)) {
            return 'Closure';
        }
        return '?';
    }

    /**
     * چاپ یک سطر با رعایت عرض ستون‌ها و کدهای رنگی
     */
    protected function printRow(
        $coloredMethod,
        $coloredUri,
        $coloredAction,
        $coloredName,
        $methodWidth,
        $uriWidth,
        $actionWidth,
        $nameWidth
    ) {
        $methodLen = $this->visibleLength($coloredMethod);
        $uriLen = $this->visibleLength($coloredUri);
        $actionLen = $this->visibleLength($coloredAction);
        $nameLen = $this->visibleLength($coloredName);

        $methodPadded = $coloredMethod . str_repeat(' ', max(0, $methodWidth - $methodLen));
        $uriPadded = $coloredUri . str_repeat(' ', max(0, $uriWidth - $uriLen));
        $actionPadded = $coloredAction . str_repeat(' ', max(0, $actionWidth - $actionLen));
        $namePadded = $coloredName . str_repeat(' ', max(0, $nameWidth - $nameLen));

        echo "  " . $methodPadded . " | " . $uriPadded . " | " . $actionPadded . " | " . $namePadded . "\n";
    }

    /**
     * محاسبه طول قابل مشاهده (بدون کدهای ANSI)
     */
    protected function visibleLength($str)
    {
        return strlen(preg_replace('/\x1b\[[0-9;]*m/', '', $str));
    }

    /**
     * رنگ‌بندی متغیرهای URI (مثل {id} و {slug})
     */
    protected function highlightVariables($uri)
    {
        return preg_replace_callback('/\{([a-zA-Z0-9_]+)(\?)?\}/', function ($matches) {
            return Colors::cyan('{' . $matches[1] . ($matches[2] ?? '') . '}');
        }, $uri);
    }

    /**
     * رنگ‌بندی متد HTTP
     */
    protected function colorMethod($method)
    {
        switch ($method) {
            case 'GET':
                return Colors::green($method);
            case 'POST':
                return Colors::yellow($method);
            case 'PUT':
                return Colors::blue($method);
            case 'PATCH':
                return Colors::blue($method);
            case 'DELETE':
                return Colors::red($method);
            default:
                return Colors::magenta($method);
        }
    }
}
