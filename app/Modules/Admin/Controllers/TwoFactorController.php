<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;use App\Auth\TwoFactor;use App\Audit\Audit;
class TwoFactorController extends Controller {
 public function enable($userId){$channel=request()->input('channel');$target=request()->input('target');TwoFactor::enable((int)$userId,$channel,$target);Audit::record('auth.2fa_enabled',['target_user_id'=>(int)$userId,'channel'=>$channel]);return redirect()->back();}
 public function disable($userId){TwoFactor::disable((int)$userId);Audit::record('auth.2fa_disabled',['target_user_id'=>(int)$userId]);return redirect()->back();}
}