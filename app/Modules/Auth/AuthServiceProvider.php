<?php

namespace App\Modules\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Auth\Services\AuthService::class);
        $this->app->singleton(\App\Modules\Auth\Services\TokenService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
