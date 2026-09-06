<?php

namespace App\Models;

use Cyron\Database\Model;

class Subscription extends Model
{
    protected static $table = 'subscriptions';
    protected static array $fillable = [
        'name', 'description', 'price', 'duration', 'allows_download',
        'counts_download', 'offer', 'others',
    ];
}