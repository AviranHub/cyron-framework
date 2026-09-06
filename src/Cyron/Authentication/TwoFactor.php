<?php
namespace Cyron\Authentication;
use Cyron\Database\ModelRegistry;
class TwoFactor {
 public static function enable(int $userId,string $channel,string $target): void {
    $model=ModelRegistry::get('user_two_factor'); $row=$model::query()->where('user_id','=',$userId)->first();
  $data=['channel'=>$channel,'target'=>$target,'enabled_at'=>date('Y-m-d H:i:s'),'disabled_at'=>null];
    if($row)$row->update($data);else $model::create(['user_id'=>$userId]+$data+['created_at'=>date('Y-m-d H:i:s')]);
 }
 public static function disable(int $userId): void { $model=ModelRegistry::get('user_two_factor'); $row=$model::query()->where('user_id','=',$userId)->first();if($row)$row->update(['disabled_at'=>date('Y-m-d H:i:s')]); }
 public static function enabled(int $userId): ?object { $model=ModelRegistry::get('user_two_factor'); return $model::query()->where('user_id','=',$userId)->where('disabled_at','=',null)->first(); }
 public static function challenge(int $userId): ?string {
  $two=self::enabled($userId);if(!$two)return null;
  $code=Verification::create($userId,$two->channel,$two->target,'two_factor');
  VerificationNotifier::sendCode($two->channel,$two->target,$code,'two_factor');
  return $code;
 }
 public static function verify(int $userId,string $channel,string $code): bool { return Verification::verify($userId,$channel,$code,'two_factor'); }
}