<?php
// app/Models/PersonalAccessToken.php

namespace App\Models;

use App\Database\Model;
use App\Models\User;

class PersonalAccessToken extends Model
{
    protected static $table = 'personal_access_tokens';

    protected static array $fillable = [
        'user_id', 'token', 'refresh_token', 'name', 'type',
        'expires_at', 'refresh_expires_at', 'last_used_at', 'revoked_at'
    ];

    protected array $dates = [
        'expires_at', 'refresh_expires_at', 'last_used_at', 
        'revoked_at', 'created_at', 'updated_at'
    ];

    /**
     * رابطه با کاربر
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * آیا توکن access معتبر است؟
     */
    public function isValidAccessToken(): bool
    {
        return $this->type === 'access' 
            && $this->revoked_at === null
            && ($this->expires_at === null || strtotime($this->expires_at) > time());
    }

    /**
     * آیا refresh token معتبر است؟
     */
    public function isValidRefreshToken(): bool
    {
        return $this->type === 'access' 
            && $this->refresh_token !== null
            && $this->revoked_at === null
            && ($this->refresh_expires_at === null || strtotime($this->refresh_expires_at) > time());
    }

    /**
     * آیا توکن منقضی شده است؟
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && strtotime($this->expires_at) < time();
    }

    /**
     * آیا refresh token منقضی شده است؟
     */
    public function isRefreshExpired(): bool
    {
        return $this->refresh_expires_at !== null && strtotime($this->refresh_expires_at) < time();
    }

    /**
     * به‌روزرسانی زمان آخرین استفاده
     */
    public function touchLastUsed(): void
    {
        $this->last_used_at = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * ابطال توکن (نرم)
     */
    public function revoke(): void
    {
        $this->revoked_at = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * آیا توکن باطل شده است؟
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}