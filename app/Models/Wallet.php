<?php

namespace App\Models;

use Cyron\Database\Model;

class Wallet extends Model
{
    protected static $table = 'wallets';
    protected static array $fillable = ['user_id', 'balance', 'others'];
}