<?php
namespace Cyron\Authentication;
use Cyron\Database\ModelRegistry;
class TwoFactorRecovery {
 public static function generate(int $userId,int $count=10): array {
    $model=ModelRegistry::get('two_factor_recovery_code'); $model::query()->where('user_id','=',$userId)->delete();$codes=[];
    for($i=0;$i<$count;$i++){ $code=strtoupper(bin2hex(random_bytes(4)));$codes[]=$code;$model::create(['user_id'=>$userId,'code_hash'=>hash('sha256',$code),'used_at'=>null,'created_at'=>date('Y-m-d H:i:s')]);}
  return $codes;
 }
 public static function consume(int $userId,string $code): bool {
    $model=ModelRegistry::get('two_factor_recovery_code'); $row=$model::query()->where('user_id','=',$userId)->where('code_hash','=',hash('sha256',strtoupper(trim($code))))->where('used_at','=',null)->first();
  if(!$row)return false;$row->update(['used_at'=>date('Y-m-d H:i:s')]);return true;
 }
}