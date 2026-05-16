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
use App\Modules\Admin\Controllers\LiveChatController;
use App\Modules\Admin\Controllers\SystemSettingController;
use App\Modules\Admin\Controllers\AssetManagementController;
use App\Modules\Admin\Controllers\BackupController;
use App\Modules\Admin\Controllers\NotificationController;
use App\Modules\Admin\Controllers\OnboardingController;
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
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('orchestrator.dashboard');
        
        // Platform Core
        Route::get('/users', [\App\Modules\Admin\Controllers\UserController::class, 'index'])->name('orchestrator.users');
        Route::post('/users', [\App\Modules\Admin\Controllers\UserController::class, 'store'])->name('orchestrator.users.store');
        Route::put('/users/{id}', [\App\Modules\Admin\Controllers\UserController::class, 'update'])->name('orchestrator.users.update');
        Route::patch('/users/{id}/toggle', [\App\Modules\Admin\Controllers\UserController::class, 'toggleStatus'])->name('orchestrator.users.toggle');
        Route::delete('/users/{id}', [\App\Modules\Admin\Controllers\UserController::class, 'destroy'])->name('orchestrator.users.destroy');

        // Admin Profile & Account Settings
        Route::get('/profile', [\App\Modules\Admin\Controllers\ProfileController::class, 'index'])->name('orchestrator.profile');
        Route::post('/profile', [\App\Modules\Admin\Controllers\ProfileController::class, 'updateProfile'])->name('orchestrator.profile.update');
        Route::post('/profile/password', [\App\Modules\Admin\Controllers\ProfileController::class, 'changePassword'])->name('orchestrator.profile.password');
        Route::post('/profile/notifications', [\App\Modules\Admin\Controllers\ProfileController::class, 'updateNotifications'])->name('orchestrator.profile.notifications');
        Route::post('/profile/revoke-sessions', [\App\Modules\Admin\Controllers\ProfileController::class, 'revokeAllSessions'])->name('orchestrator.profile.revoke');
        // Security & Fraud (Restricted)
        Route::middleware('admin_department:Security|IT|Engineering')->group(function() {
            Route::get('/security', [\App\Modules\Admin\Controllers\SecurityController::class, 'index'])->name('orchestrator.security');
            Route::patch('/security/alerts/{id}/resolve', [\App\Modules\Admin\Controllers\SecurityController::class, 'resolveAlert'])->name('orchestrator.security.alerts.resolve');
            Route::post('/security/devices/block', [\App\Modules\Admin\Controllers\SecurityController::class, 'blockDevice'])->name('orchestrator.security.devices.block');
            Route::patch('/security/devices/{id}/unblock', [\App\Modules\Admin\Controllers\SecurityController::class, 'unblockDevice'])->name('orchestrator.security.devices.unblock');
            
            // Error Monitoring
            Route::get('/error-monitoring', [\App\Modules\Admin\Controllers\ErrorMonitoringController::class, 'index'])->name('orchestrator.error_monitoring');
            Route::post('/error-monitoring/clear', [\App\Modules\Admin\Controllers\ErrorMonitoringController::class, 'clear'])->name('orchestrator.error_monitoring.clear');
        });

        // Operations & Logistics (Restricted)
        Route::middleware('admin_department:Operations|Logistics|Dispatch')->group(function() {
            Route::get('/operations-map', [\App\Modules\Admin\Controllers\OperationsController::class, 'map'])->name('orchestrator.operations_map');
            Route::get('/dispatcher', [\App\Modules\Admin\Controllers\OperationsController::class, 'dispatcher'])->name('orchestrator.dispatcher');
            Route::get('/orders', [\App\Modules\Admin\Controllers\OperationsController::class, 'globalQueue'])->name('orchestrator.orders');
            Route::get('/drivers', fn() => view('admin.asset_registry'))->name('orchestrator.drivers');
        });
        // Treasury & Financials (Restricted)
        Route::middleware('admin_department:Finance|Accounting|Treasury')->group(function() {
            Route::get('/financials', [\App\Modules\Admin\Controllers\FinancialController::class, 'index'])->name('orchestrator.financials');
            Route::patch('/financials/payout/{id}/approve', [\App\Modules\Admin\Controllers\FinancialController::class, 'approvePayout'])->name('orchestrator.financials.payout.approve');
        });

        
        // CMS & Platform
        Route::view('/menus', 'admin.menus')->name('orchestrator.menus');

        // ── Roles & Permissions ──────────────────────────────────────────────
        Route::get('/roles',                          [\App\Modules\Admin\Controllers\RolePermissionController::class, 'index'])->name('orchestrator.roles.index');
        Route::post('/roles',                         [\App\Modules\Admin\Controllers\RolePermissionController::class, 'storeRole'])->name('orchestrator.roles.store');
        Route::put('/roles/{id}',                     [\App\Modules\Admin\Controllers\RolePermissionController::class, 'updateRole'])->name('orchestrator.roles.update');
        Route::delete('/roles/{id}',                  [\App\Modules\Admin\Controllers\RolePermissionController::class, 'destroyRole'])->name('orchestrator.roles.destroy');
        Route::post('/permissions',                   [\App\Modules\Admin\Controllers\RolePermissionController::class, 'storePermission'])->name('orchestrator.permissions.store');
        Route::delete('/permissions/{id}',            [\App\Modules\Admin\Controllers\RolePermissionController::class, 'destroyPermission'])->name('orchestrator.permissions.destroy');
        Route::post('/users/{userId}/assign-role',    [\App\Modules\Admin\Controllers\RolePermissionController::class, 'assignRole'])->name('orchestrator.users.assign-role');
        Route::delete('/users/{userId}/revoke-role/{roleId}', [\App\Modules\Admin\Controllers\RolePermissionController::class, 'revokeRole'])->name('orchestrator.users.revoke-role');
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('orchestrator.settings');
        Route::get('/settings/branding', [SystemSettingController::class, 'branding'])->name('orchestrator.settings.branding');
        Route::get('/settings/dashboard-branding', [SystemSettingController::class, 'dashboardBranding'])->name('orchestrator.settings.dashboard_branding');
        
        // Asset Management
        Route::get('/settings/assets', [AssetManagementController::class, 'index'])->name('orchestrator.settings.assets');
        Route::post('/settings/assets/upload', [AssetManagementController::class, 'upload'])->name('orchestrator.settings.assets.upload');
        Route::post('/settings/assets/delete', [AssetManagementController::class, 'delete'])->name('orchestrator.settings.assets.delete');
        Route::post('/settings/assets/config', [AssetManagementController::class, 'updateConfig'])->name('orchestrator.settings.assets.config');
        
        // System Backups
        Route::get('/settings/backups', [BackupController::class, 'index'])->name('orchestrator.settings.backups');
        Route::post('/settings/backups/create', [BackupController::class, 'create'])->name('orchestrator.settings.backups.create');
        Route::get('/settings/backups/download/{file}', [BackupController::class, 'download'])->name('orchestrator.settings.backups.download');
        Route::delete('/settings/backups/delete/{file}', [BackupController::class, 'delete'])->name('orchestrator.settings.backups.delete');
        Route::post('/settings/backups/clean', [BackupController::class, 'clean'])->name('orchestrator.settings.backups.clean');
        Route::get('/settings/prefixes', [SystemSettingController::class, 'prefixes'])->name('orchestrator.settings.prefixes');
        Route::get('/settings/geolocation', [SystemSettingController::class, 'geolocation'])->name('orchestrator.settings.geolocation');
        Route::get('/settings/social-auth', [SystemSettingController::class, 'socialAuth'])->name('orchestrator.settings.social_auth');
        Route::get('/settings/authentication', [SystemSettingController::class, 'auth'])->name('orchestrator.settings.auth');
        Route::get('/settings/manifest', [SystemSettingController::class, 'manifest'])->name('orchestrator.settings.manifest');
        Route::get('/settings/localization', [SystemSettingController::class, 'localization'])->name('orchestrator.settings.localization');
        Route::get('/settings/payments', [SystemSettingController::class, 'payments'])->name('orchestrator.settings.payments');
        Route::get('/settings/security', [SystemSettingController::class, 'security'])->name('orchestrator.settings.security');
        Route::get('/settings/api-rate-limiting', [SystemSettingController::class, 'apiRateLimiting'])->name('orchestrator.settings.api_rate_limiting');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('orchestrator.settings.update');

        // Communication & Notifications
        Route::get('/settings/notifications',          [NotificationController::class, 'index'])->name('orchestrator.settings.notifications');
        Route::post('/settings/notifications',         [NotificationController::class, 'update'])->name('orchestrator.settings.notifications.update');
        Route::post('/settings/notifications/events',  [NotificationController::class, 'updateEvents'])->name('orchestrator.settings.notifications.events');
        Route::post('/settings/notifications/test',    [NotificationController::class, 'test'])->name('orchestrator.settings.notifications.test');

        // Notification Templates
        Route::get('/settings/templates',              [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'index'])->name('orchestrator.templates.index');
        Route::get('/settings/templates/create',       [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'create'])->name('orchestrator.templates.create');
        Route::post('/settings/templates',             [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'store'])->name('orchestrator.templates.store');
        Route::get('/settings/templates/{id}/edit',    [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'edit'])->name('orchestrator.templates.edit');
        Route::put('/settings/templates/{id}',         [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'update'])->name('orchestrator.templates.update');
        Route::delete('/settings/templates/{id}',      [\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'destroy'])->name('orchestrator.templates.destroy');
        Route::patch('/settings/templates/{id}/toggle',[\App\Modules\Admin\Controllers\NotificationTemplateController::class, 'toggle'])->name('orchestrator.templates.toggle');

        // App Onboarding Slides
        Route::get('/settings/onboarding/{appType}', [OnboardingController::class, 'index'])->name('orchestrator.settings.onboarding');
        Route::post('/settings/onboarding', [OnboardingController::class, 'store'])->name('orchestrator.onboarding.store');
        Route::put('/settings/onboarding/{id}', [OnboardingController::class, 'update'])->name('orchestrator.onboarding.update');
        Route::delete('/settings/onboarding/{id}', [OnboardingController::class, 'destroy'])->name('orchestrator.onboarding.destroy');
        Route::patch('/settings/onboarding/{id}/toggle', [OnboardingController::class, 'toggle'])->name('orchestrator.onboarding.toggle');
        Route::post('/settings/onboarding/reorder', [OnboardingController::class, 'reorder'])->name('orchestrator.onboarding.reorder');
        Route::post('/settings/onboarding/splash', [OnboardingController::class, 'updateSplash'])->name('orchestrator.onboarding.splash.update');

        // ── IT & Engineering Department ───────────────────────────────────────
        Route::middleware('admin_department:IT|Engineering')->group(function() {
            Route::get('/infrastructure', [\App\Modules\Admin\Controllers\InfrastructureController::class, 'infrastructure'])->name('orchestrator.infrastructure');
            Route::post('/infrastructure/command', [\App\Modules\Admin\Controllers\InfrastructureController::class, 'cacheCommand'])->name('orchestrator.infrastructure.command');
            Route::get('/modules', [\App\Modules\Admin\Controllers\InfrastructureController::class, 'modules'])->name('orchestrator.modules');
        });

        // ── Driver Management Department ──────────────────────────────────────
        Route::middleware('admin_department:Operations|HR|Management')->group(function() {
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
        });

        // ── Customer Support Department ───────────────────────────────────────
        Route::middleware('admin_department:Support|Customer Service')->group(function() {
            // Support Tickets
            Route::get('/support/tickets',                        [SupportTicketController::class, 'index'])->name('orchestrator.support.tickets');
            Route::post('/support/tickets',                       [SupportTicketController::class, 'store'])->name('orchestrator.support.tickets.store');
            Route::get('/support/tickets/{id}',                   [SupportTicketController::class, 'show'])->name('orchestrator.support.ticket.show');
            Route::post('/support/tickets/{id}/reply',            [SupportTicketController::class, 'reply'])->name('orchestrator.support.ticket.reply');
            Route::post('/support/tickets/{id}/assign',           [SupportTicketController::class, 'assign'])->name('orchestrator.support.ticket.assign');
            Route::patch('/support/tickets/{id}/resolve',         [SupportTicketController::class, 'resolve'])->name('orchestrator.support.ticket.resolve');
            Route::patch('/support/tickets/{id}/close',           [SupportTicketController::class, 'close'])->name('orchestrator.support.ticket.close');

            // Live Chat
            Route::get('/support/livechat',                       [\App\Modules\Admin\Controllers\LiveChatController::class, 'index'])->name('orchestrator.livechat');
            Route::get('/support/livechat/{id}',                  [\App\Modules\Admin\Controllers\LiveChatController::class, 'show'])->name('orchestrator.livechat.show');
            Route::post('/support/livechat/{id}/reply',           [\App\Modules\Admin\Controllers\LiveChatController::class, 'reply'])->name('orchestrator.livechat.reply');
            Route::post('/support/livechat/{id}/close',           [\App\Modules\Admin\Controllers\LiveChatController::class, 'close'])->name('orchestrator.livechat.close');
            Route::post('/support/livechat/{id}/reopen',          [\App\Modules\Admin\Controllers\LiveChatController::class, 'reopen'])->name('orchestrator.livechat.reopen');
        });

        // ── Analytics & Reporting Department ─────────────────────────────────
        Route::get('/analytics', [OrchestratorAnalyticsController::class, 'index'])->name('orchestrator.analytics');

        // ── Marketing Department ──────────────────────────────────────────────
        Route::middleware('admin_department:Marketing|Growth|Communications')->group(function() {
            Route::get('/marketing/promotions',               [MarketingController::class, 'promos'])->name('orchestrator.marketing.promos');
            Route::post('/marketing/promotions',              [MarketingController::class, 'storePromo'])->name('orchestrator.marketing.promos.store');
            Route::put('/marketing/promotions/{id}',          [MarketingController::class, 'updatePromo'])->name('orchestrator.marketing.promos.update');
            Route::patch('/marketing/promotions/{id}/toggle', [MarketingController::class, 'togglePromo'])->name('orchestrator.marketing.promos.toggle');
            Route::delete('/marketing/promotions/{id}',       [MarketingController::class, 'destroyPromo'])->name('orchestrator.marketing.promos.destroy');

            Route::get('/marketing/banners',               [MarketingController::class, 'banners'])->name('orchestrator.marketing.banners');
            Route::post('/marketing/banners',              [MarketingController::class, 'storeBanner'])->name('orchestrator.marketing.banners.store');
            Route::patch('/marketing/banners/{id}/toggle', [MarketingController::class, 'toggleBanner'])->name('orchestrator.marketing.banners.toggle');
            Route::delete('/marketing/banners/{id}',       [MarketingController::class, 'destroyBanner'])->name('orchestrator.marketing.banners.destroy');
        });

        // ── CMS Department ────────────────────────────────────────────────────
        Route::middleware('admin_department:Marketing|Growth|Communications')->group(function() {
            Route::get('/cms/blog',              [ContentManagementController::class, 'blog'])->name('orchestrator.cms.blog');
            Route::post('/cms/blog',             [ContentManagementController::class, 'storeBlog'])->name('orchestrator.cms.blog.store');
            Route::put('/cms/blog/{id}',         [ContentManagementController::class, 'updateBlog'])->name('orchestrator.cms.blog.update');
            Route::delete('/cms/blog/{id}',      [ContentManagementController::class, 'destroyBlog'])->name('orchestrator.cms.blog.destroy');

            Route::get('/cms/faq',              [ContentManagementController::class, 'faq'])->name('orchestrator.cms.faq');
            Route::post('/cms/faq',             [ContentManagementController::class, 'storeFaq'])->name('orchestrator.cms.faq.store');
            Route::put('/cms/faq/{id}',         [ContentManagementController::class, 'updateFaq'])->name('orchestrator.cms.faq.update');
            Route::delete('/cms/faq/{id}',      [ContentManagementController::class, 'destroyFaq'])->name('orchestrator.cms.faq.destroy');
        });

        // ── HR Department ─────────────────────────────────────────────────────
        Route::middleware('admin_department:HR|Management')->group(function() {
            Route::get('/hr',                       [HRManagementController::class, 'index'])->name('orchestrator.hr');
            Route::get('/hr/onboard',               [HRManagementController::class, 'create'])->name('orchestrator.hr.create');
            Route::post('/hr',                      [HRManagementController::class, 'store'])->name('orchestrator.hr.store');
            Route::patch('/hr/{id}/role',           [HRManagementController::class, 'updateRole'])->name('orchestrator.hr.role');
            Route::patch('/hr/{id}/deactivate',     [HRManagementController::class, 'deactivate'])->name('orchestrator.hr.deactivate');
            Route::patch('/hr/{id}/activate',       [HRManagementController::class, 'activate'])->name('orchestrator.hr.activate');
            Route::post('/hr/{id}/reset-password',  [HRManagementController::class, 'resetPassword'])->name('orchestrator.hr.reset-password');
        });

    });
});

