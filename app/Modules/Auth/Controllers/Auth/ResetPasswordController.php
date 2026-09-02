<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use App\Request;
use App\Database\Db;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        return view('auth.reset-password', compact('token'));
    }

    public function reset(Request $request)
    {
        $token = $request->input('token');
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');

        if ($password !== $passwordConfirmation) {
            return redirect()->back()->with('error', 'رمز عبور و تکرار آن مطابقت ندارد')->withInput();
        }

        $db = Db::getInstance();
        // بررسی توکن معتبر
        $stmt = $db->prepare("SELECT email FROM password_reset_tokens WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
        $expiry = config('auth.password_reset_expiry', 60);
        $stmt->bind_param('si', $token, $expiry);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return redirect()->route('password.request')->with('error', 'توکن نامعتبر یا منقضی شده است');
        }

        $email = $result['email'];
        $user = User::where('email', '=', $email)->first();
        if ($user) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $user->update(['password' => $hashed]);
        }

        // حذف توکن استفاده شده
        $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();

        return redirect()->route('login')->with('success', 'رمز عبور با موفقیت تغییر کرد. اکنون وارد شوید.');
    }
}