<?php

namespace App\Models;

use Cyron\Database\Model;

class WalletTransaction extends Model
{
    protected static $table = 'wallet_transactions';
    protected static array $fillable = [
        'wallet_id', 'user_id', 'amount', 'type', 'description',
        'transaction_id', 'others',
    ];
}