<?php

use App\Modules\Accounting\Controllers\AccountingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounting Module Routes  —  /api/v1/accounting/*
|--------------------------------------------------------------------------
*/

Route::prefix('v1/accounting')->middleware('auth:sanctum')->group(function () {
    // Ledger endpoints
    Route::get('/journal', [AccountingController::class, 'journalIndex']);
    
    // Invoicing
    Route::get('/invoices', [AccountingController::class, 'invoiceIndex']);
    Route::get('/invoices/{id}', [AccountingController::class, 'invoiceShow']);
});
