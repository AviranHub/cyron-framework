<?php

use Cyron\Routing\Route;
use App\Http\Middlewares\AuthMiddleware;
use App\Http\Middlewares\AuthorMiddleware;
use App\Http\Controllers\Author\DashboardController;
use App\Http\Controllers\Author\ContentController;

Route::middleware(AuthMiddleware::class)->middleware(AuthorMiddleware::class)->prefix('author')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('author.dashboard');
    Route::get('/books', [DashboardController::class, 'books'])->name('author.books');
    Route::get('/books/create', [ContentController::class, 'createBook'])->name('author.books.create');
    Route::post('/books', [ContentController::class, 'storeBook'])->name('author.books.store');
    Route::get('/books/{id}/edit', [ContentController::class, 'editBook'])->name('author.books.edit');
    Route::put('/books/{id}', [ContentController::class, 'updateBook'])->name('author.books.update');
    Route::post('/books/{id}/publish', [ContentController::class, 'publishBook'])->name('author.books.publish');
    Route::post('/books/{id}/unpublish', [ContentController::class, 'unpublishBook'])->name('author.books.unpublish');
    Route::get('/books/{id}/parts/create', [ContentController::class, 'createPart'])->name('author.books.parts.create');
    Route::post('/books/{id}/parts', [ContentController::class, 'storePart'])->name('author.books.parts.store');
});
