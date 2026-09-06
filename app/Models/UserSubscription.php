<?php

namespace App\Models;

use Cyron\Database\Model;

class UserSubscription extends Model
{
    protected static $table = 'user_subscriptions';
    protected static array $fillable = [
        'user_id', 'subscription_id', 'start_date', 'end_date', 'status',
        'trial_used', 'payment_method', 'transaction_id',
        'books_downloaded_this_month', 'last_download_reset', 'others',
    ];
}