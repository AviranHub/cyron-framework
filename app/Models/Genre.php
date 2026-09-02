<?php

namespace App\Models;

use App\Database\Model;

class Genre extends Model
{
    protected static $table = 'genres';
    protected static array $fillable = ['name'];
}