<?php

use App\Modules\Monitoring\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Monitoring Module Routes  —  /api/v1/monitoring/*
|--------------------------------------------------------------------------
*/

Route::prefix('v1/monitoring')->middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/logs', [MonitoringController::class, 'auditLogs']);
    Route::get('/alerts', [MonitoringController::class, 'alerts']);
    Route::patch('/alerts/{id}/resolve', [MonitoringController::class, 'resolveAlert']);
});
