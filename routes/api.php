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
    
    // WADEX-Guard: Ultra-Resilience Profile endpoints (Allows virtual tokens to bypass sanctum for testing)
    Route::get('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'getProfile']);
    Route::put('/profile/update', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updateProfile']);
    Route::post('/profile/photo', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updatePhoto']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
