<?php
namespace App\Core\Authorization;
use App\Core\Authentication\Auth;

final class Ownership
{
    public static function owns(object $resource, $user = null, string $ownerKey='user_id'): bool
    {
        $user=$user?:Auth::user();
        if(!$user||empty($resource->id)||!isset($resource->{$ownerKey})) return false;
        return (string)$resource->{$ownerKey} === (string)$user->id;
    }
    public static function authorize(object $resource,$user=null,string $ownerKey='user_id'): void
    {
        if(!self::owns($resource,$user,$ownerKey)){http_response_code(403);throw new \RuntimeException('You do not own this resource.');}
    }
    public static function resolveAndAuthorize(string $model,$id,$user=null,string $ownerKey='user_id'): object
    {
        if(!class_exists($model)||!method_exists($model,'find')) throw new \InvalidArgumentException('Invalid resource model.');
        $resource=$model::find($id);
        if(!$resource){http_response_code(404);throw new \RuntimeException('Resource not found.');}
        self::authorize($resource,$user,$ownerKey);
        return $resource;
    }
}
