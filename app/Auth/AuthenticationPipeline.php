<?php
namespace App\Auth;
class AuthenticationPipeline {
 public static function beforeLogin(string $key): array {
  if(!LoginProtection::check($key)) return ['allowed'=>false,'retry_after'=>LoginProtection::retryAfter($key)];
  return ['allowed'=>true];
 }
 public static function failed(string $key,?int $userId=null,array $context=[]): void {
  LoginProtection::record($key,false); LoginTracker::record($userId,false,$context);
 }
 public static function succeeded(string $key,int $userId,string $sessionToken,array $context=[]): void {
  LoginProtection::record($key,true); LoginTracker::record($userId,true,$context);
  $request=function_exists('request')?request():null;
  SessionRegistry::register($userId,$sessionToken,['ip'=>$request&&method_exists($request,'ip')?$request->ip():null,'user_agent'=>$request&&method_exists($request,'userAgent')?$request->userAgent():null]);
 }
}