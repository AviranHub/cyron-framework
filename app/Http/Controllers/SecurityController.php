<?php
namespace App\Http\Controllers;
use App\Http\Controller;use App\Auth\TwoFactor;use App\Auth\TwoFactorRecovery;
class SecurityController extends Controller {
 public function index(){ $user=auth()->user();$two=TwoFactor::enabled($user->id);return view('account.security',compact('user','two')); }
 public function recoveryCodes(){ $user=auth()->user();$codes=TwoFactorRecovery::generate($user->id);return view('account.recovery-codes',compact('codes')); }
}