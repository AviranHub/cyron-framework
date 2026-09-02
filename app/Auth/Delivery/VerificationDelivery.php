<?php
namespace App\Auth\Delivery;
class VerificationDelivery {
 protected static array $channels=[];
 public static function register(string $name,VerificationChannel $channel): void { self::$channels[$name]=$channel; }
 public static function send(string $channel,string $target,string $message,array $context=[]): bool {
  if(!isset(self::$channels[$channel])) throw new \RuntimeException("Verification channel [$channel] is not registered.");
  return self::$channels[$channel]->send($target,$message,$context);
 }
}