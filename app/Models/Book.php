<?php

namespace App\Models;

use App\Database\Model;

class Book extends Model
{
    protected static $table = 'books';
    protected static array $fillable = ['id', 'title', 'subject', 'author', 'likes', 'views', 'pages', 'total_pages', 'slug', 'status'];
}