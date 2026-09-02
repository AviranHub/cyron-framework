<?php
namespace App\Http\Controllers\User;

use App\Http\Controller;
use App\Core\Authentication\Auth;
use App\Request;

class DashboardController extends Controller
{
    /**
     * نمایش داشبورد کاربر
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.dashboard', compact('user'));
    }

    /**
     * نمایش فرم ویرایش پروفایل
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('user.edit-profile', compact('user'));
    }

    /**
     * به‌روزرسانی پروفایل کاربر
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'name'  => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|min:11|max:15|unique:users,phone,' . $user->id,
        ];
        
        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        
        $user->update([
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);
        
        return redirect()->route('user.dashboard')->with('success', 'پروفایل با موفقیت به‌روزرسانی شد.');
    }

    /**
     * نمایش فرم تغییر رمز عبور
     */
    public function showChangePassword()
    {
        return view('user.change-password');
    }

    /**
     * تغییر رمز عبور
     */
    public function changePassword(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ];
        
        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        
        $user = Auth::user();
        
        if (!password_verify($request->input('current_password'), $user->password)) {
            return redirect()->back()->with('error', 'رمز عبور فعلی اشتباه است.');
        }
        
        $user->update([
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT)
        ]);
        
        return redirect()->route('user.dashboard')->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}