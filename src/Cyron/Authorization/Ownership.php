<?php

namespace Cyron\Authorization;

class Ownership
{
    protected static $userResolver;

    public static function setUserResolver(?callable $resolver): void
    {
        self::$userResolver = $resolver;
    }

    public static function owns(object $resource, $user = null, string $ownerKey = 'user_id'): bool
    {
        $user = self::resolveUser($user);
        if (!$user || empty($resource->id) || !isset($resource->{$ownerKey})) return false;
        return (string) $resource->{$ownerKey} === (string) $user->id;
    }

    public static function authorize(object $resource, $user = null, string $ownerKey = 'user_id'): void
    {
        if (!self::owns($resource, $user, $ownerKey)) {
            http_response_code(403);
            throw new \RuntimeException('You do not own this resource.');
        }
    }

    public static function resolveAndAuthorize(string $model, $id, $user = null, string $ownerKey = 'user_id'): object
    {
        if (!class_exists($model) || !method_exists($model, 'find')) {
            throw new \InvalidArgumentException('Invalid resource model.');
        }
        $resource = $model::find($id);
        if (!$resource) {
            http_response_code(404);
            throw new \RuntimeException('Resource not found.');
        }
        self::authorize($resource, $user, $ownerKey);
        return $resource;
    }

    protected static function resolveUser($user)
    {
        if ($user !== null) return $user;
        return is_callable(self::$userResolver) ? (self::$userResolver)() : null;
    }
}
