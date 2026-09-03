<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authentication\Auth;

class AuthorMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        $user = Auth::user();
        $role = strtolower((string)($user->role ?? ''));
        if (!$user || !in_array($role, ['author', 'admin', 'superadmin'], true)) {
            http_response_code(403);
            return 'دسترسی به پنل نویسندگی برای این حساب فعال نیست.';
        }
        return $next($request);
    }
}
