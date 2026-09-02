<?php
namespace App\Models;

use App\Database\Model;

class Permission extends Model
{
    protected static $table = 'permissions';
    protected static array $fillable = ['name', 'slug', 'group', 'module', 'description', 'is_critical'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}