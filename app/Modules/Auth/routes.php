<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Module Routes  —  /api/v1/auth/*
|--------------------------------------------------------------------------
*/

Route::prefix('v1/auth')->group(function () {

    // Public routes — no authentication required
    Route::post('/login',           [AuthController::class, 'login']);
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login/otp/send',  [AuthController::class, 'sendOtp']);
    Route::post('/login/otp',       [AuthController::class, 'loginWithOtp']);
    Route::post('/refresh',         [AuthController::class, 'refresh']);
    Route::get('/config',            [\App\Modules\Auth\Controllers\Api\ConfigController::class, 'getPublicConfig']);

    // Social Authentication (Mobile Verified Tokens)
    Route::post('/google/token', [SocialAuthController::class, 'loginWithGoogle']);
    Route::post('/facebook/token', [SocialAuthController::class, 'loginWithFacebook']);

    // Socialite Flow (Web/Redirect)
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

    // Protected routes — require valid Sanctum token
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',              [AuthController::class, 'me']);
        Route::post('/logout',         [AuthController::class, 'logout']);
        Route::post('/logout/all',     [AuthController::class, 'logoutAll']);
        Route::patch('/fcm-token',     [AuthController::class, 'updateFcmToken']);
    });
});
