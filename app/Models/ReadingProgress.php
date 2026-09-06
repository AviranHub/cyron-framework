<?php

namespace App\Models;

use Cyron\Database\Model;

class ReadingProgress extends Model
{
    protected static $table = 'reading_progresses';
    protected static array $fillable = ['user_id', 'book_id', 'last_page'];
}