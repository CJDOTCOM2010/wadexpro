<?php

use App\Modules\Admin\Controllers\ModuleManagerController;
use App\Modules\Admin\Controllers\RoleController;
use App\Modules\Admin\Controllers\SystemSettingsController;
use App\Modules\Admin\Controllers\UserManagementController;
use App\Modules\Admin\Controllers\DispatchController;
use App\Modules\Admin\Controllers\CmsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Orchestrator Routes — /api/v1/orchestrator/*
|--------------------------------------------------------------------------
| These routes represent the internal systems command and control.
| Access is STICKY to 'super_admin' users ONLY.
|--------------------------------------------------------------------------
*/

Route::prefix('v1/orchestrator')->middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

    // -----------------------------------------------------------------------
    // Module Orchestra
    // -----------------------------------------------------------------------
    Route::prefix('modules')->group(function () {
        Route::get('/',                       [ModuleManagerController::class, 'index']);
        Route::patch('/{slug}/toggle',        [ModuleManagerController::class, 'toggle']);
        Route::patch('/{slug}/config',        [ModuleManagerController::class, 'updateConfig']);
    });

    // -----------------------------------------------------------------------
    // Role & Permission Orchestra
    // -----------------------------------------------------------------------
    Route::get('/roles',                          [RoleController::class, 'indexRoles']);
    Route::post('/roles',                         [RoleController::class, 'storeRole']);
    Route::patch('/roles/{id}',                   [RoleController::class, 'updateRole']);
    Route::delete('/roles/{id}',                  [RoleController::class, 'destroyRole']);
    Route::get('/permissions',                    [RoleController::class, 'indexPermissions']);
    Route::patch('/roles/{id}/permissions',       [RoleController::class, 'syncPermissions']);
    Route::post('/users/{userId}/roles',          [RoleController::class, 'assignUserRoles']);

    // -----------------------------------------------------------------------
    // System Settings Orchestra
    // -----------------------------------------------------------------------
    Route::prefix('settings')->group(function () {
        Route::get('/',      [SystemSettingsController::class, 'index']);
        Route::get('/{key}', [SystemSettingsController::class, 'show']);
        Route::patch('/',    [SystemSettingsController::class, 'update']);
    });

    // -----------------------------------------------------------------------
    // User Identity Orchestra
    // -----------------------------------------------------------------------
    Route::prefix('users')->group(function () {
        Route::get('/',                   [UserManagementController::class, 'index']);
        Route::get('/{id}',               [UserManagementController::class, 'show']);
        Route::patch('/{id}/status',      [UserManagementController::class, 'updateStatus']);
        Route::delete('/{id}',            [UserManagementController::class, 'destroy']);
    });

    // -----------------------------------------------------------------------
    // Operations & Dispatch Orchestra
    // -----------------------------------------------------------------------
    Route::prefix('dispatch')->group(function () {
        Route::get('/active', [DispatchController::class, 'activeRides']);
    });

    // --- Dynamic CMS (Landing Page) ---
    Route::prefix('cms')->group(function () {
        Route::get('regions', [CmsController::class, 'getRegions']);
        Route::post('regions', [CmsController::class, 'upsertRegion']);
        Route::get('sections', [CmsController::class, 'getSections']);
        Route::post('sections', [CmsController::class, 'upsertSection']);
    });

    // Public Landing Page Content (No Auth Required)
    Route::get('orchestrator/public/content', [CmsController::class, 'getPublicContent']);
});


// -----------------------------------------------------------------------
// Public Settings — No auth required (mobile/web app bootstrap)
// -----------------------------------------------------------------------
Route::get('v1/settings/public', [SystemSettingsController::class, 'publicSettings']);
