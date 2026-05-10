<?php

namespace App\Modules\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Modules\Notifications\Providers\HubtelProvider;

class SmsChannel
{
    public function __construct(private HubtelProvider $provider)
    {
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $notifiable->routeNotificationFor('sms');

        if (!$phone) {
            return;
        }

        $this->provider->send($phone, $message);
    }
}
