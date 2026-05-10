<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/**
 * High-priority Admin Alerts (SOS, Fraud, System Failures)
 */
Broadcast::channel('admin.alerts', function ($user) {
    return $user->user_type === 'super_admin' || $user->hasRole(['super_admin', 'admin']);
});

/**
 * Ride-specific driver notifications
 */
Broadcast::channel('driver_notifications:{driverId}', function ($user, $driverId) {
    return (string) $user->driver?->id === (string) $driverId;
});

/**
 * User-specific direct notifications
 */
Broadcast::channel('user_notifications:{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});
