<?php

namespace App\Models;

use Cyron\Database\Model;

class ChatConversation extends Model
{
    protected static $table = 'chat_conversations';
    protected static array $fillable = ['user_id', 'status', 'subject', 'last_message_at'];
}