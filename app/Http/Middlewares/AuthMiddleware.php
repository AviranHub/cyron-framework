<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authentication\Auth as AuthHelper;

class AuthMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        if (!AuthHelper::check()) {
            // اگر لاگین نیست، به صفحه لاگین هدایت کن
            return redirect()->route('login');
        }
        
        // ادامه به میدلور بعدی یا کنترلر
        return $next($request);
    }
}