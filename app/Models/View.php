<?php

namespace App\Models;

use Cyron\Database\Model;

class View extends Model
{
    protected static $table = 'views';
    protected static array $fillable = ['user_id', 'session_id', 'subject_type', 'subject_id', 'ip_address', 'user_agent', 'viewed_at'];
}