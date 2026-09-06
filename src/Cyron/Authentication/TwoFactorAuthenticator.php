<?php
namespace Cyron\Authentication;
use Cyron\Authentication\Totp;
use Cyron\Authentication\TotpCredential;
use Cyron\Authentication\TwoFactorRecovery;
class TwoFactorAuthenticator {
 public static function verify(int $userId,string $code): bool {
  $totp=Totp::enabled($userId);
  if($totp && Totp::verify(TotpCredential::secret($totp),$code))return true;
  return TwoFactorRecovery::consume($userId,$code);
 }
}