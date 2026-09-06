<?php

namespace Cyron\Authentication;

use Cyron\Database\ModelRegistry;
use Cyron\Authentication\SessionManager;

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
            $userModel = ModelRegistry::get('user');
            return self::validateCredentials($userModel::where($field, '=', $login)->first(), $password);
        }

        foreach (self::$loginFields as $fieldName) {
            $userModel = ModelRegistry::get('user');
            $user = $userModel::where($fieldName, '=', $login)->first();
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
        if (class_exists('Cyron\\Analytics\\ActivityTracker')) {
            \Cyron\Analytics\ActivityTracker::record('auth.logged_in', [], (int) $user->id);
        }
    }

    public static function setLoginFields(array $fields)
    {
        self::$loginFields = array_values(array_intersect($fields, ['email', 'phone', 'username']));
    }

    public static function login($user, bool $remember = false)
    {
        SessionManager::login($user, $remember);
    }

    public static function logout()
    {
        SessionManager::logout();
    }

    public static function check(): bool
    {
        return SessionManager::check();
    }

    public static function requireVerifiedUser(): bool
    {
        $user = self::user();
        return $user !== null && ($user->status ?? 'active') === 'active';
    }

    public static function user()
    {
        if (!static::check()) return null;
        $userModel = ModelRegistry::get('user');
        $user = $userModel::find($_SESSION['user_id']);
        if (!$user || ($user->status ?? 'active') !== 'active') { self::logout(); return null; }
        return $user;
    }

    public static function id()
    {
        return SessionManager::id();
    }
}
