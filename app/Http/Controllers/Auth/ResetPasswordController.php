<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use App\Request;
use App\Database\Db;
use App\Auth\SessionRegistry;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        return view('auth.reset-password', compact('token'));
    }

    public function reset(Request $request)
    {
        $token = (string)$request->input('token');
        $tokenHash = hash('sha256', $token);
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');

        if ($password !== $passwordConfirmation) {
            return redirect()->back()->with('error', 'رمز عبور و تکرار آن مطابقت ندارد')->withInput();
        }

        $db = Db::getInstance();
        // بررسی توکن معتبر
        $stmt = $db->prepare("SELECT email FROM password_reset_tokens WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
        $expiry = config('auth.password_reset_expiry', 60);
        $stmt->bind_param('si', $tokenHash, $expiry);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return redirect()->route('password.request')->with('error', 'توکن نامعتبر یا منقضی شده است');
        }

        $email = $result['email'];
        $user = User::where('email', '=', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'کاربر یافت نشد');
        }
        if (strlen((string)$password) < 8) {
            return redirect()->back()->with('error', 'رمز عبور باید حداقل ۸ کاراکتر باشد')->withInput();
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $user->update(['password' => $hashed]);
        // Password changes invalidate every previously authenticated device.
        SessionRegistry::revokeUser((int)$user->id);

        // حذف توکن استفاده شده
        $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();

        return redirect()->route('login')->with('success', 'رمز عبور با موفقیت تغییر کرد. اکنون وارد شوید.');
    }
}