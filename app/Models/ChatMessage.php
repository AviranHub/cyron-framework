<?php

namespace App\Models;

use Cyron\Database\Model;

class ChatMessage extends Model
{
    protected static $table = 'chat_messages';
    protected static array $fillable = ['conversation_id', 'sender_id', 'sender_type', 'body', 'read_at', 'attachment_path', 'attachment_name', 'attachment_mime'];
}