<?php

namespace App\Models;

use Cyron\Database\Model;

class Discount extends Model
{
    protected static $table = 'discounts';
    protected static array $fillable = [
        'name', 'code', 'type', 'value', 'applies_to', 'target_type', 'target_id',
        'min_amount', 'max_discount', 'starts_at', 'ends_at', 'usage_limit',
        'usage_count', 'is_active',
    ];
}
