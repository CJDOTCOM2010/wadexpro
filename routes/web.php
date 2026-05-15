<?php

use App\Http\Controllers\GlobalRedirectController;
use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Models\SystemSetting;

// Browser detection and redirection
Route::get('/', GlobalRedirectController::class);

// Health Check Operations (Phase 24)
Route::get('/v1/health', [\App\Http\Controllers\Api\HealthCheckController::class, 'ping']);

// Temporary diagnostic — remove after debugging
Route::get('/v1/debug-dashboard', function () {
    try {
        // Test 1: Can we resolve the Vite manifest?
        $viteOk = file_exists(public_path('build/manifest.json')) ? 'YES' : 'NO';
        
        // Test 2: Can we resolve SystemSetting?
        try {
            $settingOk = \App\Modules\Admin\Models\SystemSetting::count() >= 0 ? 'YES' : 'NO';
        } catch (\Exception $e) {
            $settingOk = 'FAIL: ' . $e->getMessage();
        }
        
        // Test 3: Can we render the layout?
        try {
            $metrics = [
                'active_drivers' => 0, 'pending_orders' => 0,
                'system_load' => '0%', 'daily_revenue' => '$0.00',
            ];
            $telemetry = ['drivers' => [], 'requests' => []];
            $view = view('admin.dashboard', compact('metrics', 'telemetry'))->render();
            $renderOk = 'YES (length: ' . strlen($view) . ')';
        } catch (\Exception $e) {
            $renderOk = 'FAIL: ' . get_class($e) . ' — ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine();
        }
        
        // Test 4: Check the Admin auth guard
        try {
            $guardOk = auth('admin')->check() ? 'AUTHENTICATED' : 'GUEST';
        } catch (\Exception $e) {
            $guardOk = 'FAIL: ' . $e->getMessage();
        }

        return response()->json([
            'vite_manifest' => $viteOk,
            'system_settings_db' => $settingOk,
            'dashboard_render' => $renderOk,
            'admin_guard' => $guardOk,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'session_driver' => config('session.driver'),
            'cache_driver' => config('cache.default'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'fatal_error' => get_class($e) . ': ' . $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ], 500);
    }
});

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

