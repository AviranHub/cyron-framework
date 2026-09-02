<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Core\Authentication\Auth;
use App\Request;

class PhoneVerificationController extends Controller
{
    // نمایش فرم درخواست کد
    public function showVerifyForm()
    {
        $user = Auth::user();
        if (!$user || !$user->phone) {
            return redirect()->route('register');
        }
        return view('auth.verify-phone', ['phone' => $user->phone]);
    }

    // ارسال مجدد کد
    public function sendCode(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->phone) {
            return redirect()->route('register');
        }
        $user->sendPhoneVerificationCode();
        return redirect()->back()->with('success', 'کد تایید مجدد ارسال شد.');
    }

    // تایید کد
    public function verify(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->phone) {
            return redirect()->route('register');
        }

        $code = $request->input('code');
        if ($user->verifyPhone($code)) {
            return redirect()->route('dashboard')->with('success', 'شماره تلفن با موفقیت تایید شد.');
        }
        return redirect()->back()->with('error', 'کد نامعتبر یا منقضی شده است.');
    }
}