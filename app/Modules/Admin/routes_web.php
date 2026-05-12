<?php

use App\Modules\Admin\Controllers\OrchestratorLoginController;
use App\Modules\Admin\Controllers\DashboardController;
use App\Modules\Admin\Controllers\DriverManagementController;
use App\Modules\Admin\Controllers\VehicleTypeController;
use App\Modules\Admin\Controllers\SupportTicketController;
use App\Modules\Admin\Controllers\OrchestratorAnalyticsController;
use App\Modules\Admin\Controllers\MarketingController;
use App\Modules\Admin\Controllers\ContentManagementController;
use App\Modules\Admin\Controllers\HRManagementController;
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
        Route::get('/users', [\App\Modules\Admin\Controllers\UserController::class, 'index'])->name('orchestrator.users');
        Route::get('/security', [\App\Modules\Admin\Controllers\SecurityController::class, 'index'])->name('orchestrator.security');
        Route::patch('/security/alerts/{id}/resolve', [\App\Modules\Admin\Controllers\SecurityController::class, 'resolveAlert'])->name('orchestrator.security.alerts.resolve');
        Route::post('/security/devices/block', [\App\Modules\Admin\Controllers\SecurityController::class, 'blockDevice'])->name('orchestrator.security.devices.block');
        Route::patch('/security/devices/{id}/unblock', [\App\Modules\Admin\Controllers\SecurityController::class, 'unblockDevice'])->name('orchestrator.security.devices.unblock');

        Route::get('/operations-map', [\App\Modules\Admin\Controllers\OperationsController::class, 'map'])->name('orchestrator.operations_map');
        Route::get('/dispatcher', [\App\Modules\Admin\Controllers\OperationsController::class, 'dispatcher'])->name('orchestrator.dispatcher');
        
        // Logistics Engine
        Route::get('/drivers', fn() => view('admin.asset_registry'))->name('orchestrator.drivers');
        Route::get('/orders', [\App\Modules\Admin\Controllers\OperationsController::class, 'globalQueue'])->name('orchestrator.orders');
        Route::get('/financials', [\App\Modules\Admin\Controllers\FinancialController::class, 'index'])->name('orchestrator.financials');
        Route::patch('/financials/payout/{id}/approve', [\App\Modules\Admin\Controllers\FinancialController::class, 'approvePayout'])->name('orchestrator.financials.payout.approve');

        
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
        Route::get('/driver-management',           [DriverManagementController::class, 'index'])->name('orchestrator.driver.management');
        Route::post('/driver-management/{id}/approve', [DriverManagementController::class, 'approve'])->name('orchestrator.driver.approve');
        Route::post('/driver-management/{id}/reject',  [DriverManagementController::class, 'reject'])->name('orchestrator.driver.reject');
        Route::post('/driver-management/{id}/suspend',  [DriverManagementController::class, 'suspend'])->name('orchestrator.driver.suspend');
        Route::post('/driver-management/{id}/activate', [DriverManagementController::class, 'activate'])->name('orchestrator.driver.activate');
        Route::get('/driver-management/documents', [DriverManagementController::class, 'documents'])->name('orchestrator.driver.documents');

        // ── Vehicle Types ─────────────────────────────────────────────────────
        Route::get('/driver-management/vehicle-types',         [VehicleTypeController::class, 'index'])->name('orchestrator.vehicle.types');
        Route::post('/driver-management/vehicle-types',        [VehicleTypeController::class, 'store'])->name('orchestrator.vehicle.types.store');
        Route::put('/driver-management/vehicle-types/{id}',    [VehicleTypeController::class, 'update'])->name('orchestrator.vehicle.types.update');
        Route::patch('/driver-management/vehicle-types/{id}/toggle', [VehicleTypeController::class, 'toggle'])->name('orchestrator.vehicle.types.toggle');
        Route::delete('/driver-management/vehicle-types/{id}', [VehicleTypeController::class, 'destroy'])->name('orchestrator.vehicle.types.destroy');

        // ── Customer Support Department ───────────────────────────────────────
        Route::get('/support/tickets',                        [SupportTicketController::class, 'index'])->name('orchestrator.support.tickets');
        Route::get('/support/tickets/{id}',                   [SupportTicketController::class, 'show'])->name('orchestrator.support.ticket.show');
        Route::post('/support/tickets/{id}/reply',            [SupportTicketController::class, 'reply'])->name('orchestrator.support.ticket.reply');
        Route::post('/support/tickets/{id}/assign',           [SupportTicketController::class, 'assign'])->name('orchestrator.support.ticket.assign');
        Route::patch('/support/tickets/{id}/resolve',         [SupportTicketController::class, 'resolve'])->name('orchestrator.support.ticket.resolve');
        Route::patch('/support/tickets/{id}/close',           [SupportTicketController::class, 'close'])->name('orchestrator.support.ticket.close');

        // ── Analytics & Reporting Department ─────────────────────────────────
        Route::get('/analytics', [OrchestratorAnalyticsController::class, 'index'])->name('orchestrator.analytics');

        // ── Marketing Department ──────────────────────────────────────────────
        Route::get('/marketing/promotions',               [MarketingController::class, 'promos'])->name('orchestrator.marketing.promos');
        Route::post('/marketing/promotions',              [MarketingController::class, 'storePromo'])->name('orchestrator.marketing.promos.store');
        Route::put('/marketing/promotions/{id}',          [MarketingController::class, 'updatePromo'])->name('orchestrator.marketing.promos.update');
        Route::patch('/marketing/promotions/{id}/toggle', [MarketingController::class, 'togglePromo'])->name('orchestrator.marketing.promos.toggle');
        Route::delete('/marketing/promotions/{id}',       [MarketingController::class, 'destroyPromo'])->name('orchestrator.marketing.promos.destroy');

        Route::get('/marketing/banners',               [MarketingController::class, 'banners'])->name('orchestrator.marketing.banners');
        Route::post('/marketing/banners',              [MarketingController::class, 'storeBanner'])->name('orchestrator.marketing.banners.store');
        Route::patch('/marketing/banners/{id}/toggle', [MarketingController::class, 'toggleBanner'])->name('orchestrator.marketing.banners.toggle');
        Route::delete('/marketing/banners/{id}',       [MarketingController::class, 'destroyBanner'])->name('orchestrator.marketing.banners.destroy');

        // ── CMS Department ────────────────────────────────────────────────────
        Route::get('/cms/blog',              [ContentManagementController::class, 'blog'])->name('orchestrator.cms.blog');
        Route::post('/cms/blog',             [ContentManagementController::class, 'storeBlog'])->name('orchestrator.cms.blog.store');
        Route::put('/cms/blog/{id}',         [ContentManagementController::class, 'updateBlog'])->name('orchestrator.cms.blog.update');
        Route::delete('/cms/blog/{id}',      [ContentManagementController::class, 'destroyBlog'])->name('orchestrator.cms.blog.destroy');

        Route::get('/cms/faq',              [ContentManagementController::class, 'faq'])->name('orchestrator.cms.faq');
        Route::post('/cms/faq',             [ContentManagementController::class, 'storeFaq'])->name('orchestrator.cms.faq.store');
        Route::put('/cms/faq/{id}',         [ContentManagementController::class, 'updateFaq'])->name('orchestrator.cms.faq.update');
        Route::delete('/cms/faq/{id}',      [ContentManagementController::class, 'destroyFaq'])->name('orchestrator.cms.faq.destroy');

        // ── HR Department ─────────────────────────────────────────────────────
        Route::get('/hr',                       [HRManagementController::class, 'index'])->name('orchestrator.hr');
        Route::post('/hr',                      [HRManagementController::class, 'store'])->name('orchestrator.hr.store');
        Route::patch('/hr/{id}/role',           [HRManagementController::class, 'updateRole'])->name('orchestrator.hr.role');
        Route::patch('/hr/{id}/deactivate',     [HRManagementController::class, 'deactivate'])->name('orchestrator.hr.deactivate');
        Route::patch('/hr/{id}/activate',       [HRManagementController::class, 'activate'])->name('orchestrator.hr.activate');
        Route::post('/hr/{id}/reset-password',  [HRManagementController::class, 'resetPassword'])->name('orchestrator.hr.reset-password');
    });
});

