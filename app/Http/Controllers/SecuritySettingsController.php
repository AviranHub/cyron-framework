<?php
namespace App\Http\Controllers;
use App\Http\Controller;use App\Auth\Totp;use App\Auth\TotpCredential;use App\Auth\TwoFactor;use App\Auth\TwoFactorRecovery;
class SecuritySettingsController extends Controller {
 public function setupTotp(){ $user=auth()->user();$secret=Totp::generateSecret();$_SESSION['cyron_totp_pending']=$secret;$uri=Totp::provisioningUri('Cyron',$user->email ?? ('user-'.$user->id),$secret);return view('account.security.totp-setup',compact('secret','uri')); }
 public function confirmTotp(){ $user=auth()->user();$secret=$_SESSION['cyron_totp_pending'] ?? null;$code=request()->input('code');if(!$secret||!Totp::verify($secret,$code)) return redirect()->back()->with('error','کد تأیید صحیح نیست.');TotpCredential::enable($user->id,$secret);unset($_SESSION['cyron_totp_pending']);return redirect('/account/security')->with('success','Authenticator فعال شد.'); }
 public function disableTwoFactor(){ $user=auth()->user();TwoFactor::disable($user->id);return redirect('/account/security')->with('success','ورود دومرحله‌ای غیرفعال شد.'); }
 public function recoveryCodes(){ $user=auth()->user();$codes=TwoFactorRecovery::generate($user->id);return view('account.security.recovery-codes',compact('codes')); }
}