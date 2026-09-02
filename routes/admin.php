<?php
use App\Http\Controllers\Admin\AdminController;
use App\Route;
use App\Http\Middlewares\AuthMiddleware;

$models = require APP_PATH . '/Config/admin.php';
// error_log('Admin models: ' . print_r($models, true));
foreach ($models as $key => $config) {
    Route::middleware(AuthMiddleware::class)->prefix("admin/{$key}")->group(function () use ($key) {
        Route::get('/', [AdminController::class, 'index', [$key]])->name("admin.{$key}.index");
        Route::get('/create', [AdminController::class, 'create', [$key]])->name("admin.{$key}.create");
        Route::post('/store', [AdminController::class, 'store', [$key]])->name("admin.{$key}.store");
        Route::get('/{id}/edit', [AdminController::class, 'edit', [$key]])->name("admin.{$key}.edit");
        Route::put('/{id}', [AdminController::class, 'update', [$key]])->name("admin.{$key}.update");
        Route::delete('/{id}', [AdminController::class, 'destroy', [$key]])->name("admin.{$key}.destroy");
    });
}

// روت اصلی داشبورد ادمین
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware(AuthMiddleware::class);
