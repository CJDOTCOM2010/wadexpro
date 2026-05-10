<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WADEXP Module Registry
    |--------------------------------------------------------------------------
    | Each module entry maps a slug to its ServiceProvider class.
    | The 'enabled' key serves as the ENV-level default; the database
    | modules table is the runtime source of truth (AdminModule toggles it).
    */

    'logistics' => [
        'name'    => 'Logistics',
        'enabled' => (bool) env('MODULE_LOGISTICS_ENABLED', true),
        'class'   => \App\Modules\Logistics\LogisticsServiceProvider::class,
    ],

    'payments' => [
        'name'    => 'Payments',
        'enabled' => (bool) env('MODULE_PAYMENTS_ENABLED', true),
        'class'   => \App\Modules\Payments\PaymentsServiceProvider::class,
    ],

    'notifications' => [
        'name'    => 'Notifications',
        'enabled' => (bool) env('MODULE_NOTIFICATIONS_ENABLED', true),
        'class'   => \App\Modules\Notifications\NotificationsServiceProvider::class,
    ],

    'hr' => [
        'name'    => 'Human Resources',
        'enabled' => (bool) env('MODULE_HR_ENABLED', true),
        'class'   => \App\Modules\HR\HRServiceProvider::class,
    ],

    'accounting' => [
        'name'    => 'Accounting',
        'enabled' => (bool) env('MODULE_ACCOUNTING_ENABLED', true),
        'class'   => \App\Modules\Accounting\AccountingServiceProvider::class,
    ],

    'monitoring' => [
        'name'    => 'Monitoring',
        'enabled' => (bool) env('MODULE_MONITORING_ENABLED', true),
        'class'   => \App\Modules\Monitoring\MonitoringServiceProvider::class,
    ],

    'cms' => [
        'name'    => 'CMS',
        'enabled' => (bool) env('MODULE_CMS_ENABLED', true),
        'class'   => \App\Modules\CMS\CMSServiceProvider::class,
    ],

];
