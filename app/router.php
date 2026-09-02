<?php

namespace App;

class Route
{
    protected static $instance = null;
    protected static $fallback = null;
    protected static array $globalMiddlewares = [];

    protected $routes = [];
    protected $currentGroup = [];
    protected $namedRoutes = [];
    protected $lastRoute = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function fallback($callback)
    {
        self::$fallback = $callback;
    }

    public static function globalMiddleware(...$middlewares)
    {
        self::$globalMiddlewares = array_merge(self::$globalMiddlewares, $middlewares);
    }

    // ================ متدهای ثبت روت ================
    public static function get($uri, $action)
    {
        return self::addRoute('GET', $uri, $action);
    }
    public static function post($uri, $action)
    {
        return self::addRoute('POST', $uri, $action);
    }
    public static function put($uri, $action)
    {
        return self::addRoute('PUT', $uri, $action);
    }
    public static function patch($uri, $action)
    {
        return self::addRoute('PATCH', $uri, $action);
    }
    public static function delete($uri, $action)
    {
        return self::addRoute('DELETE', $uri, $action);
    }
    public static function options($uri, $action)
    {
        return self::addRoute('OPTIONS', $uri, $action);
    }
    public static function any($uri, $action)
    {
        return self::addRoute('ANY', $uri, $action);
    }

    protected static function addRoute($method, $uri, $action)
    {
        $instance = self::getInstance();
        $prefix = $instance->currentGroup['prefix'] ?? '';
        // $fullUri = rtrim($prefix . '/' . ltrim($uri, '/'), '/');
        // if ($fullUri === '') $fullUri = '/';
        $fullUri = rtrim($prefix . '/' . ltrim($uri, '/'), '/');
        if ($fullUri === '') $fullUri = '/';
        // اضافه کردن اسلش ابتدایی
        if (substr($fullUri, 0, 1) !== '/') {
            $fullUri = '/' . $fullUri;
        }

        $route = [
            'method' => $method,
            'uri' => $fullUri,
            'action' => $action,
            'name' => null,
            'middlewares' => $instance->currentGroup['middlewares'] ?? [],
            'where' => [],
        ];

        $instance->routes[] = $route;
        $instance->lastRoute = &$instance->routes[count($instance->routes) - 1];
        return $instance;
    }

    // ================ تنظیمات روت جاری ================
    public static function name($name)
    {
        $instance = self::getInstance();
        if (isset($instance->lastRoute)) {
            $instance->lastRoute['name'] = $name;
            $instance->namedRoutes[$name] = $instance->lastRoute['uri'];
        }
        return $instance;
    }

    public static function middleware(...$middlewares)
    {
        $instance = self::getInstance();
        if (isset($instance->lastRoute)) {
            $instance->lastRoute['middlewares'] = array_merge($instance->lastRoute['middlewares'], $middlewares);
        }
        return $instance;
    }

    public static function where($params)
    {
        return new static();
    }

    // ================ گروه‌بندی ================
    public static function group($attributes, $callback)
    {
        $instance = self::getInstance();
        $oldGroup = $instance->currentGroup;

        $newGroup = [];
        if (isset($attributes['prefix'])) {
            $newGroup['prefix'] = ($oldGroup['prefix'] ?? '') . '/' . trim($attributes['prefix'], '/');
            $newGroup['prefix'] = ltrim($newGroup['prefix'], '/');
            if ($newGroup['prefix'] === '') $newGroup['prefix'] = '';
        } else {
            $newGroup['prefix'] = $oldGroup['prefix'] ?? '';
        }

        if (isset($attributes['middleware'])) {
            $newGroup['middlewares'] = array_merge($oldGroup['middlewares'] ?? [], (array)$attributes['middleware']);
        } else {
            $newGroup['middlewares'] = $oldGroup['middlewares'] ?? [];
        }

        if (isset($attributes['namespace'])) {
            $newGroup['namespace'] = $attributes['namespace'];
        } else {
            $newGroup['namespace'] = $oldGroup['namespace'] ?? '';
        }

        $instance->currentGroup = $newGroup;
        call_user_func($callback);
        $instance->currentGroup = $oldGroup;
    }

