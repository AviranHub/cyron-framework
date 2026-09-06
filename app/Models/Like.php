<?php

namespace App\Models;

use Cyron\Database\Model;

class Like extends Model
{
    protected static $table = 'likes';
    protected static array $fillable = ['user_id', 'is_like', 'likeable_type', 'likeable_id'];
}