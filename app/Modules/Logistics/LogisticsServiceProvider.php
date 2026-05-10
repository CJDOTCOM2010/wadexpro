<?php

namespace App\Modules\Logistics;

use Illuminate\Support\ServiceProvider;

class LogisticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Logistics\Services\PricingService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\DriverService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\OrderService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\OptimizationService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\PayoutService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\RideMatchingService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\FareCalculationService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\SurgeService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\SosService::class);
        $this->app->singleton(\App\Modules\Logistics\Services\AntifraudService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Logistics\Commands\RunDriverPayouts::class,
            ]);
        }

        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
