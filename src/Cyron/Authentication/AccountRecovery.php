<?php
namespace Cyron\Authentication;
class AccountRecovery {
 public static function findUser(string $identifier): ?object {
  $identifier=trim($identifier);
    $model=\Cyron\Database\ModelRegistry::get('user');
    if(filter_var($identifier,FILTER_VALIDATE_EMAIL)) return $model::query()->where('email','=',$identifier)->first();
    return $model::query()->where('phone','=',$identifier)->first();
 }
 public static function channel(string $identifier): string {
  return filter_var(trim($identifier),FILTER_VALIDATE_EMAIL)?'email':'phone';
 }
 public static function createReset(int $userId): string {
  $token=bin2hex(random_bytes(32)); PasswordReset::issue($userId,$token); return $token;
 }
}