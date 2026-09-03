<?php

use App\Route;

/**
 * Cyron Framework documentation.
 *
 * Keep the documentation route separate from application routes so projects
 * can remove or replace it without touching their normal web routes.
 */
Route::get('/docs', function () {
    return view('documentation');
})->name('docs');
