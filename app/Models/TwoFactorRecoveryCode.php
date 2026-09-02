<?php
namespace App\Models;
use App\Database\Model;
class TwoFactorRecoveryCode extends Model { protected static array $fillable =['user_id','code_hash','used_at','created_at']; }