<?php

namespace App\Modules\Accounting;

use App\Modules\Accounting\Services\AccountingService;
use App\Modules\Accounting\Services\InvoiceService;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccountingService::class);
        $this->app->singleton(InvoiceService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
