<?php

namespace App\Modules\CMS;

use Illuminate\Support\ServiceProvider;

class CMSServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Services\CmsService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/routes.php');
    }
}
