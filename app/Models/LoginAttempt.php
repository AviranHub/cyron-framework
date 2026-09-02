<?php
namespace App\Models;
use App\Database\Model;
class LoginAttempt extends Model { protected static array $fillable =['key','successful','occurred_at']; }