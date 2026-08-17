<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
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
