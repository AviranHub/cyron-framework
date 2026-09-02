<?php
namespace App\Models;
use App\Database\Model;
class PasswordResetToken extends Model { protected static array $fillable =['user_id','token_hash','expires_at','used_at','created_at']; }