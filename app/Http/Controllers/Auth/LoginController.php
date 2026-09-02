<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Auth\LoginManager;
use App\Request;

class LoginController extends Controller
{
    public function showLoginForm(){ return view('auth.login'); }

    public function login(Request $request)
    {
        $result=LoginManager::attempt((string)$request->input('login'),(string)$request->input('password'));
        if(($result['status']??'')==='authenticated') return redirect()->route('user.dashboard');
        if(($result['status']??'')==='two_factor_required') return redirect()->route('login.two-factor');
        $message=($result['status']??'')==='rate_limited'?'تعداد تلاش‌ها زیاد است. کمی بعد دوباره تلاش کنید.':'اطلاعات ورود صحیح نیست';
        return redirect()->back()->with('error',$message)->withInput();
    }

    public function showTwoFactorForm(){ return view('auth.two-factor'); }

    public function verifyTwoFactor(Request $request)
    {
        $result=LoginManager::completeTwoFactor((string)$request->input('code'));
        if(($result['status']??'')==='authenticated') return redirect()->route('user.dashboard');
        return redirect()->back()->with('error','کد تایید نامعتبر یا منقضی شده است');
    }

    public function logout(){ LoginManager::logout(); return redirect()->route('login'); }
}
