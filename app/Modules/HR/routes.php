<?php

use App\Modules\HR\Controllers\HRController;
use App\Modules\HR\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HR Module Routes  —  /api/v1/hr/*
|--------------------------------------------------------------------------
*/

Route::prefix('v1/hr')->middleware('auth:sanctum')->group(function () {
    // Admin routes
    Route::get('/employees', [HRController::class, 'index']);
    Route::post('/employees', [HRController::class, 'store']);
    Route::get('/attendance', [AttendanceController::class, 'index']);

    // Employee specific routes
    Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/leave-requests', [HRController::class, 'requestLeave']);
});
