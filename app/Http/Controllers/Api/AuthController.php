<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controller;
use App\Request;
use App\Core\Authentication\Auth;
use App\Core\Authentication\Tokenizer;
use App\Models\User;
use App\Models\PersonalAccessToken;

class AuthController extends Controller
{
    // ==================== ثبت‌نام ====================

    /**
     * ثبت‌نام کاربر جدید
     */
    public function register(Request $request)
    {
        // ۱. اعتبارسنجی
        $rules = [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|regex:/^09[0-9]{9}$/|unique:users,phone',
            'password' => 'required|min:6|confirmed',
            'device_name' => 'nullable|string|max:255',
        ];

        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return response()->validationError($errors->all(), 'خطاهای اعتبارسنجی');
        }

        // ۲. ایجاد کاربر
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
            'role' => config('auth.default_role', 'user'),
        ]);

        if (!$user) {
            return response()->error('ثبت‌نام ناموفق بود', 500);
        }

        // ۳. تولید توکن‌ها
        $device = $request->input('device_name', 'Mobile App');
        $tokens = Tokenizer::createTokenPair($user, $device);

        // ۴. پاسخ موفقیت
        return response()->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => $user->created_at,
            ],
            'tokens' => $tokens,
        ], 'ثبت‌نام با موفقیت انجام شد', 201);
    }

    // ==================== ورود ====================

    /**
     * ورود کاربر و دریافت توکن‌ها
     */
    public function login(Request $request)
    {
        // ۱. دریافت ورودی‌ها
        $login = $request->input('login');
        $password = $request->input('password');
        $device = $request->input('device_name', 'Mobile App');

        // ۲. اعتبارسنجی ساده
        if (empty($login) || empty($password)) {
            return response()->badRequest('لطفاً نام کاربری و رمز عبور را وارد کنید');
        }

        // ۳. احراز هویت
        if (!Auth::attempt($login, $password)) {
            return response()->unauthorized('اطلاعات ورود صحیح نیست');
        }

        // ۴. دریافت کاربر
        $user = Auth::user();
        if (!$user) {
            return response()->error('کاربر یافت نشد', 404);
        }

        // ۵. تولید توکن‌ها
        $tokens = Tokenizer::createTokenPair($user, $device);

        // ۶. پاسخ موفقیت
        return response()->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar ?? null,
                'role' => $user->role ?? 'user',
            ],
            'tokens' => $tokens,
        ], 'ورود با موفقیت انجام شد');
    }

    // ==================== تمدید توکن ====================

    /**
     * تمدید Access Token با Refresh Token
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->badRequest('Refresh Token ارائه نشده است');
        }

        // تمدید توکن
        $newTokens = Tokenizer::refreshAccessToken($refreshToken);

        if (!$newTokens) {
            return response()->unauthorized('Refresh Token نامعتبر یا منقضی شده است');
        }

        return response()->success($newTokens, 'توکن جدید صادر شد');
    }

    // ==================== خروج ====================

    /**
     * خروج از دستگاه فعلی
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->badRequest('توکن ارائه نشده است');
        }

        if (Tokenizer::revokeToken($token)) {
            return response()->success(null, 'خروج با موفقیت انجام شد');
        }

        return response()->error('خروج ناموفق', 400);
    }

    /**
     * خروج از یک دستگاه خاص (با Refresh Token)
     */
    public function logoutDevice(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->badRequest('Refresh Token ارائه نشده است');
        }

        if (Tokenizer::revokeByRefreshToken($refreshToken)) {
            return response()->success(null, 'خروج از دستگاه با موفقیت انجام شد');
        }

        return response()->notFound('دستگاه یافت نشد');
    }

    /**
     * خروج از همه دستگاه‌ها
     */
    public function logoutAllDevices(Request $request)
    {
        $user = $request->user;

        if (!$user) {
            return response()->notFound('کاربر یافت نشد');
        }

        $count = Tokenizer::revokeAllForUser($user->id);

        return response()->success(
            ['revoked_count' => $count],
            "خروج از {$count} دستگاه با موفقیت انجام شد"
        );
    }

    // ==================== اطلاعات کاربر ====================

    /**
     * دریافت اطلاعات کاربر جاری
     */
    public function profile(Request $request)
    {
        $user = $request->user;

        return response()->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'avatar' => $user->avatar ?? null,
            'role' => $user->role ?? 'user',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'اطلاعات کاربر');
    }

    /**
     * به‌روزرسانی اطلاعات کاربر
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user;

        $rules = [
            'name' => 'nullable|string|min:3|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|regex:/^09[0-9]{9}$/|unique:users,phone,' . $user->id,
        ];

        $errors = $request->validate($rules);
        if ($errors && $errors->any()) {
            return response()->validationError($errors->all(), 'خطاهای اعتبارسنجی');
        }

        // به‌روزرسانی فیلدها
        $data = [];
        if ($request->input('name')) $data['name'] = $request->input('name');
        if ($request->input('email')) $data['email'] = $request->input('email');
        if ($request->input('phone')) $data['phone'] = $request->input('phone');

        if (!empty($data)) {
            $user->update($data);
        }

        return response()->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ], 'اطلاعات کاربر با موفقیت به‌روزرسانی شد');
    }

    // ==================== تغییر رمز عبور ====================

    /**
     * تغییر رمز عبور و باطل کردن توکن‌های سایر دستگاه‌ها
     */
    public function changePassword(Request $request)
    {
        $user = $request->user;

        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
        $newPasswordConfirmation = $request->input('new_password_confirmation');

        // ۱. اعتبارسنجی
        if (empty($currentPassword) || empty($newPassword)) {
            return response()->badRequest('لطفاً رمز فعلی و جدید را وارد کنید');
        }

        if ($newPassword !== $newPasswordConfirmation) {
            return response()->badRequest('رمز جدید و تکرار آن مطابقت ندارد');
        }

        if (strlen($newPassword) < 6) {
            return response()->badRequest('رمز جدید باید حداقل ۶ کاراکتر باشد');
        }

        // ۲. بررسی رمز فعلی
        if (!password_verify($currentPassword, $user->password)) {
            return response()->unauthorized('رمز فعلی صحیح نیست');
        }

        // ۳. تغییر رمز
        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->save();

        // ۴. باطل کردن همه توکن‌ها (به جز دستگاه فعلی)
        $currentToken = $request->bearerToken();
        $allTokens = PersonalAccessToken::where('user_id', '=', $user->id)->get();

        $revokedCount = 0;
        foreach ($allTokens as $token) {
            if ($token->token !== $currentToken) {
                $token->revoke();
                $revokedCount++;
            }
        }

        return response()->success(
            ['revoked_other_devices' => $revokedCount],
            'رمز عبور با موفقیت تغییر کرد. توکن‌های سایر دستگاه‌ها باطل شدند.'
        );
    }

    // ==================== لیست دستگاه‌ها ====================

    /**
     * لیست دستگاه‌های فعال کاربر
     */
    public function devices(Request $request)
    {
        $user = $request->user;
        $currentToken = $request->bearerToken();

        $devices = Tokenizer::getUserDevices($user->id);

        // مشخص کردن دستگاه فعلی
        foreach ($devices as &$device) {
            $record = PersonalAccessToken::find($device['id']);
            if ($record && $record->token === $currentToken) {
                $device['is_current'] = true;
            }
        }

        return response()->success($devices, 'لیست دستگاه‌های فعال');
    }

    /**
     * حذف یک دستگاه خاص (با شناسه)
     */
    public function revokeDevice(Request $request)
    {
        $user = $request->user;
        $deviceId = $request->input('device_id');

        if (!$deviceId) {
            return response()->badRequest('شناسه دستگاه ارائه نشده است');
        }

        $token = PersonalAccessToken::find($deviceId);

        if (!$token || $token->user_id !== $user->id) {
            return response()->notFound('دستگاه یافت نشد');
        }

        // جلوگیری از حذف دستگاه فعلی
        $currentToken = $request->bearerToken();
        if ($token->token === $currentToken) {
            return response()->badRequest('نمی‌توانید دستگاه فعلی را حذف کنید. از مسیر logout استفاده کنید.');
        }

        $token->revoke();

        return response()->success(null, 'دستگاه با موفقیت حذف شد');
    }
}