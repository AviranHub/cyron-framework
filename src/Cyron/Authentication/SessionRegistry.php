<?php
namespace Cyron\Authentication;

use Cyron\Database\ModelRegistry;

class SessionRegistry
{
    public static function register(int $userId,string $token,array $meta=[]): void
    {
        $hash = hash('sha256', $token);
        $model = ModelRegistry::get('auth_session');
        $existing = $model::query()->where('token_hash','=',$hash)->first();
        $data = [
            'user_id'=>$userId,
            'token_hash'=>$hash,
            'ip_address'=>$meta['ip']??null,
            'user_agent'=>$meta['user_agent']??null,
            'last_seen_at'=>date('Y-m-d H:i:s'),
            'revoked_at'=>null,
        ];
        if ($existing) $existing->update($data);
        else $model::create($data + ['created_at'=>date('Y-m-d H:i:s')]);
    }

    public static function findByToken(string $token): ?object
    {
        $model = ModelRegistry::get('auth_session');
        return $model::query()->where('token_hash','=',hash('sha256',$token))->first();
    }

    public static function active(string $token): bool
    {
        $session = self::findByToken($token);
        return $session !== null && empty($session->revoked_at);
    }

    public static function touch(string $token): void
    {
        $session=self::findByToken($token);
        if($session && empty($session->revoked_at)) $session->update(['last_seen_at'=>date('Y-m-d H:i:s')]);
    }

    public static function revokeToken(string $token): void
    {
        $session=self::findByToken($token);
        if($session && empty($session->revoked_at)) $session->update(['revoked_at'=>date('Y-m-d H:i:s')]);
    }

    public static function revoke(int $id): void
    {
        $model = ModelRegistry::get('auth_session');
        $session=$model::find($id);
        if($session)$session->update(['revoked_at'=>date('Y-m-d H:i:s')]);
    }

    public static function revokeUser(int $userId): void
    {
        $model = ModelRegistry::get('auth_session');
        $model::query()->where('user_id','=',$userId)->where('revoked_at','=',null)->update(['revoked_at'=>date('Y-m-d H:i:s')]);
        RememberMe::revokeUser($userId);
    }
}
