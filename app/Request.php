<?php

namespace App;

use App\Database\Db;
use App\Http\ErrorBag;

/**
 * کلاس مدیریت درخواست‌های HTTP
 * 
 * این کلاس داده‌های ورودی از فرم‌ها (POST/GET)، فایل‌ها،
 * JSON خام و هدرهای درخواست را مدیریت می‌کند.
 */
class Request
{
    // ==================== پراپرتی‌ها ====================
    
    /**
     * داده‌های POST (و JSON ادغام شده)
     */
    protected $requestData;
    
    /**
     * داده‌های GET (Query String)
     */
    protected $queryData;
    
    /**
     * داده‌های فایل‌های آپلود شده
     */
    protected $files;
    
    /**
     * متد HTTP درخواست (GET, POST, PUT, DELETE, ...)
     */
    protected $method;
    
    /**
     * داده‌های JSON خام (در صورت وجود)
     */
    protected $jsonData = [];
    
    /**
     * کاربر احراز هویت‌شده (توسط میدلور تنظیم می‌شود)
     */
    public $user = null;
    
    /**
     * هدرهای درخواست
     */
    protected $headers = [];

    // ==================== سازنده ====================
    
    public function __construct()
    {
        $this->requestData = $_POST;
        $this->queryData = $_GET;
        $this->files = $_FILES;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->headers = $this->parseHeaders();
        
        // ====== خواندن JSON خام (برای API) ======
        $contentType = $this->header('Content-Type') ?? $this->header('content-type') ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $this->jsonData = json_decode($input, true) ?? [];
            // ادغام با requestData (JSON اولویت دارد)
            $this->requestData = array_merge($this->requestData, $this->jsonData);
        }
        // ============================================
    }

    // ==================== متدهای اصلی (دست نخورده) ====================
    
    /**
     * دریافت داده‌های ورودی (POST یا JSON)
     * 
     * @param string|null $key کلید مورد نظر
     * @param mixed $default مقدار پیش‌فرض در صورت نبودن کلید
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->requestData;
        }

        return $this->requestData[$key] ?? $default;
    }

    /**
     * دریافت داده‌های Query String (GET)
     * 
     * @param string|null $key کلید مورد نظر
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->queryData;
        }

        return $this->queryData[$key] ?? $default;
    }

    /**
     * دریافت یک فایل آپلود شده
     * 
     * @param string $key نام فیلد فایل
     * @return File|null
     */
    public function file($key)
    {
        if (isset($this->files[$key])) {
            return new File($this->files[$key]);
        }
        return null;
    }

    /**
     * بررسی وجود فایل آپلود شده
     * 
     * @param string $key نام فیلد فایل
     * @return bool
     */
    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * دریافت متد HTTP درخواست
     * 
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * بررسی متد HTTP درخواست
     * 
     * @param string $method نام متد (GET, POST, PUT, ...)
     * @return bool
     */
    public function isMethod($method)
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    /**
     * بررسی تطابق روت فعلی با الگو
     * 
     * @param string $pattern الگوی نام روت
     * @return bool
     */
    public function routeIs($pattern)
    {
        return \App\Route::is($pattern);
    }

    /**
     * اعتبارسنجی داده‌های ورودی
     * 
     * @param array $rules قوانین اعتبارسنجی
     * @param array $messages پیام‌های سفارشی
     * @return \App\Http\ErrorBag|null
     */
    public function validate($rules, $messages = [])
    {
        $validator = new \App\Core\Validation\Validator($this->all(), $rules, $messages);
        $validator->validate();

        if ($validator->fails()) {
            return $validator->errors();
        }
        return null;
    }

    /**
     * دریافت همه داده‌های ورودی (GET + POST + JSON)
     * 
     * @return array
     */
    public function all()
    {
        return array_merge($this->queryData, $this->requestData);
    }

    // ==================== متدهای جدید (اضافه شده) ====================
    
    /**
     * دریافت توکن از هدر Authorization (Bearer)
     * 
     * @return string|null
     */
    public function bearerToken(): ?string
    {
        $authHeader = $this->header('Authorization') ?? $this->header('authorization') ?? '';
        
        if (preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * دریافت یک هدر خاص
     * 
     * @param string $key نام هدر
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     * دریافت همه هدرها
     * 
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * دریافت داده‌های JSON خام (بدون ادغام با POST)
     * 
     * @param string|null $key کلید مورد نظر
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if ($key === null) {
            return $this->jsonData;
        }
        return $this->jsonData[$key] ?? $default;
    }

    /**
     * بررسی اینکه آیا درخواست JSON است
     * 
     * @return bool
     */
    public function isJson(): bool
    {
        $contentType = $this->header('Content-Type') ?? $this->header('content-type') ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    /**
     * بررسی اینکه آیا درخواست AJAX است
     * 
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * بررسی اینکه آیا درخواست HTTPS است
     * 
     * @return bool
     */
    public function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
            || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
    }

    /**
     * دریافت IP کاربر
     * 
     * @return string
     */
    public function ip(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * دریافت User-Agent
     * 
     * @return string|null
     */
    public function userAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * دریافت URL کامل درخواست
     * 
     * @return string
     */
    public function fullUrl(): string
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return "{$protocol}://{$host}{$uri}";
    }

    /**
     * دریافت مسیر درخواست (بدون Query String)
     * 
     * @return string
     */
    public function path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return rtrim($path ?? '/', '/') ?: '/';
    }

    /**
     * دریافت داده‌ها با پشتیبانی از Dot Notation (مثل user.name)
     * 
     * @param string $key کلید با فرمت نقطه‌دار
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $data = $this->all();
        $keys = explode('.', $key);
        
        foreach ($keys as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }
        
        return $data;
    }

    /**
     * بررسی وجود یک کلید در داده‌ها (با پشتیبانی از Dot Notation)
     * 
     * @param string $key کلید با فرمت نقطه‌دار
     * @return bool
     */
    public function has($key): bool
    {
        return $this->get($key, '__NOT_EXISTS__') !== '__NOT_EXISTS__';
    }

    /**
     * بررسی خالی بودن یک کلید
     * 
     * @param string $key کلید با فرمت نقطه‌دار
     * @return bool
     */
    public function filled($key): bool
    {
        $value = $this->get($key);
        return $value !== null && $value !== '';
    }

    /**
     * دریافت یک فیلد و خالی کردن آن از سشن (فلش دیتا)
     * 
     * @param string $key کلید
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public function old($key, $default = '')
    {
        if (isset($_SESSION['_flash']['old'][$key])) {
            $value = $_SESSION['_flash']['old'][$key];
            unset($_SESSION['_flash']['old'][$key]);
            return $value;
        }
        
        return $this->input($key, $default);
    }

    // ==================== متدهای کمکی (دست نخورده) ====================
    
    /**
     * چک کردن یکتا بودن مقدار در جدول
     * 
     * @param string $table نام جدول
     * @param string $column نام ستون
     * @param mixed $value مقدار
     * @return bool
     * @throws \Exception
     */
    private function checkUnique($table, $column, $value)
    {
        $mysqli = Db::getInstance();
        $mysqli->set_charset("utf8");
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
        if ($stmt === false) {
            throw new \Exception("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param('s', $value);
        $stmt->execute();
        $stmt->bind_result($count);
        $count = 0;
        if ($stmt->fetch()) {
            return $count > 0;
        }
        return false;
    }

    /**
     * ارسال یک درخواست HTTP به سرور دیگر (cURL-like)
     * 
     * @param string $url آدرس مقصد
     * @param array $data داده‌ها
     * @param string $method متد HTTP
     * @return string|false
     */
    public static function send($url, $data = [], $method = 'GET')
    {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => strtoupper($method),
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    // ==================== متدهای داخلی ====================
    
    /**
     * پردازش و استخراج هدرها از $_SERVER
     * 
     * @return array
     */
    protected function parseHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $headers[$name] = $value;
            }
        }
        
        return array_change_key_case($headers, CASE_LOWER);
    }
}

