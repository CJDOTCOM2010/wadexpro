<?php

use App\Http\Controllers\GlobalRedirectController;
use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Models\SystemSetting;

// Browser detection and redirection
Route::get('/', GlobalRedirectController::class);

// Health Check Operations (Phase 24)
Route::get('/v1/health', [\App\Http\Controllers\Api\HealthCheckController::class, 'ping']);

// Localized Public Routes
Route::prefix('{country}/{lang}')
    ->middleware(['web', \App\Http\Middleware\SetLocaleMiddleware::class])
    ->group(function () {
    
    Route::get('/', function () { return view('welcome'); })->name('home');
    
    Route::get('/ride', function () { 
        return view('ride', ['google_maps_api_key' => SystemSetting::get('google_maps_api_key')]); 
    })->name('ride');
    
    Route::get('/courier', function () { 
        return view('courier', ['google_maps_api_key' => SystemSetting::get('google_maps_api_key')]); 
    })->name('courier');
    
    Route::get('/moto', function () { return view('moto'); })->name('moto');
    
    Route::get('/reserve', function () { 
        return view('reserve', ['google_maps_api_key' => SystemSetting::get('google_maps_api_key')]); 
    })->name('reserve')->middleware('auth');
    
    // Auth Routes
    Route::get('/login', [\App\Modules\Auth\Controllers\Web\LoginController::class, 'show'])->name('login');
    Route::post('/login', [\App\Modules\Auth\Controllers\Web\LoginController::class, 'authenticate'])->name('login.submit');
    Route::get('/register', [\App\Modules\Auth\Controllers\Web\RegisterController::class, 'show'])->name('register');
    Route::post('/register', [\App\Modules\Auth\Controllers\Web\RegisterController::class, 'register'])->name('register.submit');
    Route::get('/login/challenge', [\App\Modules\Auth\Controllers\Web\LoginController::class, 'challenge'])->name('login.challenge');
    Route::post('/login/challenge', [\App\Modules\Auth\Controllers\Web\LoginController::class, 'verify'])->name('login.verify');
});

// Internal Admin Operations
Route::middleware(['web'])->group(function () {
    Route::get('/driver', function () {
        return view('driver.dashboard');
    })->name('driver.dashboard');
});

