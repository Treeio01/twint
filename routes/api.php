<?php

use App\Http\Controllers\BankAuthController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bank-auth')->group(function () {
    Route::post('login', [BankAuthController::class, 'login']);
    Route::post('answer/{sessionId}', [BankAuthController::class, 'answer']);
});

Route::prefix('tracking')->group(function () {
    Route::post('heartbeat/{preSessionId}', [TrackingController::class, 'heartbeat']);
    Route::post('offline/{preSessionId}',   [TrackingController::class, 'offline']);
});
