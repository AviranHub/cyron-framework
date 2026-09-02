<?php
// app/Core/Authentication/Tokenizer.php

namespace App\Core\Authentication;

use App\Models\PersonalAccessToken;
use App\Models\User;

class Tokenizer
{
    // ==================== تولید توکن ====================

    /**
     * تولید یک توکن تصادفی امن
     */
    public static function make(int $bytes = 40): string
    {
        return bin2hex(random_bytes($bytes));
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * ایجاد جفت توکن (Access + Refresh)
     */
    public static function createTokenPair(
        User $user, 
        string $deviceName = 'Unknown',
        int $accessExpiresIn = 3600,    // ۱ ساعت
        int $refreshExpiresIn = 604800  // ۷ روز
    ): array {
        // ۱. تولید توکن‌ها
        $accessToken = self::make();
        $refreshToken = self::make();

        // ۲. محاسبه زمان انقضا
        $accessExpiresAt = date('Y-m-d H:i:s', time() + $accessExpiresIn);
        $refreshExpiresAt = date('Y-m-d H:i:s', time() + $refreshExpiresIn);

        // ۳. ذخیره در دیتابیس
        $record = PersonalAccessToken::create([
            'user_id' => $user->id,
            'token' => self::hash($accessToken),
            'refresh_token' => self::hash($refreshToken),
            'name' => $deviceName,
            'type' => 'access',
            'expires_at' => $accessExpiresAt,
            'refresh_expires_at' => $refreshExpiresAt,
            'last_used_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $accessExpiresIn,
            'refresh_expires_in' => $refreshExpiresIn,
            'token_type' => 'Bearer',
            'device' => $deviceName,
        ];
    }

    // ==================== اعتبارسنجی ====================

    /**
     * دریافت کاربر از روی Access Token
     */
    public static function getUserByAccessToken(string $token): ?User
    {
        $record = PersonalAccessToken::where('token', '=', self::hash($token))->first();
        
        if (!$record || !$record->isValidAccessToken()) {
            return null;
        }

        $record->touchLastUsed();
        return $record->user;
    }

    /**
     * بررسی معتبر بودن Access Token
     */
    public static function isValidAccessToken(string $token): bool
    {
        $record = PersonalAccessToken::where('token', '=', self::hash($token))->first();
        return $record && $record->isValidAccessToken();
    }

    /**
     * بررسی معتبر بودن Refresh Token
     */
    public static function isValidRefreshToken(string $refreshToken): bool
    {
        $record = PersonalAccessToken::where('refresh_token', '=', self::hash($refreshToken))->first();
        return $record && $record->isValidRefreshToken();
    }

    // ==================== تمدید توکن ====================

    /**
     * تمدید Access Token با استفاده از Refresh Token
     */
    public static function refreshAccessToken(string $refreshToken, int $newExpiresIn = 3600): ?array
    {
        // ۱. پیدا کردن رکورد با refresh_token معتبر
        $record = PersonalAccessToken::where('refresh_token', '=', self::hash($refreshToken))->first();

        if (!$record || !$record->isValidRefreshToken()) {
            return null;
        }

        // ۲. تولید Access Token جدید
        $newAccessToken = self::make();
        $newExpiresAt = date('Y-m-d H:i:s', time() + $newExpiresIn);

        // ۳. بروزرسانی رکورد
        $record->token = self::hash($newAccessToken);
        $record->expires_at = $newExpiresAt;
        $record->last_used_at = date('Y-m-d H:i:s');
        $record->save();

        return [
            'access_token' => $newAccessToken,
            'expires_in' => $newExpiresIn,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * تمدید هر دو توکن (Access + Refresh)
     */
    public static function refreshBothTokens(
        string $refreshToken,
        int $newAccessExpiresIn = 3600,
        int $newRefreshExpiresIn = 604800
    ): ?array {
        $record = PersonalAccessToken::where('refresh_token', '=', self::hash($refreshToken))->first();

        if (!$record || !$record->isValidRefreshToken()) {
            return null;
        }

        // تولید توکن‌های جدید
        $newAccessToken = self::make();
        $newRefreshToken = self::make();

        $newAccessExpiresAt = date('Y-m-d H:i:s', time() + $newAccessExpiresIn);
        $newRefreshExpiresAt = date('Y-m-d H:i:s', time() + $newRefreshExpiresIn);

        // بروزرسانی رکورد
        $record->token = self::hash($newAccessToken);
        $record->refresh_token = self::hash($newRefreshToken);
        $record->expires_at = $newAccessExpiresAt;
        $record->refresh_expires_at = $newRefreshExpiresAt;
        $record->last_used_at = date('Y-m-d H:i:s');
        $record->save();

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'expires_in' => $newAccessExpiresIn,
            'refresh_expires_in' => $newRefreshExpiresIn,
            'token_type' => 'Bearer',
        ];
    }

    // ==================== ابطال توکن ====================

    /**
     * ابطال یک توکن خاص (خروج از یک دستگاه)
     */
    public static function revokeToken(string $token): bool
    {
        $record = PersonalAccessToken::where('token', '=', self::hash($token))->first();
        if ($record) {
            $record->revoke();
            return true;
        }
        return false;
    }

    /**
     * ابطال با refresh token (خروج از یک دستگاه)
     */
    public static function revokeByRefreshToken(string $refreshToken): bool
    {
        $record = PersonalAccessToken::where('refresh_token', '=', self::hash($refreshToken))->first();
        if ($record) {
            $record->revoke();
            return true;
        }
        return false;
    }

    /**
     * ابطال همه توکن‌های یک کاربر (خروج از همه دستگاه‌ها)
     */
    public static function revokeAllForUser(int $userId): int
    {
        $records = PersonalAccessToken::where('user_id', '=', $userId)->get();
        $count = 0;
        foreach ($records as $record) {
            $record->revoke();
            $count++;
        }
        return $count;
    }

    /**
     * ابطال توکن‌های یک دستگاه خاص
     */
    public static function revokeDeviceTokens(int $userId, string $deviceName): int
    {
        $records = PersonalAccessToken::where('user_id', '=', $userId)
            ->where('name', '=', $deviceName)
            ->get();
        
        $count = 0;
        foreach ($records as $record) {
            $record->revoke();
            $count++;
        }
        return $count;
    }

    // ==================== پاکسازی ====================

    /**
     * پاکسازی توکن‌های منقضی شده و باطل شده
     */
    public static function pruneExpired(): int
    {
        $count = 0;
        $records = PersonalAccessToken::all();
        
        foreach ($records as $record) {
            // حذف اگر:
            // 1. باطل شده باشد
            // 2. هر دو access و refresh منقضی شده باشند
            if ($record->isRevoked() || 
                ($record->isExpired() && $record->isRefreshExpired())) {
                if ($record->delete()) {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    // ==================== اطلاعات ====================

    /**
     * دریافت اطلاعات یک توکن (برای مدیریت)
     */
    public static function getTokenInfo(string $token): ?array
    {
        $record = PersonalAccessToken::where('token', '=', self::hash($token))
            ->orWhere('refresh_token', '=', self::hash($token))
            ->first();
            
        if (!$record) return null;

        return [
            'id' => $record->id,
            'user_id' => $record->user_id,
            'user_name' => $record->user->name ?? 'Unknown',
            'device' => $record->name,
            'type' => $record->type,
            'expires_at' => $record->expires_at,
            'refresh_expires_at' => $record->refresh_expires_at,
            'last_used_at' => $record->last_used_at,
            'is_revoked' => $record->isRevoked(),
            'is_valid' => $record->isValidAccessToken(),
        ];
    }

    /**
     * لیست همه دستگاه‌های یک کاربر
     */
    public static function getUserDevices(int $userId): array
    {
        $records = PersonalAccessToken::where('user_id', '=', $userId)
            ->whereNull('revoked_at')
            ->orderBy('last_used_at', 'desc')
            ->get();

        $devices = [];
        foreach ($records as $record) {
            $devices[] = [
                'id' => $record->id,
                'name' => $record->name,
                'last_used_at' => $record->last_used_at,
                'created_at' => $record->created_at,
                'is_current' => false, // این را در کنترلر تنظیم کن
            ];
        }
        return $devices;
    }
}