<?php
// app/Modules/Auth/routes.php
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Middlewares\AuthMiddleware;
use App\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/login/two-factor', [LoginController::class, 'showTwoFactorForm'])->name('login.two-factor');
Route::post('/login/two-factor', [LoginController::class, 'verifyTwoFactor'])->name('login.two-factor.verify');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/phone/verify', [PhoneVerificationController::class, 'showVerifyForm'])->name('phone.verify');
Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verify.submit');
Route::post('/phone/send-code', [PhoneVerificationController::class, 'sendCode'])->name('phone.send');

Route::middleware(AuthMiddleware::class)->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profile/edit', [DashboardController::class, 'editProfile'])->name('user.profile.edit');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/change-password', [DashboardController::class, 'showChangePassword'])->name('user.change-password');
    Route::post('/change-password', [DashboardController::class, 'changePassword']);
});