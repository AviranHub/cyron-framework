<?php


use App\Core\Lady\Engine;
use App\Core\Lady\Compiler;
use App\Core\Lady\Parser;
use App\Request;
use App\Response;
use App\Str;
use App\Route;

if (!function_exists('random')) {
    function random($a, $z)
    {
        return rand($a, $z);
    }
}

if (!function_exists('vars')) {
    function vars($key)
    {
        if (defined($key)) {
            return constant($key);
        }
        return null;
    }
}

if (!function_exists('load')) {
    function load($folder_name, $file_name = null, $attributes = null)
    {
        // استفاده از مسیر مطلق
        $basePath = defined('RESOURCES_PATH') ? RESOURCES_PATH : (defined('BASE_PATH') ? BASE_PATH . '/resources' : './resources');
        $folder_path = $basePath . '/' . $folder_name;

        if (!is_dir($folder_path)) {
            return '';
        }

        if ($file_name) {
            return $folder_path . '/' . $file_name;
        } else {
            $files = array_diff(scandir($folder_path), ['.', '..']);
            $output = '';
            foreach ($files as $folder_file) {
                if ($folder_name == 'Styles') {
                    $output .= "<link rel=\"stylesheet\" href=\"$folder_path/$folder_file\" $attributes>\n";
                }
                if ($folder_name == 'Scripts') {
                    $output .= "<script src=\"$folder_path/$folder_file\" $attributes></script>\n";
                }
            }
            return $output;
        }
    }
}

if (!function_exists('asset')) {
    function asset($file_path)
    {
        // در ساختار جدید، asset ها از public پوشه سرو میشن
        echo '/assets/' . ltrim($file_path, '/');
    }
}

if (!function_exists('arrayToObject')) {
    function arrayToObject($array)
    {
        return json_decode(json_encode($array));
    }
}

// if (!function_exists('view')) {
//     function view($name, $datas = null) {
//         // استفاده از مسیر مطلق
//         $basePath = defined('RESOURCES_PATH') ? RESOURCES_PATH : (defined('BASE_PATH') ? BASE_PATH . '/resources' : './resources');

//         $callback = $name;
//         if (!is_callable($callback)) {
//             if (!strpos($name, '.php')) {
//                 $name .= '.lady.php';
//             }
//         }

//         // تبدیل آرایه به شیء اگر $datas آرایه باشد
//         if (is_array($datas)) {
//             $datas = arrayToObject($datas);
//         }

//         // اگر $datas شیء است، ویژگی‌های آن را استخراج کنید
//         if (isset($datas)) {
//             foreach ($datas as $key => $value) {
//                 $$key = $value;
//             }
//         }

//         $viewPath = $basePath . '/app/Views/' . $name;

//         if (file_exists($viewPath)) {
//             include_once $viewPath;
//         } else {
//             echo "View not found: " . $viewPath;
//         }
//         exit();
//     }
// }



// if (!function_exists('view')) {
//     function view(string $name, array $data = [])
//     {
//         // مسیر پایه resources
//         $resourcesPath = defined('RESOURCES_PATH') ? RESOURCES_PATH : (defined('BASE_PATH') ? BASE_PATH . '/resources' : __DIR__ . '/../resources');
//         $cachePath = defined('CACHE_PATH') ? CACHE_PATH . '/views' : (defined('BASE_PATH') ? BASE_PATH . '/storage/cache/views' : __DIR__ . '/../storage/cache/views');

//         if (!is_dir($cachePath)) {
//             mkdir($cachePath, 0755, true);
//         }

//         print("-Ok View function paths : {$resourcesPath} -- {$cachePath}");

//         // مسیر فایل lady
//         $viewFile = $resourcesPath . '/Views/' . str_replace('.', '/', $name) . '.lady.php';
//         if (!file_exists($viewFile)) {
//             throw new Exception("View not found: {$viewFile}");
//         }

//         // ساخت اشیاء
//         $parser = new Parser();
//         $compiler = new Compiler($parser, $cachePath);
//         $engine = new Engine($compiler, $cachePath);

//         // ذخیره engine در service container برای استفاده در @include و layout
//         if (!app()->has('view.engine')) {
//             app()->instance('view.engine', $engine);
//         }

//         print("-Ok View function : {$viewFile}");

//         return $engine->render($viewFile, $data);
//     }
// }

if (!function_exists('view')) {
    function view(string $name, array $data = [])
    {
        // error_log(" * view name : {$name}");
        global $viewEngine;
        if (!$viewEngine) {
            throw new Exception('View engine not initialized. Please check bootstrap.php');
        }
        return $viewEngine->renderView($name, $data);
    }
}

