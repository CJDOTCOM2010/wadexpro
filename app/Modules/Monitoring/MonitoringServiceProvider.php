<?php

namespace App\Modules\Monitoring;

use Illuminate\Support\ServiceProvider;

class MonitoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
