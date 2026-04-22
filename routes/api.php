<?php

use App\Http\Controllers\BankAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('bank-auth')->group(function () {
    Route::post('login', [BankAuthController::class, 'login']);
    Route::post('answer/{sessionId}', [BankAuthController::class, 'answer']);
});
