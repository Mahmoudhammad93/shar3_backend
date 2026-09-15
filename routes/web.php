<?php

use App\Http\Controllers\Admin\LessonBunnyMediaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth', 'staff'])->group(function () {
    Route::post('admin/bunny/uploads', [LessonBunnyMediaController::class, 'prepare'])->name('admin.bunny.uploads.prepare');
    Route::put('admin/bunny/uploads/{videoId}/content', [LessonBunnyMediaController::class, 'upload'])->name('admin.bunny.uploads.content');
    Route::post('admin/bunny/uploads/cancel', [LessonBunnyMediaController::class, 'cancel'])->name('admin.bunny.uploads.cancel');

    Route::prefix('admin/lessons/{lesson}/bunny')->group(function () {
        Route::post('create', [LessonBunnyMediaController::class, 'create'])->name('admin.lessons.bunny.create');
        Route::post('replace', [LessonBunnyMediaController::class, 'replace'])->name('admin.lessons.bunny.replace');
        Route::post('confirm', [LessonBunnyMediaController::class, 'confirm'])->name('admin.lessons.bunny.confirm');
        Route::get('status', [LessonBunnyMediaController::class, 'status'])->name('admin.lessons.bunny.status');
        Route::delete('/', [LessonBunnyMediaController::class, 'destroy'])->name('admin.lessons.bunny.destroy');
    });
});

Route::get('/media/{path}', function (string $path) {
    $path = ltrim(str_replace(['..', '\\'], '', $path), '/');

    foreach (['public', 'local'] as $disk) {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->response($path);
        }
    }

    abort(404);
})->where('path', '.*');
