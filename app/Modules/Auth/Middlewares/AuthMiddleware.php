<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authentication\Auth as AuthHelper;
use App\Auth\SessionRegistry;

class AuthMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        if (!AuthHelper::check()) {
            // اگر لاگین نیست، به صفحه لاگین هدایت کن
            return redirect()->route('login');
        }
        $token = session_id();
        if ($token === '' || !SessionRegistry::active($token)) {
            AuthHelper::logout();
            return redirect()->route('login');
        }
        SessionRegistry::touch($token);

        // ادامه به میدلور بعدی یا کنترلر
        return $next($request);
    }
}