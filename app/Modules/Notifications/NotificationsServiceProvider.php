<?php

namespace App\Modules\Notifications;

use Illuminate\Support\ServiceProvider;
use App\Modules\Notifications\Providers\HubtelProvider;
use App\Modules\Notifications\Channels\SmsChannel;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HubtelProvider::class);
        $this->app->singleton(SmsChannel::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
