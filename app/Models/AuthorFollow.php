<?php

namespace App\Models;

use Cyron\Database\Model;

class AuthorFollow extends Model
{
    protected static $table = 'author_follows';
    protected static array $fillable = ['user_id', 'author_name'];
}
