<?php

use App\Modules\Admin\Controllers\OrchestratorLoginController;
use App\Modules\Admin\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Module Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix(env('ORCHESTRATOR_PATH', 'orchestrator'))->group(function () {
    Route::get('/login', [OrchestratorLoginController::class, 'show'])->name('orchestrator.login');
    Route::post('/login', [OrchestratorLoginController::class, 'authenticate'])->name('orchestrator.login.submit');
    
    Route::post('/logout', [OrchestratorLoginController::class, 'logout'])->name('orchestrator.logout');
    
    // Protected Dashboard Route
    Route::middleware(['web', 'auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('orchestrator.dashboard');
        
        // Platform Core
        Route::get('/users', fn() => view('admin.users'))->name('orchestrator.users');
        Route::get('/security', fn() => view('admin.security'))->name('orchestrator.security');
        Route::get('/operations-map', fn() => view('admin.operations_map'))->name('orchestrator.operations_map');
        Route::get('/dispatcher', fn() => view('admin.dispatcher'))->name('orchestrator.dispatcher');
        
        // Logistics Engine
        Route::get('/drivers', fn() => view('admin.asset_registry'))->name('orchestrator.drivers');
        Route::get('/orders', fn() => view('admin.global_queue'))->name('orchestrator.orders');
        Route::get('/financials', fn() => view('admin.financials'))->name('orchestrator.financials');
        
        // CMS & Platform
        Route::view('/menus', 'admin.menus')->name('orchestrator.menus');
        Route::get('/settings', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'index'])->name('orchestrator.settings');
        Route::get('/settings/branding', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'branding'])->name('orchestrator.settings.branding');
        Route::get('/settings/authentication', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'auth'])->name('orchestrator.settings.auth');
        Route::get('/settings/mobile-manifest', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'manifest'])->name('orchestrator.settings.manifest');
        Route::get('/settings/localization', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'localization'])->name('orchestrator.settings.localization');
        Route::post('/settings', [\App\Modules\Admin\Controllers\SystemSettingController::class, 'update'])->name('orchestrator.settings.update');

        // Onboarding Slide Manager
        Route::get('/settings/onboarding/{appType}', [\App\Modules\Admin\Controllers\OnboardingController::class, 'index'])->name('orchestrator.settings.onboarding');
        Route::post('/settings/onboarding', [\App\Modules\Admin\Controllers\OnboardingController::class, 'store'])->name('orchestrator.onboarding.store');
        Route::put('/settings/onboarding/{id}', [\App\Modules\Admin\Controllers\OnboardingController::class, 'update'])->name('orchestrator.onboarding.update');
        Route::delete('/settings/onboarding/{id}', [\App\Modules\Admin\Controllers\OnboardingController::class, 'destroy'])->name('orchestrator.onboarding.destroy');
        Route::patch('/settings/onboarding/{id}/toggle', [\App\Modules\Admin\Controllers\OnboardingController::class, 'toggle'])->name('orchestrator.onboarding.toggle');
        Route::post('/settings/onboarding/reorder', [\App\Modules\Admin\Controllers\OnboardingController::class, 'reorder'])->name('orchestrator.onboarding.reorder');
        Route::post('/settings/onboarding/splash', [\App\Modules\Admin\Controllers\OnboardingController::class, 'updateSplash'])->name('orchestrator.onboarding.splash.update');

        Route::get('/infrastructure', fn() => view('admin.infrastructure'))->name('orchestrator.infrastructure');
        Route::get('/modules', fn() => view('admin.module_hardening'))->name('orchestrator.modules');

        // ── Driver Management Department ──────────────────────────────────────
        Route::get('/driver-management', fn() => view('admin.driver_management'))->name('orchestrator.driver.management');
        Route::get('/driver-management/documents', fn() => view('admin.driver_documents'))->name('orchestrator.driver.documents');
        Route::get('/driver-management/vehicle-types', fn() => view('admin.vehicle_types'))->name('orchestrator.vehicle.types');

        // ── Customer Support Department ───────────────────────────────────────
        Route::get('/support/tickets', fn() => view('admin.support_tickets'))->name('orchestrator.support.tickets');
        Route::get('/support/tickets/{id}', fn($id) => view('admin.support_ticket_detail', ['ticketId' => $id]))->name('orchestrator.support.ticket.show');

        // ── Analytics & Reporting Department ─────────────────────────────────
        Route::get('/analytics', fn() => view('admin.analytics'))->name('orchestrator.analytics');

        // ── Marketing Department ──────────────────────────────────────────────
        Route::get('/marketing/promotions', fn() => view('admin.marketing_promos'))->name('orchestrator.marketing.promos');
        Route::get('/marketing/banners', fn() => view('admin.marketing_banners'))->name('orchestrator.marketing.banners');

        // ── CMS Department ────────────────────────────────────────────────────
        Route::get('/cms/blog', fn() => view('admin.cms_blog'))->name('orchestrator.cms.blog');
        Route::get('/cms/faq', fn() => view('admin.cms_faq'))->name('orchestrator.cms.faq');

        // ── HR Department ─────────────────────────────────────────────────────
        Route::get('/hr', fn() => view('admin.hr_management'))->name('orchestrator.hr');
    });
});
