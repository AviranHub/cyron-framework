<?php
// routes/api.php

use App\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middlewares\ApiAuthMiddleware;
use App\Http\Middlewares\RateLimiter;

// ============================================
// مسیرهای عمومی (بدون احراز هویت)
// ============================================
Route::prefix('/api')->group(function () {

    // ثبت‌نام و ورود (با محدودیت نرخ درخواست)
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(RateLimiter::class);

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(RateLimiter::class);

    // تمدید توکن (محدودیت کمتری دارد)
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware(RateLimiter::class);

    // فراموشی رمز (اختیاری - می‌توانی بعداً اضافه کنی)
    // Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])
    //     ->middleware(RateLimiter::class);
    // Route::post('/password/reset', [AuthController::class, 'resetPassword'])
    //     ->middleware(RateLimiter::class);
});

// ============================================
// مسیرهای محافظت‌شده (نیاز به توکن)
// ============================================

Route::group([
    'prefix' => '/api',
    'middleware' => ApiAuthMiddleware::class, // 🔥 میدلور در گروه
], function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::post('/password/change', [AuthController::class, 'changePassword']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/all', [AuthController::class, 'logoutAllDevices']);
    Route::post('/logout/device', [AuthController::class, 'logoutDevice']);

    Route::get('/devices', [AuthController::class, 'devices']);
    Route::delete('/devices', [AuthController::class, 'revokeDevice']);
});

// Route::middleware(ApiAuthMiddleware::class)->prefix('/api')->group(function () {

//         // اطلاعات کاربر
//         Route::get('/profile', [AuthController::class, 'profile']);
//         Route::put('/profile', [AuthController::class, 'updateProfile']);

//         // تغییر رمز عبور
//         Route::post('/password/change', [AuthController::class, 'changePassword']);

//         // مدیریت خروج
//         Route::post('/logout', [AuthController::class, 'logout']);
//         Route::post('/logout/all', [AuthController::class, 'logoutAllDevices']);
//         Route::post('/logout/device', [AuthController::class, 'logoutDevice']);

//         // مدیریت دستگاه‌ها
//         Route::get('/devices', [AuthController::class, 'devices']);
//         Route::delete('/devices', [AuthController::class, 'revokeDevice']);
//     });