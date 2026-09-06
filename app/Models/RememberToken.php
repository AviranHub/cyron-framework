<?php

namespace App\Models;

use Cyron\Database\Model;

class RememberToken extends Model
{
    protected static $table = 'remember_tokens';
    protected static array $fillable = [
        'user_id', 'selector', 'token_hash', 'expires_at', 'last_used_at',
    ];
}
