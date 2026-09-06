<?php

namespace App\Models;

use Cyron\Database\Model;

class Transaction extends Model
{
    protected static $table = 'transactions';
    protected static array $fillable = [
        'user_id', 'amount', 'type', 'status', 'description', 'reference_id',
        'tracking_code', 'payment_method', 'gateway_name', 'authority',
        'wallet_amount', 'gateway_response', 'others', 'payable_type',
        'payable_id', 'subscription_id', 'paid_at', 'expires_at',
    ];
}