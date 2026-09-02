<?php
namespace App\Auth\Delivery;
class SmsVerificationChannel implements VerificationChannel {
 public function send(string $target,string $message,array $context=[]): bool {
  if(isset($context['provider']) && is_callable($context['provider'])) return (bool)call_user_func($context['provider'],$target,$message,$context);
  throw new \RuntimeException('No SMS provider has been configured.');
 }
}