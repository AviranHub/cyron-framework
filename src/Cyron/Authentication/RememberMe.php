<?php

namespace Cyron\Authentication;

use Cyron\Database\ModelRegistry;

final class RememberMe
{
    private const COOKIE = 'cyron_remember';
    private const LIFETIME = 2592000;

    public static function issue(int $userId): void
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $model = ModelRegistry::get('remember_token');
        $model::create([
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => hash('sha256', $validator),
            'expires_at' => date('Y-m-d H:i:s', time() + self::LIFETIME),
        ]);
        self::setCookie($selector . ':' . $validator, time() + self::LIFETIME);
    }

    public static function restore(): bool
    {
        $value = (string) ($_COOKIE[self::COOKIE] ?? '');
        if (!preg_match('/^([a-f0-9]{32}):([a-f0-9]{64})$/', $value, $matches)) return false;

        $model = ModelRegistry::get('remember_token');
        $token = $model::where('selector', $matches[1])->first();
        if (!$token || strtotime((string) $token->expires_at) <= time()) {
            self::forget();
            return false;
        }
        if (!hash_equals((string) $token->token_hash, hash('sha256', $matches[2]))) {
            self::forget();
            return false;
        }

        $userModel = ModelRegistry::get('user');
        $user = $userModel::find((int) $token->user_id);
        if (!$user || ($user->status ?? 'active') !== 'active') {
            self::forget();
            return false;
        }

        $token->delete();
        SessionManager::login($user, false);
        self::issue((int) $user->id);
        return true;
    }

    public static function forget(): void
    {
        $value = (string) ($_COOKIE[self::COOKIE] ?? '');
        if (preg_match('/^([a-f0-9]{32}):[a-f0-9]{64}$/', $value, $matches)) {
            $model = ModelRegistry::get('remember_token');
            $model::where('selector', $matches[1])->delete();
        }
        self::setCookie('', time() - 42000);
    }

    public static function revokeUser(int $userId): void
    {
        $model = ModelRegistry::get('remember_token');
        $model::where('user_id', $userId)->delete();
    }

    private static function setCookie(string $value, int $expires): void
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        setcookie(self::COOKIE, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
