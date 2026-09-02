<?php

namespace App\Http\Middlewares;

class CsrfMiddleware
{
    private array $safeMethods = ['GET', 'HEAD', 'OPTIONS'];

    public function handle($request, $next)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        // API endpoints use bearer-token authentication and are not cookie-authenticated.
        if (str_starts_with($path, '/api/') || $path === '/api' || in_array($method, $this->safeMethods, true)) {
            return $next($request);
        }

        $token = $_POST['_token']
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)
            ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? null);

        $sessionToken = $_SESSION['csrf_token'] ?? null;
        if (!is_string($token) || !is_string($sessionToken) || !hash_equals($sessionToken, $token)) {
            http_response_code(419);
            return 'CSRF token mismatch.';
        }

        return $next($request);
    }
}
