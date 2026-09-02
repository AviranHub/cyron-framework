<?php
namespace App\Core\Authentication;

use App\Models\User;
use App\Database\Db;

class PhoneVerification
{
    public static function generateCode($phone)
    {
        $code = (string) random_int(100000, 999999);
        $codeHash = hash('sha256', $code);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // حذف کدهای قبلی
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM phone_verification_codes WHERE phone = ?");
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        
        // ذخیره کد جدید
        $stmt = $db->prepare("INSERT INTO phone_verification_codes (phone, code, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $phone, $codeHash, $expires);
        $stmt->execute();
        
        return $code;
    }
    
    public static function verifyCode($phone, $code)
    {
        $db = Db::getInstance();
        $codeHash = hash('sha256', (string) $code);
        $stmt = $db->prepare("SELECT * FROM phone_verification_codes WHERE phone = ? AND code = ? AND expires_at > NOW()");
        $stmt->bind_param('ss', $phone, $codeHash);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            // حذف کد استفاده شده
            $stmt = $db->prepare("DELETE FROM phone_verification_codes WHERE phone = ?");
            $stmt->bind_param('s', $phone);
            $stmt->execute();
            
            // بروزرسانی کاربر
            User::where('phone', '=', $phone)->update(['phone_verified_at' => date('Y-m-d H:i:s')]);
            return true;
        }
        return false;
    }
    
    public static function isVerified($user)
    {
        return !empty($user->phone_verified_at);
    }
}