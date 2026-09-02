<?php
use App\Http\Controllers\Admin\AdminController;
use App\Route;

$models = require APP_PATH . '/config/admin_models.php';

foreach ($models as $key => $config) {
    Route::prefix("admin/{$key}")->group(function () use ($key) {
        Route::get('/', [AdminController::class, 'index'])->name("admin.{$key}.index");
        Route::get('/create', [AdminController::class, 'create'])->name("admin.{$key}.create");
        Route::post('/store', [AdminController::class, 'store'])->name("admin.{$key}.store");
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name("admin.{$key}.edit");
        Route::put('/{id}', [AdminController::class, 'update'])->name("admin.{$key}.update");
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name("admin.{$key}.destroy");
    });
}

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');

Route::get('/admin/roles/{id}/permissions', [RolePermissionController::class, 'edit'])->name('admin.roles.permissions.edit');
Route::put('/admin/roles/{id}/permissions', [RolePermissionController::class, 'update'])->name('admin.roles.permissions.update');
Route::get('/admin/users/{id}/roles', [UserRoleController::class, 'edit'])->name('admin.users.roles.edit');
Route::put('/admin/users/{id}/roles', [UserRoleController::class, 'update'])->name('admin.users.roles.update');

Route::get('/admin/activities', [ActivityController::class, 'index'])->name('admin.activities.index');
Route::get('/admin/users/{id}/activities', [ActivityController::class, 'user'])->name('admin.activities.user');
Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');

Route::get('/admin/analytics/segments',[SegmentController::class,'index'])->name('admin.analytics.segments');

Route::get('/admin/audit-logs',[AuditLogController::class,'index'])->name('admin.audit.index');

Route::get('/admin/audit-logs/{id}',[AuditLogController::class,'show'])->name('admin.audit.show');

Route::get('/admin/security/logins',[LoginHistoryController::class,'index'])->name('admin.security.logins');

Route::get('/admin/security/sessions',[SessionController::class,'index'])->name('admin.security.sessions');
Route::post('/admin/security/sessions/{id}/revoke',[SessionController::class,'revoke'])->name('admin.security.sessions.revoke');
Route::post('/admin/security/users/{userId}/sessions/revoke',[SessionController::class,'revokeUser'])->name('admin.security.users.sessions.revoke');

Route::get('/admin/security/login-attempts',[LoginSecurityController::class,'attempts'])->name('admin.security.login-attempts');

Route::post('/admin/security/users/{userId}/2fa/enable',[TwoFactorController::class,'enable'])->name('admin.security.users.2fa.enable');
Route::post('/admin/security/users/{userId}/2fa/disable',[TwoFactorController::class,'disable'])->name('admin.security.users.2fa.disable');
