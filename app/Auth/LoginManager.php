<?php
namespace App\Auth;

use App\Core\Authentication\Auth;

class LoginManager
{
    public static function attempt(string $login, string $password): array
    {
        $key = self::protectionKey($login);
        $preflight = AuthenticationPipeline::beforeLogin($key);
        if (!($preflight['allowed'] ?? false)) {
            return ['status'=>'rate_limited','retry_after'=>$preflight['retry_after'] ?? null];
        }

        $user = Auth::credentials($login, $password);
        if (!$user) {
            return AuthenticationFlow::passwordRejected($key, null, self::context());
        }

        $two = TwoFactor::enabled((int)$user->id);
        if ($two) {
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            $_SESSION['pending_auth'] = [
                'user_id'=>(int)$user->id,
                'key'=>$key,
                'channel'=>$two->channel,
                'created_at'=>time(),
            ];
            TwoFactor::challenge((int)$user->id);
            return ['status'=>'two_factor_required','user_id'=>(int)$user->id,'channel'=>$two->channel];
        }

        return self::complete($key, $user);
    }

    public static function completeTwoFactor(string $code): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $pending = $_SESSION['pending_auth'] ?? null;
        if (!is_array($pending) || empty($pending['user_id']) || empty($pending['channel']) || empty($pending['key'])) {
            return ['status'=>'no_pending_authentication'];
        }
        if ((time() - (int)($pending['created_at'] ?? 0)) > 600) {
            unset($_SESSION['pending_auth']);
            return ['status'=>'authentication_expired'];
        }

        $userId = (int)$pending['user_id'];
        $channel = (string)$pending['channel'];
        if (!TwoFactor::verify($userId, $channel, $code)) {
            return ['status'=>'invalid_two_factor_code'];
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            unset($_SESSION['pending_auth']);
            return ['status'=>'invalid_credentials'];
        }

        $key = (string)$pending['key'];
        unset($_SESSION['pending_auth']);
        return self::complete($key, $user, ['two_factor'=>true,'channel'=>$channel]);
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $token = session_id();
        if ($token !== '') SessionRegistry::revokeToken($token);
        Auth::logout();
    }

    private static function complete(string $key, $user, array $context=[]): array
    {
        Auth::login($user);
        Auth::markLogin($user);
        $token = session_id();
        AuthenticationPipeline::succeeded($key, (int)$user->id, $token, $context + self::context());
        return ['status'=>'authenticated','user_id'=>(int)$user->id];
    }

    private static function protectionKey(string $login): string
    {
        $ip = self::context()['ip'] ?? 'unknown';
        return hash('sha256', strtolower(trim($login)).'|'.$ip);
    }

    private static function context(): array
    {
        $request = function_exists('request') ? request() : null;
        return [
            'ip'=>$request && method_exists($request,'ip') ? $request->ip() : ($_SERVER['REMOTE_ADDR'] ?? null),
            'user_agent'=>$request && method_exists($request,'userAgent') ? $request->userAgent() : ($_SERVER['HTTP_USER_AGENT'] ?? null),
        ];
    }
}
