<?php

namespace App\Models;

use Cyron\Database\Model;

class Shelf extends Model
{
    protected static $table = 'shelves';
    protected static array $fillable = ['user_id', 'name'];
}