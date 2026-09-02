<?php
// app/Http/Middlewares/ApiAuthMiddleware.php

namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authentication\Tokenizer;

class ApiAuthMiddleware extends Middleware
{
    /**
     * پردازش درخواست و احراز هویت کاربر از طریق توکن
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        // ۱. دریافت توکن از هدر Authorization
        $token = $request->bearerToken();

        if (!$token) {
            return response()->unauthorized('توکن احراز هویت ارائه نشده است');
        }

        // ۲. دریافت کاربر از طریق توکن
        $user = Tokenizer::getUserByAccessToken($token);

        if (!$user) {
            return response()->unauthorized('توکن نامعتبر یا منقضی شده است');
        }

        // ۳. چسباندن کاربر به درخواست برای استفاده در کنترلر
        $request->user = $user;

        // ۴. ادامه به میدلور بعدی یا کنترلر
        return $next($request);
    }
}