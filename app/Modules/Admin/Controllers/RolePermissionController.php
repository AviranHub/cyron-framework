<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionController extends Controller
{
    public function edit(int $id)
    {
        $role = Role::find($id);
        if (!$role) abort(404);
        $permissions = Permission::query()->get();
        $assigned = $role->permissions()->pluck('id')->all();
        $groups = [];
        foreach ($permissions as $permission) {
            $groups[$permission->group ?: 'general'][] = $permission;
        }
        return view('admin.roles.permissions', compact('role','groups','assigned'));
    }

    public function update(int $id)
    {
        $role = Role::find($id);
        if (!$role) abort(404);
        if ((bool)($role->is_system ?? false)) abort(403, 'System roles cannot be modified here.');

        $before = $role->permissions()->pluck('id')->all();
        $ids = array_values(array_unique(array_map('intval', (array) request()->input('permissions', []))));
        $valid = Permission::query()->whereIn('id', $ids)->pluck('id')->all();
        $role->permissions()->sync($valid);
        Audit::record('role.permissions_updated', ['role_id'=>$role->id,'old_permissions'=>$before,'new_permissions'=>$valid]);

        return redirect()->back()->with('success', 'دسترسی‌های نقش با موفقیت ذخیره شد');
    }
}