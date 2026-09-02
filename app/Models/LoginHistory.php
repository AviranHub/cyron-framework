<?php
namespace App\Models;
use App\Database\Model;
class LoginHistory extends Model {
 protected static array $fillable =['user_id','successful','ip_address','user_agent','context','occurred_at'];
 public function contextData(): array { return json_decode($this->context ?? '{}',true) ?: []; }
}