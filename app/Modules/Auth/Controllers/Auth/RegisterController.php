<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use App\Request;
use App\Core\Authentication\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // اعتبارسنجی
        $rules = [
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|regex:/^09[0-9]{9}$/|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ];

        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // ایجاد کاربر
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'phone'    => $request->input('phone'),
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
            'role'     => config('auth.default_role', 'user'),
        ]);

        // لاگین خودکار
        Auth::login($user);

        // اگر شماره تلفن وارد شده، کد تایید بفرست
        if ($user->phone) {
            $user->sendPhoneVerificationCode();
            return redirect()->route('phone.verify');
        }

        return redirect()->route('dashboard');
    }
}