<?php

use App\Route;
use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\AuthorMiddleware;
use App\Http\Controllers\Author\DashboardController;

Route::middleware(AuthMiddleware::class)->middleware(AuthorMiddleware::class)->prefix('author')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('author.dashboard');
    Route::get('/books', [DashboardController::class, 'books'])->name('author.books');
});
