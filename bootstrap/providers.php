<?php

use App\Providers\AppServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Accounting\AccountingServiceProvider::class,
    App\Modules\Admin\AdminServiceProvider::class,
    App\Modules\Auth\AuthServiceProvider::class,
    App\Modules\CMS\CMSServiceProvider::class,
    App\Modules\HR\HRServiceProvider::class,
    App\Modules\Logistics\LogisticsServiceProvider::class,
    App\Modules\Monitoring\MonitoringServiceProvider::class,
    App\Modules\Notifications\NotificationsServiceProvider::class,
    App\Modules\Payments\PaymentsServiceProvider::class,
];
