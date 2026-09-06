<?php

namespace App\Models;

use Cyron\Database\Model;

class Article extends Model
{
    protected static $table = 'articles';
    protected static array $fillable = [
        'title', 'slug', 'excerpt', 'content', 'cover', 'author_id',
        'category_id', 'status', 'published_at',
    ];
}