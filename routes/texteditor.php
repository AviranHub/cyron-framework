<?php
use App\Route;
Route::prefix('texteditor')->group(function () {
    Route::get('/', [\Plugins\TextEditor\Controllers\TextEditorController::class, 'index'])->name('texteditor.index');
    Route::get('/{id}', [\Plugins\TextEditor\Controllers\TextEditorController::class, 'show'])->name('texteditor.show');
});