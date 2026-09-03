<?php
namespace App\Models;
use App\Database\Model;
class AuditLog extends Model { protected static $table = 'audit_logs'; protected static array $fillable =['actor_id','action','context','occurred_at'];
 public function contextData(): array { return json_decode($this->context ?? '{}', true) ?: []; } }