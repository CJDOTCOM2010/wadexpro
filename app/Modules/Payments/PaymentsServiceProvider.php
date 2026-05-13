<?php

namespace App\Modules\Payments;

use App\Modules\Payments\Providers\PaystackProvider;
use App\Modules\Payments\Providers\FlutterwaveProvider;
use App\Modules\Payments\Providers\StripeProvider;
use App\Modules\Payments\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the standardized PaymentService with all active gateway classes
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService([
                $app->make(PaystackProvider::class),
                $app->make(FlutterwaveProvider::class),
                $app->make(StripeProvider::class),
            ], $app->make(\App\Modules\Accounting\Services\AccountingService::class));
        });
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
