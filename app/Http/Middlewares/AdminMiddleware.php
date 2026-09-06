<?php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use Cyron\Authentication\Auth;

class AdminMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if (!in_array((string) ($user->role ?? ''), ['admin', 'superadmin'], true)) {
            return redirect()->route('home')->with('error', 'دسترسی مدیریت برای این حساب مجاز نیست.');
        }

        return $next($request);
    }
}