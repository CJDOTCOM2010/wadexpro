<?php

namespace App\Modules\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Modules\Notifications\Channels\SmsChannel;
use App\Modules\Logistics\Models\Order;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Example logic: Send SMS only if priority is express or status is delivered
        if ($this->order->priority === 'express' || in_array($this->order->status, ['delivered', 'assigned'])) {
            $channels[] = SmsChannel::class;
        }

        // We would also add 'fcm' here once Firebase SDK is fully installed 
        
        return $channels;
    }

    public function toSms(object $notifiable): string
    {
        // Apply the user's preferred locale for this message context
        $locale = $notifiable->preferred_locale ?? 'en';
        
        return __("order.{$this->order->status}", [
            'id' => $this->order->id,
            'driver' => $this->order->driver?->user?->name ?? 'WadExp Driver'
        ], $locale);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => "Order {$this->order->id} status changed to {$this->order->status}"
        ];
    }
}
