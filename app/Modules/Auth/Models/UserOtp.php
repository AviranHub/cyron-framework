<?php
namespace App\Models;

use App\Database\Model;

class UserOtp extends Model
{
    protected static $table = 'user_otps';
    protected static array $fillable = ['user_id', 'otp_token', 'otp_type', 'otp_time', 'otp_ip', 'otp_code'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}