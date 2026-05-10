<?php

use App\Modules\CMS\Controllers\CmsPageController;
use App\Modules\CMS\Controllers\CmsSectionController;
use App\Modules\CMS\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS Module Routes  —  /api/v1/cms/*
|--------------------------------------------------------------------------
*/

// Public routes — no auth needed (landing website SSR fetches)
Route::prefix('v1/cms')->group(function () {
    Route::get('/pages/{slug}', [CmsPageController::class, 'show']);
    Route::get('/navigation', [CmsPageController::class, 'navigation']);
    Route::get('/section-types', [CmsSectionController::class, 'types']);
    Route::get('/menus/{slug}', [MenuController::class, 'show']);
});

// Admin CMS routes — require auth + admin role
Route::prefix('v1/cms/admin')->middleware(['auth:sanctum', 'role:super_admin|admin'])->group(function () {

    // Pages
    Route::get('/pages', [CmsPageController::class, 'index']);
    Route::post('/pages', [CmsPageController::class, 'store']);
    Route::get('/pages/{id}', [CmsPageController::class, 'edit']);
    Route::put('/pages/{id}', [CmsPageController::class, 'update']);
    Route::delete('/pages/{id}', [CmsPageController::class, 'destroy']);
    Route::post('/pages/{id}/reorder-sections', [CmsPageController::class, 'reorderSections']);

    // Sections
    Route::post('/pages/{pageId}/sections', [CmsSectionController::class, 'store']);
    Route::put('/sections/{sectionId}', [CmsSectionController::class, 'update']);
    Route::delete('/sections/{sectionId}', [CmsSectionController::class, 'destroy']);

    // Blocks
    Route::post('/sections/{sectionId}/blocks', [CmsSectionController::class, 'storeBlock']);
    Route::put('/blocks/{blockId}', [CmsSectionController::class, 'updateBlock']);
    Route::delete('/blocks/{blockId}', [CmsSectionController::class, 'destroyBlock']);

    // Menus
    Route::get('/menus', [MenuController::class, 'index']);
    Route::post('/menus', [MenuController::class, 'store']);
    Route::put('/menus/{id}', [MenuController::class, 'update']);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy']);

    // Menu Items
    Route::get('/menus/{menuId}/items', [MenuController::class, 'items']);
    Route::post('/menus/{menuId}/items', [MenuController::class, 'storeItem']);
    Route::put('/items/{itemId}', [MenuController::class, 'updateItem']);
    Route::delete('/items/{itemId}', [MenuController::class, 'destroyItem']);
    Route::post('/menus/{menuId}/reorder', [MenuController::class, 'reorder']);
});
