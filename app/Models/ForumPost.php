<?php

namespace App\Models;

use App\Database\Model;

class ForumPost extends Model
{
    protected static $table = 'forum_posts';
    protected static array $fillable = ['topic_id', 'user_id', 'parent_post_id', 'content', 'likes', 'dislikes'];
}