    public static function prefix($prefix)
    {
        return new class($prefix) {
            protected $prefix;
            public function __construct($prefix)
            {
                $this->prefix = $prefix;
            }
            public function group($callback)
            {
                Route::group(['prefix' => $this->prefix], $callback);
            }
        };
    }

    // ================ اجرا و دیسپچ ================
    public static function run()
    {
        $instance = self::getInstance();
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';


        // ========== اضافه شد: پشتیبانی از _method برای PUT/DELETE ==========
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        // =================================================================

        foreach ($instance->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') continue;

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)(\?)?\}/', '(?P<$1>[^/]+)$2', $route['uri']);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $middlewares = array_merge(self::$globalMiddlewares, $route['middlewares']);
                $request = new Request();
                $next = function ($request) use ($route, $params) {
                    return self::executeAction($route['action'], $params, $request);
                };
                $pipeline = array_reduce($middlewares, function ($stack, $mw) {
                    return function ($request) use ($mw, $stack) {
                        $middlewareClass = $mw;
                        $parameters = [];

                        if (is_string($mw) && str_contains($mw, ':')) {
                            [$middlewareClass, $parameterString] = explode(':', $mw, 2);
                            $parameters = $parameterString === '' ? [] : array_map('trim', explode(',', $parameterString));
                        }

                        if (!class_exists($middlewareClass)) {
                            throw new \RuntimeException('Middleware class not found: ' . $middlewareClass);
                        }

                        $reflection = new \ReflectionClass($middlewareClass);
                        $middleware = $reflection->newInstanceArgs($parameters);
                        return $middleware->handle($request, $stack);
                    };
                }, $next);
                $response = $pipeline($request);

