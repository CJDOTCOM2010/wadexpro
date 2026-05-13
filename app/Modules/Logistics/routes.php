<?php

use App\Modules\Logistics\Controllers\DriverOrderController;
use App\Modules\Logistics\Controllers\OptimizationController;
use App\Modules\Logistics\Controllers\OrderController;
use App\Modules\Logistics\Controllers\PayoutController;
use App\Modules\Logistics\Controllers\RideRequestController;
use App\Modules\Logistics\Controllers\DriverController;
use App\Modules\Logistics\Controllers\PromotionController;
use App\Modules\Logistics\Controllers\SOSController;
use App\Modules\Logistics\Controllers\AnalyticsController;
use App\Modules\Logistics\Controllers\FinancialController;
use App\Modules\Logistics\Controllers\DriverKYCController;
use App\Modules\Logistics\Controllers\AdminDriverController;
use App\Modules\Logistics\Controllers\Admin\SurgeManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Logistics Module Routes  —  /api/v1/logistics/*
|--------------------------------------------------------------------------
| Consolidated Primary Environment (wadexpro_plus)
*/

Route::prefix('v1/logistics')->middleware('auth:sanctum')->group(function () {

    // -----------------------------------------------------------------------
    // Safety & SOS (Mobile Side)
    // -----------------------------------------------------------------------
    Route::post('/sos', [SOSController::class, 'store'])->middleware('throttle:sos');

    // -----------------------------------------------------------------------
    // Financial & Wallet Endpoints
    // -----------------------------------------------------------------------
    Route::prefix('wallet')->group(function () {
        Route::get('/balance', [FinancialController::class, 'getBalance']);
        Route::get('/transactions', [FinancialController::class, 'getTransactions']);
        Route::post('/topup', [FinancialController::class, 'initializeTopUp']);
        Route::get('/verify', [FinancialController::class, 'verifyTopUp']);
        
        // Driver Specifics
        Route::get('/weekly-summary', [\App\Modules\Logistics\Controllers\DriverFinancialController::class, 'weeklySummary']);
        Route::post('/payout', [\App\Modules\Logistics\Controllers\DriverFinancialController::class, 'requestPayout']);
    });

    // -----------------------------------------------------------------------
    // Ride Request Endpoints (Passenger Side)
    // -----------------------------------------------------------------------
    Route::prefix('rides')->group(function () {
        Route::post('/estimate', [RideRequestController::class, 'estimate']);
        Route::post('/', [RideRequestController::class, 'store']);
        Route::get('/{id}', [RideRequestController::class, 'show']);
        
        // Real-time Operational Chat
        Route::get('/{id}/chat/history', [\App\Modules\Logistics\Controllers\ChatController::class, 'history']);
        Route::post('/{id}/chat/send', [\App\Modules\Logistics\Controllers\ChatController::class, 'send']);
    });

    // -----------------------------------------------------------------------
    // Simulation Endpoints (Development/Staging Only)
    // -----------------------------------------------------------------------
    if (!app()->isProduction()) {
        Route::post('/simulate/accept/{id}', [\App\Modules\Logistics\Controllers\SimulateController::class, 'acceptRide']);
        Route::post('/simulate/complete/{id}', [\App\Modules\Logistics\Controllers\SimulateController::class, 'completeRide']);
        Route::post('/simulate/sos/{id}', [\App\Modules\Logistics\Controllers\SimulateController::class, 'triggerSOS']);
    }

    // -----------------------------------------------------------------------
    // Customer Endpoints
    // -----------------------------------------------------------------------
    Route::prefix('orders')->group(function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/optimize-route', [OptimizationController::class, 'optimize']);
    });

    Route::prefix('support/chat')->group(function () {
        Route::get('/', [\App\Modules\Logistics\Controllers\CustomerSupportChatController::class, 'getActive']);
        Route::post('/send', [\App\Modules\Logistics\Controllers\CustomerSupportChatController::class, 'send']);
    });

    Route::prefix('support/tickets')->group(function () {
        Route::get('/', [\App\Modules\Logistics\Controllers\CustomerSupportTicketController::class, 'index']);
        Route::post('/', [\App\Modules\Logistics\Controllers\CustomerSupportTicketController::class, 'store']);
        Route::post('/{id}/reply', [\App\Modules\Logistics\Controllers\CustomerSupportTicketController::class, 'reply']);
    });

    // -----------------------------------------------------------------------
    // Personal Order Endpoints (Customer Side)
    // -----------------------------------------------------------------------
    Route::prefix('personal/orders')->group(function () {
        Route::get('/', [\App\Modules\Logistics\Controllers\PersonalOrderController::class, 'index']);
        Route::get('/{id}', [\App\Modules\Logistics\Controllers\PersonalOrderController::class, 'show']);
        Route::post('/{id}/cancel', [\App\Modules\Logistics\Controllers\PersonalOrderController::class, 'cancel']);
    });

    // -----------------------------------------------------------------------
    // Driver Endpoints
    // -----------------------------------------------------------------------
    Route::prefix('driver')->group(function () {
        Route::patch('/status', [DriverController::class, 'toggleStatus']);
        Route::get('/rides/available', [DriverController::class, 'availableRides']);
        Route::patch('/rides/{id}/accept', [DriverController::class, 'acceptRide']);
        Route::patch('/rides/{id}/status', [DriverController::class, 'updateStatus']);
        
        Route::post('/location', [DriverOrderController::class, 'updateLocation'])->middleware('throttle:telemetry');
        Route::patch('/orders/{orderId}/status', [DriverOrderController::class, 'updateOrderStatus']);

        // KYC & Onboarding
        Route::prefix('kyc')->group(function () {
            Route::get('/status', [DriverKYCController::class, 'getStatus']);
            Route::post('/upload', [DriverKYCController::class, 'uploadDocuments']);
        });

        // Multi-stop Order Management
        Route::patch('/stops/{id}/status', [\App\Modules\Logistics\Controllers\OrderStopController::class, 'updateStatus']);
    });

    // -----------------------------------------------------------------------
    // Admin / Ops Endpoints (requires specific RBAC matching the prefix)
    // -----------------------------------------------------------------------
    Route::prefix('admin')->middleware('role:super_admin|admin')->group(function () {
        
        // Analytics
        Route::get('/analytics/overview', [\App\Modules\Logistics\Controllers\Admin\AnalyticsController::class, 'overview']);
        Route::get('/analytics/revenue', [\App\Modules\Logistics\Controllers\Admin\AnalyticsController::class, 'revenue']);
        Route::get('/analytics/rides', [\App\Modules\Logistics\Controllers\Admin\AnalyticsController::class, 'rides']);
        Route::get('/analytics/drivers', [\App\Modules\Logistics\Controllers\Admin\AnalyticsController::class, 'drivers']);

        // Driver Fleet Management
        Route::prefix('drivers')->group(function () {
            Route::get('/',               [AdminDriverController::class, 'index']);
            Route::get('/{id}',           [AdminDriverController::class, 'show']);
            Route::post('/{id}/approve',  [AdminDriverController::class, 'approve']);
            Route::post('/{id}/reject',   [AdminDriverController::class, 'reject']);
            Route::post('/{id}/suspend',  [AdminDriverController::class, 'suspend']);
        });

        // Analytics & Heatmaps
        Route::get('/analytics/demand-heatmap', [AnalyticsController::class, 'demandHeatmap']);

        // SOS Monitoring & Real-time Alerts
        Route::prefix('sos')->group(function () {
            Route::get('/', [SOSController::class, 'index']);
            Route::post('/', [SOSController::class, 'store']); // Simulation trigger
            Route::patch('/{id}', [SOSController::class, 'update']);
        });

        // Real-time Driver Telemetry (Live Map)
        Route::get('/live-map/drivers', [\App\Modules\Logistics\Controllers\Admin\LiveMapController::class, 'index']);

        // Payout Management
        Route::prefix('payouts')->group(function () {
            Route::get('/', [PayoutController::class, 'index']);
            Route::post('/calculate', [PayoutController::class, 'calculate']);
            Route::post('/{payoutId}/execute', [PayoutController::class, 'execute']);
        });

        // Surge & Dynamic Pricing Management
        Route::prefix('surge')->group(function () {
            Route::get('/',              [SurgeManagementController::class, 'index']);
            Route::post('/',             [SurgeManagementController::class, 'store']);
            Route::get('/{id}',          [SurgeManagementController::class, 'show']);
            Route::put('/{id}',          [SurgeManagementController::class, 'update']);
            Route::delete('/{id}',       [SurgeManagementController::class, 'destroy']);
            Route::post('/{id}/rules',   [SurgeManagementController::class, 'syncRules']);
        });

        // Analytics & Business Intelligence
        Route::prefix('analytics')->group(function () {
            Route::get('/overview',      [AnalyticsController::class, 'getOverview']);
            Route::get('/trends',        [AnalyticsController::class, 'getTrends']);
            Route::get('/distribution',  [AnalyticsController::class, 'getVehicleDistribution']);
            Route::get('/heatmap',       [AnalyticsController::class, 'demandHeatmap']);
        });

        // Growth & Referrals Console
        Route::prefix('referrals')->group(function () {
            Route::get('/metrics',       [\App\Modules\Logistics\Controllers\Admin\AdminReferralController::class, 'getMetrics']);
            Route::get('/conversions',   [\App\Modules\Logistics\Controllers\Admin\AdminReferralController::class, 'getRecentConversions']);
        });

        // Promotion & Campaign Management
        Route::prefix('promotions')->group(function () {
            Route::get('/', [PromotionController::class, 'index']);
            Route::post('/', [PromotionController::class, 'store']);
            Route::put('/{id}', [PromotionController::class, 'update']);
            Route::delete('/{id}', [PromotionController::class, 'destroy']);
            Route::patch('/{id}/toggle', [PromotionController::class, 'toggle']);
        });

        // Accounting & General Ledger
        Route::prefix('accounting')->group(function () {
            Route::get('/ledger', [\App\Modules\Logistics\Controllers\Admin\AccountingController::class, 'ledger']);
            Route::get('/summary', [\App\Modules\Logistics\Controllers\Admin\AccountingController::class, 'revenueSummary']);
            Route::get('/breakdown', [\App\Modules\Logistics\Controllers\Admin\AccountingController::class, 'earningsBreakdown']);
        });

        // Advanced Logistics & Fleet Orchestration
        Route::get('/deliveries', [\App\Modules\Logistics\Controllers\Admin\LogisticsOrchestratorController::class, 'index']);
        Route::get('/deliveries/{id}', [\App\Modules\Logistics\Controllers\Admin\LogisticsOrchestratorController::class, 'show']);
        Route::get('/fleet/overview', [\App\Modules\Logistics\Controllers\Admin\LogisticsOrchestratorController::class, 'fleetOverview']);
        Route::get('/fleet/vehicles', [\App\Modules\Logistics\Controllers\Admin\LogisticsOrchestratorController::class, 'indexVehicles']);

        // Global Expansion & Regions
        Route::prefix('regions')->group(function () {
            Route::get('/', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'index']);
            Route::post('/', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'store']);
            Route::get('/{id}', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'show']);
            Route::put('/{id}', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'update']);
            Route::delete('/{id}', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'destroy']);
            Route::post('/{id}/rates', [\App\Modules\Logistics\Controllers\Admin\RegionController::class, 'syncRates']);
        });

        // Advanced Security & Safety (WADEX-Guard)
        Route::prefix('safety')->group(function () {
            Route::get('/alerts', [\App\Modules\Logistics\Controllers\Admin\SafetyController::class, 'alerts']);
            Route::get('/alerts/{id}', [\App\Modules\Logistics\Controllers\Admin\SafetyController::class, 'show']);
            Route::post('/alerts/{id}/resolve', [\App\Modules\Logistics\Controllers\Admin\SafetyController::class, 'resolve']);
            Route::get('/stats', [\App\Modules\Logistics\Controllers\Admin\SafetyController::class, 'stats']);
        });

        // Enterprise & Multi-Tenancy (Organizations Hub)
        Route::prefix('organizations')->group(function () {
            Route::get('/', [\App\Modules\Logistics\Controllers\Admin\OrganizationController::class, 'index']);
            Route::post('/', [\App\Modules\Logistics\Controllers\Admin\OrganizationController::class, 'store']);
            Route::get('/{id}', [\App\Modules\Logistics\Controllers\Admin\OrganizationController::class, 'show']);
            Route::put('/{id}/billing', [\App\Modules\Logistics\Controllers\Admin\OrganizationController::class, 'updateBilling']);
            Route::post('/{id}/members', [\App\Modules\Logistics\Controllers\Admin\OrganizationController::class, 'addMembers']);
            Route::post('/{id}/bulk', [\App\Modules\Logistics\Controllers\Admin\BulkShipmentController::class, 'store']);
        });
    });

    // -----------------------------------------------------------------------
    // Admin / Reporting Endpoints
    // -----------------------------------------------------------------------
    Route::prefix('admin/reports')->middleware('role:super_admin|admin')->group(function () {
        Route::get('/driver/{driverId}/performance', [\App\Modules\Logistics\Controllers\ReportController::class, 'downloadDriverPerformance']);
        Route::get('/financials/export', [\App\Modules\Logistics\Controllers\ReportController::class, 'exportFinancials']);
    });
});
