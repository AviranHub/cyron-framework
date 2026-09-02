<?php
namespace App\Models;

use App\Database\Model;

class UserActivity extends Model
{
    protected static $table = 'user_activities';
    protected static array $fillable = [
        'user_id', 'category', 'action', 'subject_type', 'subject_id',
        'amount', 'ip_address', 'user_agent', 'device', 'browser',
        'platform', 'occurred_at', 'data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}