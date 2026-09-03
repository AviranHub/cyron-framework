<?php


use App\Core\Env;
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
    /**
     * Backward-compatible environment helper.
     * .env is the only configuration source.
     */
    function vars($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('load')) {
    function load($folder_name, $file_name = null, $attributes = null)
    {
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
        echo '/assets/' . ltrim($file_path, '/');
    }
}

if (!function_exists('arrayToObject')) {
    function arrayToObject($array)
    {
        return json_decode(json_encode($array));
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = [])
    {
        global $viewEngine;
        if (!$viewEngine) {
            throw new Exception('View engine not initialized. Please check bootstrap.php');
        }
        return $viewEngine->renderView($name, $data);
    }
}

if (!function_exists('dump')) {
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

if (!function_exists('route')) {
    function route($name, $parameters = [])
    {
        if (class_exists('App\Route')) {
            $router = \App\Route::getInstance();
            return $router->url($name, $parameters ?? []);
        }
        return $name;
    }
}

if (!function_exists('url')) {
    function url($path)
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
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

        public static function get($key, $default = null)
        {
            self::start();
            return $_SESSION[$key] ?? $default;
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

        public function send()
        {
            if (!empty($this->withData)) {
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
