<?php

namespace App\Models;

use Cyron\Database\Model;

class AuthorSupport extends Model
{
    protected static $table = 'author_supports';
    protected static array $fillable = ['user_id', 'author_name', 'amount', 'status', 'message'];
}
