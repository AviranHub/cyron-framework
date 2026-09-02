<?php
namespace App\Http\Middleware;

use App\Auth\SessionRegistry;

class EnsureSessionActive
{
    public function handle($request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $token = method_exists($request, 'sessionToken') ? $request->sessionToken() : null;
        if (!$token) $token = session_id();

        if ($token) {
            $session = SessionRegistry::findByToken($token);
            if ($session && !empty($session->revoked_at)) {
                \App\Core\Authentication\Auth::logout();
                throw new \RuntimeException('Session has been revoked.');
            }
            if ($session) SessionRegistry::touch($token);
        }

        return $next($request);
    }
}
