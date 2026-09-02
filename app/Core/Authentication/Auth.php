<?php

namespace App\Core\Authentication;

use App\Models\User;

class Auth
{
    protected const SESSION_IDLE_TIMEOUT = 1800;
    protected const SESSION_MAX_LIFETIME = 86400;
    protected static $loginFields = ['email', 'phone', 'username'];

    /**
     * Backward-compatible login attempt. New code should prefer
     * LoginManager so 2FA, tracking and session registration are honored.
     */
    public static function attempt($login, $password, $field = null)
    {
        $user = self::credentials($login, $password, $field);
        if (!$user) return false;

        self::login($user);
        self::markLogin($user);
        return true;
    }

    /**
     * Verify credentials without creating an authenticated session.
     */
    public static function credentials($login, $password, $field = null)
    {
        if ($field !== null) {
            if (!in_array($field, self::$loginFields, true)) return null;
            return self::validateCredentials(User::where($field, '=', $login)->first(), $password);
        }

        foreach (self::$loginFields as $fieldName) {
            $user = User::where($fieldName, '=', $login)->first();
            $valid = self::validateCredentials($user, $password);
            if ($valid) return $valid;
        }

        return null;
    }

    protected static function validateCredentials($user, $password)
    {
        if (!$user || !password_verify($password, $user->password)) return null;
        if (($user->status ?? 'active') !== 'active') return null;
        if (!empty($user->suspended_until) && strtotime((string) $user->suspended_until) > time()) return null;
        return $user;
    }

    public static function markLogin($user): void
    {
        $user->update([
            'login_count' => (int)($user->login_count ?? 0) + 1,
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function setLoginFields(array $fields)
    {
        self::$loginFields = array_values(array_intersect($fields, ['email', 'phone', 'username']));
    }

    public static function login($user)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['user_id'] = $user->id;
        $_SESSION['login_at'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_started_at'] = time();
        $_SESSION['user_agent_hash'] = hash('sha256', (string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
            }
            session_regenerate_id(true);
            session_destroy();
        }
    }

    public static function check(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] <= 0) return false;
        $now = time();
        $last = is_numeric($_SESSION['last_activity'] ?? null) ? (int)$_SESSION['last_activity'] : $now;
        $started = is_numeric($_SESSION['session_started_at'] ?? null) ? (int)$_SESSION['session_started_at'] : $now;
        if (($now-$last)>self::SESSION_IDLE_TIMEOUT || ($now-$started)>self::SESSION_MAX_LIFETIME) { self::logout(); return false; }
        $expected = hash('sha256', (string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if (!hash_equals((string)($_SESSION['user_agent_hash'] ?? ''), $expected)) { self::logout(); return false; }
        $_SESSION['last_activity']=$now;
        return true;
    }

    public static function requireVerifiedUser(): bool
    {
        $user = self::user();
        return $user !== null && ($user->status ?? 'active') === 'active';
    }

    public static function user()
    {
        if (!static::check()) return null;
        $user = User::find($_SESSION['user_id']);
        if (!$user || ($user->status ?? 'active') !== 'active') { self::logout(); return null; }
        return $user;
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
