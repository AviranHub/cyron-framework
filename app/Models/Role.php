<?php
namespace App\Models;

use App\Database\Model;
use App\Models\Permission;

class Role extends Model
{
    protected static $table = 'roles';
    protected static array $fillable = ['name', 'slug', 'description', 'is_system', 'is_active', 'priority'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}