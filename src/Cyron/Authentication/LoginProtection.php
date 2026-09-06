<?php
namespace Cyron\Authentication;
use Cyron\Database\ModelRegistry;
class LoginProtection {
 public static function check(string $key,int $max=5,int $window=900): bool {
  $since=date('Y-m-d H:i:s',time()-$window);
    $model=ModelRegistry::get('login_attempt'); $count=$model::query()->where('key','=',$key)->where('successful','=',0)->where('occurred_at','>=',$since)->count();
  return $count < $max;
 }
 public static function record(string $key,bool $successful): void {
    $model=ModelRegistry::get('login_attempt'); $model::create(['key'=>$key,'successful'=>$successful?1:0,'occurred_at'=>date('Y-m-d H:i:s')]);
 }
 public static function retryAfter(string $key,int $window=900): int {
    $model=ModelRegistry::get('login_attempt'); $row=$model::query()->where('key','=',$key)->where('successful','=',0)->orderBy('occurred_at','asc')->first();
  if(!$row)return 0;
  return max(0,$window-(time()-strtotime($row->occurred_at)));
 }
}