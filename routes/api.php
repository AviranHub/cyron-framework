<?php
// routes/api.php

use Cyron\Routing\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middlewares\ApiAuthMiddleware;
use App\Http\Middlewares\RateLimiter;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\User\LibraryController;
use App\Http\Controllers\ReaderController;
use App\Http\Middlewares\AuthMiddleware;

// ============================================
// مسیرهای عمومی (بدون احراز هویت)
// ============================================
Route::prefix('/api')->group(function () {
    Route::get('/book/{bookId}/pages', [ReaderController::class, 'pages'])->middleware(AuthMiddleware::class);
    Route::post('/reading-progress', [LibraryController::class, 'saveProgress'])->middleware(AuthMiddleware::class);

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

// Versioned API for mobile and external clients.
Route::prefix('/api/v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware(RateLimiter::class);
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware(RateLimiter::class);
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])
        ->middleware(RateLimiter::class);

    Route::get('/books', [CatalogController::class, 'books']);
    Route::get('/books/{slug}', [CatalogController::class, 'book']);
    Route::get('/categories', [CatalogController::class, 'categories']);
});

Route::group([
    'prefix' => '/api/v1',
    'middleware' => ApiAuthMiddleware::class,
], function () {
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout/all', [AuthController::class, 'logoutAllDevices']);
    Route::get('/books/{bookId}/progress', [LibraryController::class, 'progress']);
    Route::post('/reading-progress', [LibraryController::class, 'saveProgress']);
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