<?php

use App\Http\Controllers\BankLoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});
Route::get('/info', function () {
    return Inertia::render('Info');
});

Route::get('/{bankSlug}', [BankLoginController::class, 'show'])
    ->where('bankSlug', '[a-z0-9-]+')
    ->name('bank-login.show');

require __DIR__.'/auth.php';
