<?php

namespace App\Core\Authorization;

use App\Core\Authentication\Auth;

class Gate
{
    public static function allows(string $ability, $user = null): bool
    {
        $user = $user ?: Auth::user();
        return $user !== null && $user->hasPermission($ability);
    }

    public static function allowsAny(array $abilities, $user = null): bool
    {
        foreach ($abilities as $ability) if (self::allows((string) $ability, $user)) return true;
        return false;
    }

    public static function allowsAll(array $abilities, $user = null): bool
    {
        foreach ($abilities as $ability) if (!self::allows((string) $ability, $user)) return false;
        return true;
    }

    public static function denies(string $ability, $user = null): bool
    {
        return !self::allows($ability, $user);
    }

    public static function hasRole(string $role, $user = null): bool
    {
        $user = $user ?: Auth::user();
        return $user !== null && $user->hasRole($role);
    }

    public static function authorize(string $ability, $user = null): void
    {
        if (!self::allows($ability, $user)) {
            http_response_code(403);
            throw new \RuntimeException('Forbidden.');
        }
    }
}
