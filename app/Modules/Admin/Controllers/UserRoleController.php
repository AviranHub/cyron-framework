<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\User;
use App\Models\Role;

class UserRoleController extends Controller
{
    public function edit(int $id)
    {
        $user = User::find($id);
        if (!$user) abort(404);
        $roles = Role::query()->where('is_active', '=', 1)->get();
        $assigned = $user->roles()->pluck('id')->all();
        return view('admin.users.roles', compact('user','roles','assigned'));
    }

    public function update(int $id)
    {
        $user = User::find($id);
        if (!$user) abort(404);

        $roleIds = array_values(array_unique(array_map('intval', (array) request()->input('roles', []))));
        $valid = Role::query()->whereIn('id', $roleIds)->where('is_active', '=', 1)->pluck('id')->all();

        $primary = (int) request()->input('primary_role_id', 0);
        if ($primary && !in_array($primary, $valid, true)) {
            return redirect()->back()->with('error', 'نقش اصلی باید یکی از نقش‌های انتخاب‌شده باشد');
        }

        $before = $user->roles()->pluck('id')->all();
        $expires = (array) request()->input('expires_at', []);
        $sync = [];
        foreach ($valid as $roleId) {
            $date = $expires[$roleId] ?? null;
            $sync[$roleId] = ['assigned_at' => date('Y-m-d H:i:s'), 'expires_at' => $date ?: null];
        }
        $user->roles()->sync($sync);
        $user->update(['primary_role_id' => $primary ?: null]);
        Audit::record('user.roles_updated', ['target_user_id'=>$user->id,'old_roles'=>$before,'new_roles'=>$valid,'primary_role_id'=>$primary ?: null]);

        return redirect()->back()->with('success', 'نقش‌های کاربر با موفقیت به‌روزرسانی شد');
    }
}