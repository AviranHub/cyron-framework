<?php

namespace Cyron\Authentication;

final class SessionManager
{
    private const DEFAULT_IDLE_TIMEOUT = 1800;
    private const DEFAULT_MAX_LIFETIME = 86400;

    public static function login(object $user, bool $remember = false): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['user_id'] = $user->id;
        $_SESSION['login_at'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_started_at'] = time();
        $_SESSION['user_agent_hash'] = hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($remember) RememberMe::issue((int) $user->id);
    }

    public static function logout(): void
    {
        self::clearSession(true);
    }

    private static function clearSession(bool $forgetRemember): void
    {
        if ($forgetRemember) RememberMe::forget();
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
            }
            session_regenerate_id(true);
            session_destroy();
        }
    }

    public static function check(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] <= 0) {
            return RememberMe::restore();
        }

        $now = time();
        $last = is_numeric($_SESSION['last_activity'] ?? null) ? (int) $_SESSION['last_activity'] : $now;
        $started = is_numeric($_SESSION['session_started_at'] ?? null) ? (int) $_SESSION['session_started_at'] : $now;
        if (($now - $last) > self::idleTimeout() || ($now - $started) > self::maxLifetime()) {
            self::clearSession(false);
            return false;
        }

        $expected = hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if (!hash_equals((string) ($_SESSION['user_agent_hash'] ?? ''), $expected)) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = $now;
        return true;
    }

    private static function idleTimeout(): int
    {
        return max(300, (int) \Cyron\Support\Env::get('APP_SESSION_IDLE_TIMEOUT', self::DEFAULT_IDLE_TIMEOUT));
    }

    private static function maxLifetime(): int
    {
        return max(3600, (int) \Cyron\Support\Env::get('APP_SESSION_MAX_LIFETIME', self::DEFAULT_MAX_LIFETIME));
    }

    public static function id(): mixed
    {
        return $_SESSION['user_id'] ?? null;
    }
}
