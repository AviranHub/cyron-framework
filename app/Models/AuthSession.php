<?php
namespace App\Models;
use Cyron\Database\Model;
class AuthSession extends Model { protected static $table = 'auth_sessions'; protected static array $fillable =['user_id','token_hash','ip_address','user_agent','last_seen_at','revoked_at','created_at']; }