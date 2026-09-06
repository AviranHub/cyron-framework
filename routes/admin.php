<?php
use App\Http\Controllers\Admin\AdminController;
use Cyron\Routing\Route;
use App\Http\Middlewares\AdminMiddleware;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ForumController;

$models = require APP_PATH . '/Config/admin.php';
// error_log('Admin models: ' . print_r($models, true));
foreach ($models as $key => $config) {
    Route::middleware(AdminMiddleware::class)->prefix("admin/{$key}")->group(function () use ($key) {
        Route::get('/', [AdminController::class, 'index', [$key]])->name("admin.{$key}.index");
        Route::get('/create', [AdminController::class, 'create', [$key]])->name("admin.{$key}.create");
        Route::post('/store', [AdminController::class, 'store', [$key]])->name("admin.{$key}.store");
        Route::get('/{id}/edit', [AdminController::class, 'edit', [$key]])->name("admin.{$key}.edit");
        Route::put('/{id}', [AdminController::class, 'update', [$key]])->name("admin.{$key}.update");
        Route::delete('/{id}', [AdminController::class, 'destroy', [$key]])->name("admin.{$key}.destroy");
    });
}

// روت اصلی داشبورد ادمین
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware(AdminMiddleware::class);
Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
    Route::get('/finance', [FinanceController::class, 'overview'])->name('admin.finance.overview');
    Route::get('/transactions', [FinanceController::class, 'transactions'])->name('admin.finance.transactions');
    Route::get('/wallet-transactions', [FinanceController::class, 'walletTransactions'])->name('admin.finance.wallet-transactions');
    Route::get('/user-subscriptions', [FinanceController::class, 'subscriptions'])->name('admin.finance.user-subscriptions');
    Route::get('/activities', [ActivityController::class, 'index'])->name('admin.activities.index');
    Route::get('/activities/export', [ActivityController::class, 'export'])->name('admin.activities.export');
    Route::get('/activities/user/{id}', [ActivityController::class, 'user'])->name('admin.activities.user');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('admin.analytics.export');
    Route::get('/forum', [ForumController::class, 'index'])->name('admin.forum.index');
    Route::get('/forum/topics', [ForumController::class, 'topics'])->name('admin.forum.topics');
    Route::get('/forum/posts', [ForumController::class, 'posts'])->name('admin.forum.posts');
    Route::post('/forum/topics/{id}/toggle-lock', [ForumController::class, 'toggleLock'])->name('admin.forum.topics.toggle-lock');
    Route::post('/forum/topics/{id}/toggle-pin', [ForumController::class, 'togglePin'])->name('admin.forum.topics.toggle-pin');
    Route::delete('/forum/topics/{id}', [ForumController::class, 'destroyTopic'])->name('admin.forum.topics.destroy');
    Route::delete('/forum/posts/{id}', [ForumController::class, 'destroyPost'])->name('admin.forum.posts.destroy');
    Route::get('/support', [SupportController::class, 'inbox'])->name('admin.support.index');
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('admin.support.show');
    Route::post('/support/{id}/reply', [SupportController::class, 'reply'])->name('admin.support.reply');
    Route::post('/support/{id}/close', [SupportController::class, 'close'])->name('admin.support.close');
});
