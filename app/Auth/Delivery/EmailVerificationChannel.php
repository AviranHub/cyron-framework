<?php
namespace App\Auth\Delivery;
class EmailVerificationChannel implements VerificationChannel {
 public function send(string $target,string $message,array $context=[]): bool {
  if(function_exists('mail')) return @mail($target,$context['subject'] ?? 'Verification',$message);
  return false;
 }
}