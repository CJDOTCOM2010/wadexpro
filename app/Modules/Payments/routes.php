<?php

use App\Modules\Payments\Controllers\CheckoutController;
use App\Modules\Payments\Controllers\WalletController;
use App\Modules\Payments\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payments Module Routes  —  /api/v1/payments/*
|--------------------------------------------------------------------------
*/

// Webhooks must bypass auth
Route::post('v1/payments/webhook/{provider}', [WebhookController::class, 'handle']);

// Mobile / Frontend checkout endpoints
Route::prefix('v1/payments')->middleware('auth:sanctum')->group(function () {
    Route::post('/initialize', [CheckoutController::class, 'initialize']);
    Route::post('/verify', [CheckoutController::class, 'verify']);
});

// Wallet endpoints
Route::prefix('v1/payments/wallet')->middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [WalletController::class, 'balance']);
    Route::get('/history', [WalletController::class, 'history']);
    Route::get('/weekly-summary', [WalletController::class, 'weeklySummary']);
    Route::post('/topup', [WalletController::class, 'topup']);
    Route::post('/payout', [WalletController::class, 'payout']);

    // Unified Wallet Hub (Profile > Wallet)
    Route::get('/hub', [\App\Modules\Payments\Controllers\WalletManagerController::class, 'index']);
    Route::post('/hub/payment-methods', [\App\Modules\Payments\Controllers\WalletManagerController::class, 'addPaymentMethod']);
    Route::post('/hub/promos/check', [\App\Modules\Payments\Controllers\WalletManagerController::class, 'checkPromo']);
    Route::get('/hub/referrals', [\App\Modules\Payments\Controllers\WalletManagerController::class, 'referrals']);
});
