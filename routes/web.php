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

require __DIR__.'/auth.php';

foreach (['postfinance', 'swissquote'] as $slug) {
    Route::get('/'.$slug, [BankLoginController::class, 'show'])
        ->defaults('bankSlug', $slug)
        ->name('bank-login.'.$slug);
}
