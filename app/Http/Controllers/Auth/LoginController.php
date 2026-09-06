<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use Cyron\Authentication\LoginManager;
use App\Models\User;
use Cyron\Http\Request;
use Cyron\Authentication\Auth;

class LoginController extends Controller
{
    public function showLoginForm(){
        if (Auth::check()) return $this->redirectAfterLogin(Auth::id());
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $remember = filter_var($request->input('remember', false), FILTER_VALIDATE_BOOLEAN);
        $result=LoginManager::attempt((string)$request->input('login'),(string)$request->input('password'),$remember);
        if(($result['status']??'')==='authenticated') return $this->redirectAfterLogin($result['user_id'] ?? null);
        if(($result['status']??'')==='two_factor_required') return redirect()->route('login.two-factor');
        $message=($result['status']??'')==='rate_limited'?'تعداد تلاش‌ها زیاد است. کمی بعد دوباره تلاش کنید.':'اطلاعات ورود صحیح نیست';
        return redirect()->back()->with('error',$message)->withInput();
    }

    public function showTwoFactorForm(){ return view('auth.two-factor'); }

    public function verifyTwoFactor(Request $request)
    {
        $result=LoginManager::completeTwoFactor((string)$request->input('code'));
        if(($result['status']??'')==='authenticated') return $this->redirectAfterLogin($result['user_id'] ?? null);
        return redirect()->back()->with('error','کد تایید نامعتبر یا منقضی شده است');
    }

    public function logout(){ LoginManager::logout(); return redirect()->route('login'); }

    private function redirectAfterLogin($userId)
    {
        $user = $userId ? User::find($userId) : null;
        return $user && in_array((string)($user->role ?? ''), ['admin', 'superadmin'], true)
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
}
