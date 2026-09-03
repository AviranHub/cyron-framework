<?php
namespace App\Models;
use App\Database\Model;
class UserTotp extends Model { protected static $table = 'user_totps'; protected static array $fillable =['user_id','secret','enabled_at','disabled_at','created_at']; }