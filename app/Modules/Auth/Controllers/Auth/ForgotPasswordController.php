<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use App\Request;
use App\Database\Db;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'ایمیل یافت نشد')->withInput();
        }

        // تولید توکن
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+' . config('auth.password_reset_expiry', 60) . ' minutes'));

        $db = Db::getInstance();
        // حذف توکن‌های قبلی
        $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        // ذخیره توکن جدید
        $stmt = $db->prepare("INSERT INTO password_reset_tokens (email, token, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param('ss', $email, $token);
        $stmt->execute();

        // در اینجا ایمیل حاوی لینک بازنشانی ارسال می‌شود (برای نمونه لاگ می‌کنیم)
        $resetLink = route('password.reset', ['token' => $token]);
        error_log("Password reset link for {$email}: {$resetLink}");

        return redirect()->back()->with('success', 'لینک بازنشانی رمز به ایمیل شما ارسال شد');
    }
}