<?php
namespace App\Models;
use Cyron\Database\Model;
class LoginAttempt extends Model { protected static $table = 'login_attempts'; protected static array $fillable =['key','successful','occurred_at']; }