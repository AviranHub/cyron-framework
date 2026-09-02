<?php
namespace App\Auth;
use App\Models\LoginHistory;
class LoginTracker {
 public static function record(?int $userId,bool $successful,array $extra=[]): void {
  $request=function_exists('request')?request():null;
  LoginHistory::create([
   'user_id'=>$userId,
   'successful'=>$successful ? 1 : 0,
   'ip_address'=>$request && method_exists($request,'ip') ? $request->ip() : null,
   'user_agent'=>$request && method_exists($request,'userAgent') ? $request->userAgent() : null,
   'context'=>json_encode($extra,JSON_UNESCAPED_UNICODE),
   'occurred_at'=>date('Y-m-d H:i:s'),
  ]);
 }
}