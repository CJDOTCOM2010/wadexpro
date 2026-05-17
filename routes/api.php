<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * WADEXPRO API LAYER
 * --------------------------------------------------------------------------
 * Most logic is handled in modular route files. This file serves as the 
 * primary entry point for global API health and custom unified endpoints.
 */

Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'platform' => 'WADEXPRO Express',
        'timestamp' => now()
    ]);
});

use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/auth/login/otp', [AuthController::class, 'verifyOtp']);

    // Onboarding and Splash endpoints
    Route::get('/onboarding/{appType}', [\App\Modules\Admin\Controllers\OnboardingController::class, 'apiIndex']);
    Route::get('/platform/splash/{appType}', [\App\Modules\Admin\Controllers\OnboardingController::class, 'apiSplash']);
    
    // Build Branding Endpoint for Scripts
    Route::get('/platform/build-branding/{appType}', function ($appType) {
        if (!in_array($appType, ['customer', 'driver'])) {
            return response()->json(['error' => 'Invalid app type'], 400);
        }
        $settings = \App\Modules\Admin\Models\SystemSetting::whereIn('key', [
            "{$appType}_app_display_name",
            "{$appType}_app_icon_url",
            'app_icon_url' // fallback
        ])->pluck('value', 'key');
        
        return response()->json([
            'appName' => $settings["{$appType}_app_display_name"] ?? 'WADEXPRO',
            'appIconUrl' => $settings["{$appType}_app_icon_url"] ?? ($settings['app_icon_url'] ?? ''),
        ]);
    });
    
    // WADEX-Guard: Ultra-Resilience Profile endpoints (Allows virtual tokens to bypass sanctum for testing)
    Route::get('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'getProfile']);
    Route::put('/profile/update', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updateProfile']);
    Route::post('/profile/photo', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updatePhoto']);

    // Mobile Wallet Endpoints (Shared & Customer)
    Route::get('/payments/wallet/hub', [\App\Http\Controllers\Api\V1\CustomerWalletController::class, 'getHubData']);
    Route::get('/logistics/wallet/transactions', [\App\Http\Controllers\Api\V1\CustomerWalletController::class, 'getTransactions']);
    Route::post('/logistics/wallet/topup', [\App\Http\Controllers\Api\V1\CustomerWalletController::class, 'initializeTopUp']);
    Route::get('/logistics/wallet/verify', [\App\Http\Controllers\Api\V1\CustomerWalletController::class, 'verifyTopUp']);
    Route::post('/payments/wallet/hub/promos/check', [\App\Http\Controllers\Api\V1\CustomerWalletController::class, 'checkPromo']);

    Route::prefix('logistics')->group(function () {
        Route::get('/hubs/{serviceType}', [\App\Http\Controllers\Api\V1\CustomerLogisticsController::class, 'getHubs']);
        Route::get('/vehicles', [\App\Http\Controllers\Api\V1\CustomerLogisticsController::class, 'getRentals']);
        Route::post('/book', [\App\Http\Controllers\Api\V1\CustomerLogisticsController::class, 'bookLogistics']);
    });

    // Telemetry Endpoint
    Route::post('/telemetry/location', [\App\Http\Controllers\Api\V1\TelemetryController::class, 'updateLocation']);

    // Driver Specific Endpoints
    Route::get('/logistics/profile', [\App\Http\Controllers\Api\V1\DriverProfileController::class, 'getProfile']);
    Route::patch('/logistics/profile', [\App\Http\Controllers\Api\V1\DriverProfileController::class, 'updateProfile']);
    Route::post('/logistics/profile/documents', [\App\Http\Controllers\Api\V1\DriverProfileController::class, 'uploadDocument']);
    Route::get('/logistics/wallet/balance', [\App\Http\Controllers\Api\V1\DriverWalletController::class, 'getBalance']);
    Route::get('/logistics/wallet/weekly-summary', [\App\Http\Controllers\Api\V1\DriverWalletController::class, 'getWeeklySummary']);
    Route::post('/logistics/wallet/payout', [\App\Http\Controllers\Api\V1\DriverWalletController::class, 'requestPayout']);

    // Mock WebView for TopUp testing
    Route::get('/mock/wallet/topup/webview', function(Request $request) {
        $ref = $request->query('reference', 'UNKNOWN');
        return "<html><body style='background:#111;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;'>
                <h1>WADEXPRO Mock Gateway</h1>
                <p>Simulating Payment...</p>
                <button onclick=\"window.location.href='/api/v1/wallet/verify?reference={$ref}';\" style='padding:15px 30px;background:#00D4AA;border:none;border-radius:8px;color:#fff;font-weight:bold;margin-top:20px;'>Authorize Top-Up</button>
                </body></html>";
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
