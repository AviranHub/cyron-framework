<?php
namespace App\Auth;
use App\Models\VerificationChallenge;
class Verification {
 public static function create(int $userId,string $channel,string $target,string $purpose='verify',int $ttl=600): string {
  $code=(string)random_int(100000,999999);
  VerificationChallenge::create(['user_id'=>$userId,'channel'=>$channel,'target'=>$target,'code_hash'=>hash('sha256',$code),'purpose'=>$purpose,'expires_at'=>date('Y-m-d H:i:s',time()+$ttl),'verified_at'=>null,'created_at'=>date('Y-m-d H:i:s')]);
  return $code;
 }
 public static function verify(int $userId,string $channel,string $code,string $purpose='verify'): bool {
  $row=VerificationChallenge::query()->where('user_id','=',$userId)->where('channel','=',$channel)->where('purpose','=',$purpose)->where('code_hash','=',hash('sha256',$code))->orderBy('created_at','desc')->first();
  if(!$row||$row->verified_at||strtotime($row->expires_at)<time())return false;
  $row->update(['verified_at'=>date('Y-m-d H:i:s')]);return true;
 }
}