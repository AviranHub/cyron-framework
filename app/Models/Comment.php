<?php

namespace App\Models;

use Cyron\Database\Model;

class Comment extends Model
{
    protected static $table = 'comments';
    protected static array $fillable = [
        'author_id', 'author_name', 'text', 'reply_id', 'depth',
        'is_public', 'is_admin_view', 'is_publisher_view', 'is_approved',
        'approved_at', 'replies_count', 'report_count', 'is_edited',
        'edited_at', 'commentable_type', 'commentable_id',
    ];
}