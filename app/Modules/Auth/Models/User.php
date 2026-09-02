<?php
namespace App\Models;

use App\Database\Model;
use App\Core\Authentication\PhoneVerification;

class User extends Model
{
    protected static $table = 'users';
    protected static array $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'phone_verified_at', 'email_verified_at', 'remember_token',
        'login_count', 'last_login_at', 'status', 'suspended_until', 'primary_role_id'
    ];


    // رابطه‌ها (اختیاری)
    // public function articles()
    // {
    //     return $this->hasMany(Article::class, 'author_id');
    // }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class, 'author_id');
    // }
    
    // روابط
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function primaryRole()
    {
        return $this->belongsTo(Role::class, 'primary_role_id');
    }

    public function otps()
    {
        return $this->hasMany(UserOtp::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    // متدهای کمکی
    public function isPhoneVerified()
    {
        return !is_null($this->phone_verified_at);
    }

    public function isEmailVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function verifyPhone($code)
    {
        if (PhoneVerification::verifyCode($this->phone, $code)) {
            $this->update(['phone_verified_at' => date('Y-m-d H:i:s')]);
            return true;
        }
        return false;
    }

    public function sendPhoneVerificationCode()
    {
        if (!$this->phone) return false;
        $code = PhoneVerification::generateCode($this->phone);
        // در اینجا کد را از طریق SMS ارسال کنید
        error_log("Phone verification code for {$this->phone}: {$code}");
        return true;
    }

    public function hasRole($role)
    {
        foreach ($this->roles as $userRole) {
            if ($userRole->slug === $role || $userRole->name === $role) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $perm) {
                if ($perm->slug === $permission || $perm->name === $permission) {
                    return true;
                }
            }
        }
        return false;
    }
}