                // ========== اصلاح مهم: بررسی شیء Redirect ==========
                if ($response !== null) {
                    if (is_object($response) && method_exists($response, 'send')) {
                        $response->send();
                    } else {
                        echo $response;
                    }
                }
                return;
            }
        }

        // اگر هیچ روتی پیدا نشد
        http_response_code(404);
        if (self::$fallback && is_callable(self::$fallback)) {
            echo call_user_func(self::$fallback);
        } else {
            // پیش‌فرض: نمایش ویو 404 اگر وجود داشته باشد
            if (view_exists('errors.404')) {
                echo view('errors.404');
            } else {
                echo "404 - صفحه مورد نظر یافت نشد";
            }
        }
        return;
    }

    // protected static function executeAction($action, $params)
    // {
    //     if (is_string($action)) {
    //         // view
    //         return view($action, $params);
    //     }
    //     if (is_array($action) && count($action) === 2) {
    //         // [Controller::class, method]
    //         $controller = new $action[0]();
    //         return call_user_func_array([$controller, $action[1]], $params);
    //     }
    //     if (is_callable($action)) {
    //         return call_user_func_array($action, $params);
    //     }
    //     return null;
    // }

    // protected static function executeAction($action, $routeParams)
    // {
    //     if (is_string($action)) {
    //         return view($action, $routeParams);
    //     }
    //     if (is_array($action) && count($action) === 2) {
    //         $controller = new $action[0]();
    //         $method = $action[1];

    //         $reflection = new \ReflectionMethod($controller, $method);
    //         $args = [];
    //         $routeParamsCopy = $routeParams; // برای پارامترهای ساده

    //         foreach ($reflection->getParameters() as $param) {
    //             $paramType = $param->getType();
    //             $paramName = $param->getName();

    //             // 1. تزریق Request (هر جا که باشد)
    //             if ($paramType && $paramType->getName() === 'App\Request') {
    //                 $args[] = new \App\Request();
    //                 continue;
    //             }

    //             // 2. تزریق مدل‌ها (Route Model Binding)
    //             if ($paramType && !$paramType->isBuiltin()) {
    //                 $className = $paramType->getName();
    //                 if (class_exists($className) && is_subclass_of($className, 'App\Database\Model')) {
    //                     // پیدا کردن مقدار پارامتر مسیر با همان نام (مثلاً {user})
    //                     if (isset($routeParams[$paramName])) {
    //                         $model = $className::find($routeParams[$paramName]);
    //                         if (!$model) {
    //                             abort(404, "{$className} not found");
    //                         }
    //                         $args[] = $model;
    //                         // حذف این پارامتر از لیست ساده‌ها تا دوباره استفاده نشود
    //                         unset($routeParamsCopy[$paramName]);
    //                         continue;
    //                     }
    //                 }
    //             }

    //             // 3. پارامترهای ساده (مانند $id, $slug) – به ترتیب از routeParams برداشته می‌شوند
    //             $args[] = array_shift($routeParamsCopy);
    //         }

    //         return $reflection->invokeArgs($controller, $args);
    //     }
    //     if (is_callable($action)) {
    //         return call_user_func_array($action, $routeParams);
    //     }
    //     return null;
    // }

    protected static function executeAction($action, $routeParams, $request = null)
    {
        if (is_string($action)) {
            return view($action, $routeParams);
        }
        if (is_array($action) && count($action) >= 2) {
            $controllerClass = $action[0];
            $method = $action[1];
            $constructorArgs = $action[2] ?? []; // آرگومان‌های سازنده (اختیاری)

            // ایجاد نمونه کنترلر با آرگومان‌های سازنده (در صورت وجود)
            $reflectionClass = new \ReflectionClass($controllerClass);
            $controller = $reflectionClass->newInstanceArgs($constructorArgs);

            // بازتاب متد برای تزریق پارامترهای درخواست و مدل
            $reflectionMethod = new \ReflectionMethod($controller, $method);
            $args = [];
            $routeParamsCopy = $routeParams;

            foreach ($reflectionMethod->getParameters() as $param) {
                $paramType = $param->getType();
                $paramName = $param->getName();

                // 1. تزریق Request
                if ($paramType && $paramType->getName() === 'App\Request') {
                    $args[] = $request ?? new \App\Request();
                    continue;
                }

                // 2. تزریق مدل (Route Model Binding)
                if ($paramType && !$paramType->isBuiltin()) {
                    $className = $paramType->getName();
                    if (class_exists($className) && is_subclass_of($className, 'App\Database\Model')) {
                        if (isset($routeParams[$paramName])) {
                            $model = $className::find($routeParams[$paramName]);
                            if (!$model) {
                                abort(404, "{$className} not found");
                            }
                            $args[] = $model;
                            unset($routeParamsCopy[$paramName]);
                            continue;
                        }
                    }
                }

                // 3. پارامترهای ساده (از مسیر)
                $args[] = array_shift($routeParamsCopy);
            }

            return $reflectionMethod->invokeArgs($controller, $args);
        }
        if (is_callable($action)) {
            return call_user_func_array($action, $routeParams);
        }
        return null;
    }


    // ================ ابزارهای کمکی ================
    public static function url($name, $params = [])
    {
        if (!is_array($params)) $params = [];

        $instance = self::getInstance();
        if (!isset($instance->namedRoutes[$name])) return '#';
        $uri = $instance->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
            $uri = str_replace('{' . $key . '?}', $value, $uri);
        }
        $uri = preg_replace('/\{[a-zA-Z0-9_]+\?\}/', '', $uri);
        $uri = str_replace('//', '/', $uri);
        return $uri;
    }

    public static function getRoutes()
    {
        return self::getInstance()->routes;
    }
    public static function getNamedRoutes()
    {
        return self::getInstance()->namedRoutes;
    }
    public static function auth()
    {
        return self::middleware(\App\Http\Middlewares\AuthMiddleware::class);
    }

    public static function permission(string $ability)
    {
        return self::middleware(\App\Http\Middlewares\PermissionMiddleware::class . ':' . $ability);
    }

    public static function currentRouteName()
    {
        $instance = self::getInstance();
        foreach ($instance->routes as $route) {
            if (isset($route['name']) && $route['uri'] === $instance->getCurrentUri()) {
                return $route['name'];
            }
        }
        return null;
    }

    protected static function getCurrentUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    public static function is($pattern)
    {
        $name = self::currentRouteName();
        if (!$name) return false;
        if ($pattern === $name) return true;
        if (str_ends_with($pattern, '.*')) {
            $patternBase = substr($pattern, 0, -2);
            return str_starts_with($name, $patternBase);
        }
        return false;
    }
}
