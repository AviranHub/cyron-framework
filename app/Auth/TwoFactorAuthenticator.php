<?php
namespace App\Auth;
class TwoFactorAuthenticator {
 public static function verify(int $userId,string $code): bool {
  $totp=Totp::enabled($userId);
  if($totp && Totp::verify(TotpCredential::secret($totp),$code))return true;
  return TwoFactorRecovery::consume($userId,$code);
 }
}