// ==================== کلاس File (دست نخورده) ====================
class File
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * بررسی معتبر بودن فایل
     */
    public function isValid()
    {
        return isset($this->file) && $this->file['error'] === UPLOAD_ERR_OK;
    }

    /**
     * دریافت نام اصلی فایل
     */
    public function getClientOriginalName()
    {
        return $this->file['name'] ?? null;
    }

    /**
     * دریافت پسوند فایل
     */
    public function getClientOriginalExtension()
    {
        return pathinfo($this->file['name'] ?? '', PATHINFO_EXTENSION);
    }

    /**
     * دریافت نوع MIME فایل
     */
    public function getMimeType()
    {
        return $this->file['type'] ?? null;
    }

    /**
     * دریافت حجم فایل (به بایت)
     */
    public function getSize()
    {
        return $this->file['size'] ?? 0;
    }

    /**
     * دریافت مسیر موقت فایل
     */
    public function getRealPath()
    {
        return $this->file['tmp_name'] ?? null;
    }

    /**
     * ذخیره فایل در مسیر مشخص
     * 
     * @param string $path مسیر ذخیره‌سازی
     * @return bool
     */
    public function move($path)
    {
        if (!$this->isValid()) {
            return false;
        }
        
        $targetDir = dirname($path);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        return move_uploaded_file($this->file['tmp_name'], $path);
    }

    /**
     * ذخیره فایل با نام جدید
     * 
     * @param string $directory دایرکتوری مقصد
     * @param string|null $name نام جدید (اختیاری)
     * @return string|false مسیر ذخیره‌شده یا false در صورت خطا
     */
    public function storeAs($directory, $name = null)
    {
        if (!$this->isValid()) {
            return false;
        }
        
        if ($name === null) {
            $name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $this->getClientOriginalExtension();
        }
        
        $path = rtrim($directory, '/') . '/' . $name;
        
        if ($this->move($path)) {
            return $path;
        }
        
        return false;
    }

    /**
     * ذخیره فایل با نام خودکار
     */
    public function store($directory)
    {
        return $this->storeAs($directory);
    }

    /**
     * دریافت محتوای فایل
     */
    public function getContent()
    {
        if ($this->isValid()) {
            return file_get_contents($this->file['tmp_name']);
        }
        return null;
    }

    /**
     * تبدیل به آرایه
     */
    public function toArray()
    {
        return [
            'name' => $this->getClientOriginalName(),
            'size' => $this->getSize(),
            'mime_type' => $this->getMimeType(),
            'extension' => $this->getClientOriginalExtension(),
        ];
    }
}