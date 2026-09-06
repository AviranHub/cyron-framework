<?php

namespace App\Models;

use Cyron\Database\Model;

class ForumTopic extends Model
{
    protected static $table = 'forum_topics';
    protected static array $fillable = ['category_id', 'user_id', 'title', 'slug', 'content', 'views', 'is_pinned', 'is_locked'];
}