<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;use App\Models\LoginAttempt;
class LoginSecurityController extends Controller {
 public function attempts(){
  $logs=LoginAttempt::query()->where('successful','=',0)->orderBy('occurred_at','desc')->paginate(100);
  return view('admin.security.attempts',compact('logs'));
 }
}