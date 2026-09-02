<?php
namespace App\Auth;
use App\Models\UserTwoFactor;
class TwoFactor {
 public static function enable(int $userId,string $channel,string $target): void {
  $row=UserTwoFactor::query()->where('user_id','=',$userId)->first();
  $data=['channel'=>$channel,'target'=>$target,'enabled_at'=>date('Y-m-d H:i:s'),'disabled_at'=>null];
  if($row)$row->update($data);else UserTwoFactor::create(['user_id'=>$userId]+$data+['created_at'=>date('Y-m-d H:i:s')]);
 }
 public static function disable(int $userId): void { $row=UserTwoFactor::query()->where('user_id','=',$userId)->first();if($row)$row->update(['disabled_at'=>date('Y-m-d H:i:s')]); }
 public static function enabled(int $userId): ?UserTwoFactor { return UserTwoFactor::query()->where('user_id','=',$userId)->where('disabled_at','=',null)->first(); }
 public static function challenge(int $userId): ?string {
  $two=self::enabled($userId);if(!$two)return null;
  $code=Verification::create($userId,$two->channel,$two->target,'two_factor');
  VerificationNotifier::sendCode($two->channel,$two->target,$code,'two_factor');
  return $code;
 }
 public static function verify(int $userId,string $channel,string $code): bool { return Verification::verify($userId,$channel,$code,'two_factor'); }
}