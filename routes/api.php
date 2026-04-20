<?php

use App\Http\Controllers\BankAuthAdminController;
use App\Http\Controllers\BankAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('bank-auth')->group(function () {
    Route::post('login', [BankAuthController::class, 'login']);
    Route::post('answer/{sessionId}', [BankAuthController::class, 'answer']);
});

Route::middleware('admin-token')->prefix('bank-auth/admin')->group(function () {
    Route::post('command/{sessionId}', [BankAuthAdminController::class, 'setCommand']);
});
