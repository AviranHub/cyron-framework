<?php
namespace Cyron\Authentication;
use Cyron\Database\ModelRegistry;
class PasswordReset {
 public static function issue(int $userId,string $rawToken,int $ttl=3600): void {
    $model=ModelRegistry::get('password_reset_token'); $model::query()->where('user_id','=',$userId)->delete();
    $model::create(['user_id'=>$userId,'token_hash'=>hash('sha256',$rawToken),'expires_at'=>date('Y-m-d H:i:s',time()+$ttl),'used_at'=>null,'created_at'=>date('Y-m-d H:i:s')]);
 }
 public static function consume(int $userId,string $rawToken): bool {
    $model=ModelRegistry::get('password_reset_token'); $row=$model::query()->where('user_id','=',$userId)->where('token_hash','=',hash('sha256',$rawToken))->first();
  if(!$row || $row->used_at || strtotime($row->expires_at)<time())return false;
  $row->update(['used_at'=>date('Y-m-d H:i:s')]); return true;
 }
}