if (!function_exists('dump')) {
    /**
     * خروجی زیبا از متغیرها بدون توقف اجرا
     * @param mixed ...$vars
     * @return void
     */
    function dump(...$vars)
    {
        echo '<div style="background: #f4f4f4; padding: 1rem; margin: 1rem 0; border-left: 4px solid #ff9800; font-family: monospace;">';
        foreach ($vars as $var) {
            echo '<pre style="margin: 0; overflow-x: auto;">';
            var_dump($var);
            echo '</pre>';
        }
        echo '</div>';
    }
}

if (!function_exists('dd')) {
    /**
     * خروجی زیبا از متغیرها و سپس توقف اجرا
     * @param mixed ...$vars
     * @return void
     */
    function dd(...$vars)
    {
        dump(...$vars);
        die(1);
    }
}


if (!function_exists('ob')) {
    function ob($array)
    {
        return json_decode(json_encode($array));
    }
}






if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('method_field')) {
    function method_field($method)
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('storage_url')) {
    function storage_url($path)
    {
        return '/storage/' . ltrim($path, '/');
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return \App\Core\Localization\Translator::get($key, $replace);
    }
}

if (!function_exists('locale')) {
    function locale(): string
    {
        return \App\Core\Localization\Translator::getLocale();
    }
}

if (!function_exists('is_rtl')) {
    function is_rtl(): bool
    {
        return \App\Core\Localization\Translator::isRtl();
    }
}

if (!function_exists('set_locale')) {
    function set_locale(string $locale): void
    {
        \App\Core\Localization\Translator::setLocale($locale);
    }
}


if (!function_exists('trans_choice')) {
    function trans_choice($key, $number)
    {
        return $key;
    }
}















if (!function_exists('isActive')) {
    function isActive($route)
    {
        return ($_SERVER['REQUEST_URI'] === $route);
    }
}

// if (!function_exists('route')) {
//     function route($name, $parameters = null)
//     {
//         if (class_exists('App\Route')) {
//             // استفاده از instance روتر
//             $router = \App\Route::getInstance();
//             $url = $router->url($name, $parameters ?? []);
//             echo $url;
//         } else {
//             echo $name;
//         }
//     }
// }
if (!function_exists('route')) {
    function route($name, $parameters = [])   // تغییر: مقدار پیش‌فرض [] به جای null
    {
        if (class_exists('App\Route')) {
            $router = \App\Route::getInstance();
            $url = $router->url($name, $parameters ?? []); // اطمینان از آرایه بودن
            return $url;
        } else {
            return $name;
        }
    }
}

if (!function_exists('url')) {
    function url($path)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'];
        echo $protocol . '://' . $domain . '/' . ltrim($path, '/');
    }
}

if (!function_exists('input')) {
    function input($key, $default = null)
    {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }
}

