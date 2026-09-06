<?php

namespace App\Models;

use Cyron\Database\Model;

class Library extends Model
{
    protected static $table = 'libraries';
    protected static array $fillable = ['user_id', 'book_id', 'shelf_id', 'purchased_at'];
}