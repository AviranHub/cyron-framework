<?php
namespace App\Models;
use App\Database\Model;
class UserTwoFactor extends Model { protected static array $fillable =['user_id','channel','target','enabled_at','disabled_at','created_at']; }