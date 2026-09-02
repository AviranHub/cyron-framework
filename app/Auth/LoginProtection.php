<?php
namespace App\Auth;
use App\Models\LoginAttempt;
class LoginProtection {
 public static function check(string $key,int $max=5,int $window=900): bool {
  $since=date('Y-m-d H:i:s',time()-$window);
  $count=LoginAttempt::query()->where('key','=',$key)->where('successful','=',0)->where('occurred_at','>=',$since)->count();
  return $count < $max;
 }
 public static function record(string $key,bool $successful): void {
  LoginAttempt::create(['key'=>$key,'successful'=>$successful?1:0,'occurred_at'=>date('Y-m-d H:i:s')]);
 }
 public static function retryAfter(string $key,int $window=900): int {
  $row=LoginAttempt::query()->where('key','=',$key)->where('successful','=',0)->orderBy('occurred_at','asc')->first();
  if(!$row)return 0;
  return max(0,$window-(time()-strtotime($row->occurred_at)));
 }
}