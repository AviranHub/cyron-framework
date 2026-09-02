<?php
// app/Core/Http/Response.php

namespace App;

class Response
{
    protected mixed $content = null;
    protected int $statusCode = 200;
    protected array $headers = [];
    protected array $cookies = [];
    protected bool $shouldExit = true;

    // ==================== سازنده ====================

    public function __construct(mixed $content = null, int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $status;
        $this->headers = $headers;
    }

    // ==================== متدهای زنجیره‌ای (Fluent) ====================

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function cookie(string $name, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        $this->cookies[] = compact('name', 'value', 'expire', 'path', 'domain', 'secure', 'httponly');
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        return $this->headers($headers);
    }

    public function withoutExit(): self
    {
        $this->shouldExit = false;
        return $this;
    }

    // ==================== ارسال پاسخ ====================

    public function send(): void
    {
        // تنظیم کد وضعیت
        http_response_code($this->statusCode);

        // تنظیم هدرها
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // تنظیم کوکی‌ها
        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }

        // محتوای پاسخ
        if ($this->content !== null) {
            echo $this->content;
        }

        if ($this->shouldExit) {
            exit;
        }
    }

    // ==================== متدهای کمکی (Builders) ====================

    /**
     * پاسخ JSON
     */
    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $headers = array_merge($headers, ['Content-Type' => 'application/json']);
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return new self($content, $status, $headers);
    }

    /**
     * پاسخ View (HTML)
     */
    public static function view(string $view, array $data = [], int $status = 200, array $headers = []): self
    {
        $content = view($view, $data);
        return new self($content, $status, $headers);
    }

    /**
     * پاسخ متن ساده
     */
    public static function text(string $text, int $status = 200, array $headers = []): self
    {
        $headers = array_merge($headers, ['Content-Type' => 'text/plain; charset=utf-8']);
        return new self($text, $status, $headers);
    }

    /**
     * پاسخ HTML
     */
    public static function html(string $html, int $status = 200, array $headers = []): self
    {
        $headers = array_merge($headers, ['Content-Type' => 'text/html; charset=utf-8']);
        return new self($html, $status, $headers);
    }

    /**
     * دانلود فایل
     */
    public static function download(string $filePath, ?string $fileName = null, array $headers = []): self
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $fileName = $fileName ?? basename($filePath);
        $headers = array_merge($headers, [
            'Content-Type' => mime_content_type($filePath) ?: 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Length' => filesize($filePath),
        ]);

        $content = file_get_contents($filePath);
        return new self($content, 200, $headers);
    }

    /**
     * نمایش فایل (Inline)
     */
    public static function file(string $filePath, array $headers = []): self
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $headers = array_merge($headers, [
            'Content-Type' => mime_content_type($filePath) ?: 'application/octet-stream',
            'Content-Length' => filesize($filePath),
        ]);

        $content = file_get_contents($filePath);
        return new self($content, 200, $headers);
    }

    /**
     * پاسخ بدون محتوا (204)
     */
    public static function noContent(): self
    {
        return new self(null, 204);
    }

    /**
     * پاسخ ریدایرکت
     */
    public static function redirect(string $url, int $status = 302, array $headers = []): self
    {
        $headers = array_merge($headers, ['Location' => $url]);
        return new self(null, $status, $headers);
    }

    /**
     * ریدایرکت به نام روت
     */
    public static function route(string $name, array $parameters = [], int $status = 302): self
    {
        $url = route($name, $parameters);
        return self::redirect($url, $status);
    }

    /**
     * ریدایرکت به صفحه قبلی
     */
    public static function back(int $status = 302, array $headers = []): self
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return self::redirect($referer, $status, $headers);
    }

    // ==================== متدهای API (استاندارد) ====================

    /**
     * پاسخ موفقیت API
     */
    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): self
    {
        return self::json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * پاسخ خطای API
     */
    public static function error(string $message = 'Error', int $status = 400, mixed $errors = null): self
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        return self::json($payload, $status);
    }

    /**
     * پاسخ خطای اعتبارسنجی (422)
     */
    public static function validationError(mixed $errors, string $message = 'Validation failed'): self
    {
        return self::error($message, 422, $errors);
    }

    /**
     * پاسخ خطای احراز هویت (401)
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    /**
     * پاسخ خطای دسترسی (403)
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    /**
     * پاسخ منبع پیدا نشد (404)
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::error($message, 404);
    }

    /**
     * پاسخ درخواست نامعتبر (400)
     */
    public static function badRequest(string $message = 'Bad Request', mixed $errors = null): self
    {
        return self::error($message, 400, $errors);
    }

    // ==================== متدهای ویژه ====================

    /**
     * به‌روزرسانی محتوای پاسخ
     */
    public function setContent(mixed $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * دریافت محتوای پاسخ
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * دریافت کد وضعیت
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * دریافت هدرها
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * تبدیل به آرایه (برای دیباگ)
     */
    public function toArray(): array
    {
        return [
            'status' => $this->statusCode,
            'headers' => $this->headers,
            'content' => $this->content,
        ];
    }
}