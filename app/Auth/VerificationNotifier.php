<?php
namespace App\Auth;
use App\Auth\Delivery\VerificationDelivery;
class VerificationNotifier {
 public static function sendCode(string $channel,string $target,string $code,string $purpose='verify',array $context=[]): bool {
  $message=$context['message'] ?? "Your verification code is: $code";
  return VerificationDelivery::send($channel,$target,$message,$context+['purpose'=>$purpose]);
 }
}