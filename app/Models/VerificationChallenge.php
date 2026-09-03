<?php
namespace App\Models;
use App\Database\Model;
class VerificationChallenge extends Model { protected static $table = 'verification_challenges'; protected static array $fillable =['user_id','channel','target','code_hash','purpose','expires_at','verified_at','created_at']; }