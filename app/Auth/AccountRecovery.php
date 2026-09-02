<?php
namespace App\Auth;
use App\Models\User;
class AccountRecovery {
 public static function findUser(string $identifier): ?User {
  $identifier=trim($identifier);
  if(filter_var($identifier,FILTER_VALIDATE_EMAIL)) return User::query()->where('email','=',$identifier)->first();
  return User::query()->where('phone','=',$identifier)->first();
 }
 public static function channel(string $identifier): string {
  return filter_var(trim($identifier),FILTER_VALIDATE_EMAIL)?'email':'phone';
 }
 public static function createReset(int $userId): string {
  $token=bin2hex(random_bytes(32)); PasswordReset::issue($userId,$token); return $token;
 }
}