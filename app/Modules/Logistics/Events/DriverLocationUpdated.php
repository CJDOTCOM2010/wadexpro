<?php

namespace App\Modules\Logistics\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $driverId,
        public float $lat,
        public float $lng,
        public ?string $orderId = null
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     * Public channel for map visibility.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('driver_tracking:' . $this->driverId),
            $this->orderId ? new Channel('order_tracking:' . $this->orderId) : null,
        ];
    }

    /**
     * The name under which the event will be broadcasted.
     */
    public function broadcastAs(): string
    {
        return 'LocationUpdated';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'driver_id' => $this->driverId,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
