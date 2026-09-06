<?php

namespace App\Models;

use Cyron\Database\Model;

class BookPart extends Model
{
    protected static $table = 'book_parts';
    protected static array $fillable = ['text', 'page_id', 'page_name', 'book_id', 'publisher_id'];
}