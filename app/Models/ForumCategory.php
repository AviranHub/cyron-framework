<?php

namespace App\Models;

use App\Database\Model;

class ForumCategory extends Model
{
    protected static $table = 'forum_categories';
    protected static array $fillable = ['name', 'slug', 'description', 'sort_order'];
}