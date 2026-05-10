<?php

namespace App\Modules\HR;

use App\Modules\HR\Services\EmployeeService;
use App\Modules\HR\Services\AttendanceService;
use App\Modules\HR\Services\PayrollService;
use Illuminate\Support\ServiceProvider;

class HRServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmployeeService::class);
        $this->app->singleton(AttendanceService::class);
        $this->app->singleton(PayrollService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
