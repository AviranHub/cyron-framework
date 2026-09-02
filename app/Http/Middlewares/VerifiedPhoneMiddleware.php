<?php
namespace App\Http\Middlewares;

use App\Http\Middleware;
use App\Core\Authentication\Auth;
use App\Core\Authentication\PhoneVerification;

class VerifiedPhoneMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        $user = Auth::user();
        if (!$user || !PhoneVerification::isVerified($user)) {
            return redirect()->route('verification.required');
        }
        return $next($request);
    }
}