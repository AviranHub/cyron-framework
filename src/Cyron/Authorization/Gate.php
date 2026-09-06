<?php

namespace Cyron\Authorization;

class Gate
{
    protected static $userResolver;

    public static function setUserResolver(?callable $resolver): void
    {
        self::$userResolver = $resolver;
    }

    public static function allows(string $ability, $user = null): bool
    {
        $user = self::resolveUser($user);
        return $user !== null && method_exists($user, 'hasPermission') && $user->hasPermission($ability);
    }

    public static function allowsAny(array $abilities, $user = null): bool
    {
        foreach ($abilities as $ability) {
            if (self::allows((string) $ability, $user)) return true;
        }
        return false;
    }

    public static function allowsAll(array $abilities, $user = null): bool
    {
        foreach ($abilities as $ability) {
            if (!self::allows((string) $ability, $user)) return false;
        }
        return true;
    }

    public static function denies(string $ability, $user = null): bool
    {
        return !self::allows($ability, $user);
    }

    public static function hasRole(string $role, $user = null): bool
    {
        $user = self::resolveUser($user);
        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole($role);
    }

    public static function authorize(string $ability, $user = null): void
    {
        if (!self::allows($ability, $user)) {
            http_response_code(403);
            throw new \RuntimeException('Forbidden.');
        }
    }

    protected static function resolveUser($user)
    {
        if ($user !== null) return $user;
        return is_callable(self::$userResolver) ? (self::$userResolver)() : null;
    }
}
