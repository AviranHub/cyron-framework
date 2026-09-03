<?php

namespace Cyron\Http;

class Response
{
    protected mixed $content = null;
    protected int $statusCode = 200;
    protected array $headers = [];
    protected array $cookies = [];
    protected bool $shouldExit = true;

    public function __construct(mixed $content = null, int $status = 200, array $headers = []) { $this->content = $content; $this->statusCode = $status; $this->headers = $headers; }
    public function status(int $code): self { $this->statusCode = $code; return $this; }
    public function header(string $key, string $value): self { $this->headers[$key] = $value; return $this; }
    public function headers(array $headers): self { $this->headers = array_merge($this->headers, $headers); return $this; }
    public function cookie(string $name, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self { $this->cookies[] = compact('name', 'value', 'expire', 'path', 'domain', 'secure', 'httponly'); return $this; }
    public function withHeaders(array $headers): self { return $this->headers($headers); }
    public function withoutExit(): self { $this->shouldExit = false; return $this; }
    public function send(): void { http_response_code($this->statusCode); foreach ($this->headers as $key => $value) header("{$key}: {$value}"); foreach ($this->cookies as $cookie) setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']); if ($this->content !== null) echo $this->content; if ($this->shouldExit) exit; }
    public static function json(mixed $data, int $status = 200, array $headers = []): self { return new self(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $status, array_merge($headers, ['Content-Type' => 'application/json'])); }
    public static function view(string $view, array $data = [], int $status = 200, array $headers = []): self { return new self(view($view, $data), $status, $headers); }
    public static function text(string $text, int $status = 200, array $headers = []): self { return new self($text, $status, array_merge($headers, ['Content-Type' => 'text/plain; charset=utf-8'])); }
    public static function html(string $html, int $status = 200, array $headers = []): self { return new self($html, $status, array_merge($headers, ['Content-Type' => 'text/html; charset=utf-8'])); }
    public static function download(string $filePath, ?string $fileName = null, array $headers = []): self { if (!file_exists($filePath)) throw new \Exception("File not found: {$filePath}"); $fileName = $fileName ?? basename($filePath); return new self(file_get_contents($filePath), 200, array_merge($headers, ['Content-Type' => mime_content_type($filePath) ?: 'application/octet-stream', 'Content-Disposition' => "attachment; filename=\"{$fileName}\"", 'Content-Length' => filesize($filePath)])); }
    public static function file(string $filePath, array $headers = []): self { if (!file_exists($filePath)) throw new \Exception("File not found: {$filePath}"); return new self(file_get_contents($filePath), 200, array_merge($headers, ['Content-Type' => mime_content_type($filePath) ?: 'application/octet-stream', 'Content-Length' => filesize($filePath)])); }
    public static function noContent(): self { return new self(null, 204); }
    public static function redirect(string $url, int $status = 302, array $headers = []): self { return new self(null, $status, array_merge($headers, ['Location' => $url])); }
    public static function route(string $name, array $parameters = [], int $status = 302): self { return self::redirect(route($name, $parameters), $status); }
    public static function back(int $status = 302, array $headers = []): self { return self::redirect($_SERVER['HTTP_REFERER'] ?? '/', $status, $headers); }
    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): self { return self::json(['success' => true, 'message' => $message, 'data' => $data], $status); }
    public static function error(string $message = 'Error', int $status = 400, mixed $errors = null): self { $payload = ['success' => false, 'message' => $message]; if ($errors !== null) $payload['errors'] = $errors; return self::json($payload, $status); }
    public static function validationError(mixed $errors, string $message = 'Validation failed'): self { return self::error($message, 422, $errors); }
    public static function unauthorized(string $message = 'Unauthorized'): self { return self::error($message, 401); }
    public static function forbidden(string $message = 'Forbidden'): self { return self::error($message, 403); }
    public static function notFound(string $message = 'Not Found'): self { return self::error($message, 404); }
    public static function badRequest(string $message = 'Bad Request', mixed $errors = null): self { return self::error($message, 400, $errors); }
    public function setContent(mixed $content): self { $this->content = $content; return $this; }
    public function getContent(): mixed { return $this->content; }
    public function getStatusCode(): int { return $this->statusCode; }
    public function getHeaders(): array { return $this->headers; }
    public function toArray(): array { return ['status' => $this->statusCode, 'headers' => $this->headers, 'content' => $this->content]; }
}
