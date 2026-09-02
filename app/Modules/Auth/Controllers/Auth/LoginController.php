<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Auth\LoginManager;
use App\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $login = (string)$request->input('login');
        $password = (string)$request->input('password');
        $result = LoginManager::attempt($login, $password);

        if (($result['status'] ?? null) === 'authenticated') {
            return redirect()->route('dashboard');
        }

        if (($result['status'] ?? null) === 'two_factor_required') {
            return redirect()->route('login.two-factor');
        }

        if (($result['status'] ?? null) === 'rate_limited') {
            return redirect()->back()->with('error', 'تلاش‌های ورود بیش از حد مجاز است. کمی بعد دوباره امتحان کنید.')->withInput();
        }

        return redirect()->back()->with('error', 'اطلاعات ورود صحیح نیست')->withInput();
    }

    public function showTwoFactorForm()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['pending_auth'])) return redirect()->route('login');
        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $result = LoginManager::completeTwoFactor((string)$request->input('code'));
        if (($result['status'] ?? null) === 'authenticated') return redirect()->route('dashboard');
        if (($result['status'] ?? null) === 'authentication_expired') {
            return redirect()->route('login')->with('error', 'درخواست ورود منقضی شد. دوباره وارد شوید.');
        }
        return redirect()->back()->with('error', 'کد ورود دومرحله‌ای صحیح نیست.');
    }

    public function logout()
    {
        LoginManager::logout();
        return redirect()->route('login');
    }
}
