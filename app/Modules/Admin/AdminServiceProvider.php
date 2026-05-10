<?php

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Admin\Services\AdminLogService::class);
        $this->app->singleton(\App\Modules\Admin\Services\SystemSettingsService::class);
        $this->app->singleton(\App\Modules\Admin\Services\ModuleManagementService::class);
    }

    public function boot(): void
    {
        // Load API Routes
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');

        // Load Web Routes
        \Illuminate\Support\Facades\Route::middleware('web')
            ->group(__DIR__ . '/routes_web.php');
    }
}
