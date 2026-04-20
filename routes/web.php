<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});
Route::get('/info', function () {
    return Inertia::render('Info');
});
require __DIR__.'/auth.php';
