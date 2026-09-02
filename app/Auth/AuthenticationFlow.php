<?php
namespace App\Auth;
class AuthenticationFlow {
 public static function passwordAccepted(string $key,int $userId,string $sessionToken): array {
  $two=TwoFactor::enabled($userId);
  if($two){ TwoFactor::challenge($userId); return ['status'=>'two_factor_required','user_id'=>$userId,'channel'=>$two->channel]; }
  AuthenticationPipeline::succeeded($key,$userId,$sessionToken); return ['status'=>'authenticated','user_id'=>$userId];
 }
 public static function passwordRejected(string $key,?int $userId=null,array $context=[]): array {
  AuthenticationPipeline::failed($key,$userId,$context); return ['status'=>'invalid_credentials'];
 }
 public static function completeTwoFactor(string $key,int $userId,string $channel,string $code,string $sessionToken): array {
  if(!TwoFactor::verify($userId,$channel,$code)) return ['status'=>'invalid_two_factor_code'];
  AuthenticationPipeline::succeeded($key,$userId,$sessionToken,['two_factor'=>true]);
  return ['status'=>'authenticated','user_id'=>$userId];
 }
}