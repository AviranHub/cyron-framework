<?php

namespace App\Models;

use Cyron\Database\Model;

class Book extends Model
{
    protected static $table = 'books';
    protected static array $fillable = ['id', 'title', 'subject', 'author', 'author_id', 'author_name', 'publisher_id', 'category_id', 'cover', 'pdf', 'introduction', 'description', 'likes', 'views', 'pages', 'total_pages', 'slug', 'status', 'price', 'copen', 'is_audio', 'is_buy', 'is_show', 'is_download', 'is_subscribe', 'is_read', 'is_public', 'is_bestseller', 'access_type'];
}