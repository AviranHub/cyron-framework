<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;

class SecurityHeadersMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        return $next($request);
    }
}