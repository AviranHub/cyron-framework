<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use App\Models\Wallet;
use Cyron\Http\Request;
use Cyron\Authentication\Auth;
use Cyron\Authentication\AuthenticationPipeline;
use App\Events\UserRegistered;
use Cyron\Analytics\ActivityTracker;

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

        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        event(new UserRegistered((int) $user->id, $user->email, $user->name));
        ActivityTracker::record('user.registered', [], (int) $user->id);

        // لاگین خودکار
        Auth::login($user);
        Auth::markLogin($user);
        AuthenticationPipeline::succeeded('registration:' . (string)$user->id, (int)$user->id, session_id());

        // اگر شماره تلفن وارد شده، کد تایید بفرست
        if ($user->phone) {
            $user->sendPhoneVerificationCode();
            return redirect()->route('phone.verify');
        }

        return redirect()->route('user.dashboard');
    }
}