if (!function_exists('session')) {
    class Session
    {
        private static $sessionStarted = false;
        private static $lifetime;

        public function __construct($key = null)
        {
            if ($key) {
                return $this->get($key);
            }
        }

        public static function start($lifetime = 0)
        {
            if (!self::$sessionStarted) {
                self::$lifetime = $lifetime;
                if (session_status() === PHP_SESSION_NONE) {
                    session_name('PHPSESSID');
                    session_set_cookie_params(self::$lifetime);
                    session_start();
                }
                self::$sessionStarted = true;
            }
        }

        public static function set($key, $value)
        {
            self::start();
            $_SESSION[$key] = $value;
        }

        public static function has($key)
        {
            self::start();
            return isset($_SESSION[$key]);
        }

        public static function get($key)
        {
            self::start();
            return $_SESSION[$key] ?? null;
        }

        public static function delete($key)
        {
            self::start();
            unset($_SESSION[$key]);
        }

        public static function destroy()
        {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_unset();
                session_destroy();
            }
            self::$sessionStarted = false;
        }
    }

    function session($key = null, $default = null)
    {
        if ($key === null) {
            return new Session();
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('cookie')) {
    class Cookie
    {
        public static function set($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
        {
            setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        }

        public static function get($name)
        {
            return $_COOKIE[$name] ?? null;
        }

        public static function delete($name)
        {
            setcookie($name, '', time() - 3600, '/');
            unset($_COOKIE[$name]);
        }
    }

    function cookie()
    {
        return new Cookie();
    }
}

if (!function_exists('redirect')) {
    class Redirect
    {
        protected $url;
        protected $status;
        protected $withData = [];

        public static function to($url, $status = 302)
        {
            $instance = new self();
            $instance->url = $url;
            $instance->status = $status;
            return $instance;
        }

        public static function route($name, $parameters = null)
        {
            $url = \App\Route::url($name, $parameters);
            return self::to($url);
        }

        public static function back()
        {
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            return self::to($referer);
        }

        public function with($key, $value)
        {
            $this->withData[$key] = $value;
            return $this;
        }

        public function withErrors($errors)
        {
            return $this->with('errors', $errors);
        }

        public function withInput($input = null)
        {
            if ($input === null) {
                $input = $_POST;
            }
            return $this->with('old', $input);
        }

        public function intended($default = '/')
        {
            $intended = $_SESSION['intended_url'] ?? $default;
            unset($_SESSION['intended_url']);
            return $this->to($intended);
        }

        /**
         * اجرای ریدایرکت و ذخیره فلش دیتا
         */
        public function send()
        {
            // اگر داده فلش وجود دارد، در سشن ذخیره کن
            if (!empty($this->withData)) {
                // اطمینان از شروع سشن (اگر قبلاً شروع نشده)
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if (!isset($_SESSION['_flash'])) {
                    $_SESSION['_flash'] = [];
                }
                foreach ($this->withData as $key => $value) {
                    $_SESSION['_flash'][$key] = $value;
                }
            }
            // ارسال هدر ریدایرکت
            header('Location: ' . $this->url, true, $this->status);
            exit();
        }
    }

    function redirect()
    {
        return new Redirect();
    }
}

if (!function_exists('response')) {
    /**
     * تابع کمکی برای ایجاد پاسخ
     * 
     * @param mixed $content محتوای پاسخ (اختیاری)
     * @param int $status کد وضعیت
     * @param array $headers هدرها
     * @return \App\Core\Http\Response|\App\Core\Http\Response
     */
    function response(mixed $content = null, int $status = 200, array $headers = [])
    {
        if ($content === null) {
            return new Response();
        }
        return new Response($content, $status, $headers);
    }
}

if (!function_exists('request')) {
    function request()
    {
        return new Request();
    }
}

// ==================== Mail Helpers ====================

if (!function_exists('mailer')) {
    /**
     * تابع کمکی برای ایجاد نمونه‌ی Mailer
     * 
     * @return \App\Core\Mail\Mailer
     */
    function mailer()
    {
        return new \App\Core\Mail\Mailer();
    }
}

if (!function_exists('send_mail')) {
    /**
     * ارسال سریع ایمیل با یک خط کد
     * 
     * @param string $to آدرس ایمیل گیرنده
     * @param string $subject موضوع ایمیل
     * @param string $body محتوای ایمیل (HTML)
     * @param string|null $from آدرس فرستنده (اختیاری)
     * @return bool
     */
    function send_mail(string $to, string $subject, string $body, ?string $from = null): bool
    {
        return \App\Core\Mail\Mailer::quick($to, $subject, $body, $from);
    }
}

if (!function_exists('str')) {
    function str()
    {
        return new Str();
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }
}

if (!function_exists('old')) {
    /**
     * دریافت مقدار قبلی یک فیلد از درخواست (برای بازگردانی فرم)
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old($key, $default = '')
    {
        // اگر داده فلش شده باشد (مثل after redirect withInput)
        if (isset($_SESSION['_flash']['old'][$key])) {
            return $_SESSION['_flash']['old'][$key];
        }
        // در غیر این صورت از POST و GET جاری
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }
}

if (!function_exists('action')) {
    function action($class, $data = [])
    {
        return (new $class())->execute($data);
    }
}

// if (!function_exists('env')) {
//     function env($key, $default = null)
//     {
//         $value = getenv($key);
//         if ($value === false) {
//             // همچنین از $_ENV هم چک کن
//             $value = $_ENV[$key] ?? null;
//         }
//         // تبدیل مقادیر boolean و عددی
//         switch (strtolower($value)) {
//             case 'true':
//                 return true;
//             case 'false':
//                 return false;
//             case 'null':
//                 return null;
//         }
//         return $value ?? $default;
//     }
// }

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? null;
        }

        // اگر مقدار null است، مستقیماً $default را برگردان
        if ($value === null) {
            return $default;
        }

        // تبدیل به رشته برای اطمینان
        $strValue = (string)$value;

        switch (strtolower($strValue)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        return $value;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $config = [];

        // اگر کلید نقطه نداشته باشد، ابتدا ثابت‌ها و env را بررسی کن
        if (strpos($key, '.') === false) {
            // 1. ثابت (constant)
            if (defined($key)) {
                return constant($key);
            }
            // 2. متغیر محیطی (env)
            $envValue = env($key);
            if ($envValue !== null) {
                return $envValue;
            }
        }

        // پشتیبانی از dot notation (مثل app.name)
        $parts = explode('.', $key);
        $file = $parts[0];
        if (!isset($config[$file])) {
            $path = APP_PATH . '/Config/' . $file . '.php';
            if (file_exists($path)) {
                $config[$file] = require $path;
            } else {
                $config[$file] = [];
            }
        }
        $value = $config[$file];
        for ($i = 1; $i < count($parts); $i++) {
            if (!isset($value[$parts[$i]])) {
                return $default;
            }
            $value = $value[$parts[$i]];
        }
        return $value;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return new class {
            public function user()
            {
                return $_SESSION['user'] ?? null;
            }

            public function check()
            {
                return isset($_SESSION['user']);
            }

            public function guest()
            {
                return !isset($_SESSION['user']);
            }
        };
    }
}





if (!function_exists('paginate_links')) {
    function paginate_links($paginator, $pageName = 'page')
    {
        if (!$paginator['has_pages']) return '';

        $html = '<nav class="pagination"><ul class="flex justify-center space-x-2">';

        // لینک قبلی
        if ($paginator['has_prev']) {
            $url = current_url([$pageName => $paginator['prev_page']]);
            $html .= "<li><a href=\"{$url}\" class=\"px-3 py-1 border rounded\">قبلی</a></li>";
        } else {
            $html .= '<li><span class="px-3 py-1 border rounded text-gray-400">قبلی</span></li>';
        }

        // شماره صفحات (نمایش ۵ صفحه اطراف صفحه جاری)
        $current = $paginator['current_page'];
        $last = $paginator['last_page'];
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        for ($i = $start; $i <= $end; $i++) {
            $url = current_url([$pageName => $i]);
            $activeClass = ($i == $current) ? 'bg-blue-500 text-white' : '';
            $html .= "<li><a href=\"{$url}\" class=\"px-3 py-1 border rounded {$activeClass}\">{$i}</a></li>";
        }

        // لینک بعدی
        if ($paginator['has_next']) {
            $url = current_url([$pageName => $paginator['next_page']]);
            $html .= "<li><a href=\"{$url}\" class=\"px-3 py-1 border rounded\">بعدی</a></li>";
        } else {
            $html .= '<li><span class="px-3 py-1 border rounded text-gray-400">بعدی</span></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }
}

if (!function_exists('current_url')) {
    function current_url($params = [])
    {
        $url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $query);
        $query = array_merge($query, $params);
        $newQuery = http_build_query($query);
        return $parts['path'] . ($newQuery ? '?' . $newQuery : '');
    }
}

if (!function_exists('storage')) {
    function storage(string $disk = 'public'): \App\Core\Storage\DriverInterface
    {
        return \App\Core\Storage\StorageManager::disk($disk);
    }
}

if (!function_exists('storage_url')) {
    function storage_url(string $path): string
    {
        return \App\Core\Storage\StorageManager::url($path);
    }
}

if (!function_exists('cache')) {
    function cache(?string $key = null, mixed $value = null, int $seconds = 3600): mixed
    {
        if ($key === null) {
            return \App\Core\Cache\CacheManager::driver();
        }
        if ($value === null) {
            return \App\Core\Cache\CacheManager::get($key);
        }
        return \App\Core\Cache\CacheManager::put($key, $value, $seconds);
    }
}

if (!function_exists('logger')) {
    function logger(string $level = 'info', string $message = '', array $context = [])
    {
        if (func_num_args() === 0) {
            return \App\Core\Log\LogManager::driver();
        }
        return \App\Core\Log\LogManager::$level($message, $context);
    }
}

if (!function_exists('view_exists')) {
    function view_exists(string $name): bool
    {
        global $viewEngine;
        try {
            $viewEngine->getCompiledPath($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

// helpers.php
if (!function_exists('token')) {
    /**
     * تولید یک توکن تصادفی امن
     * @param int $bytes تعداد بایت (پیشفرض: ۴۰ = ۸۰ کاراکتر هگز)
     * @return string
     */
    function token(int $bytes = 40): string
    {
        return \App\Core\Authentication\Tokenizer::make($bytes);
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = '')
    {
        http_response_code($code);
        if ($code == 404) {
            echo view('errors.404', ['message' => $message]);
        } else {
            echo $message ?: "Error {$code}";
        }
        exit;
    }
}

if (!function_exists('show_title')) {
    function show_title($title = null)
    {
        echo $__set['title'] ?? $title ?? config('APP_NAME');
    }
}


if (!function_exists('component')) {
    /**
     * دسترسی به مدیریت کامپوننت‌ها (ComponentManager)
     * @return \App\Core\Lady\ComponentManager
     */
    function component()
    {
        return \App\Core\Lady\ComponentManager::getInstance();
    }
}
