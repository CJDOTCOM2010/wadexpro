<?php

namespace App\Modules\Logistics\Events;

use App\Modules\Logistics\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStopsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Order $order)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     * Maps to the Redis channel pattern expected by the socket server: "order_tracking:{id}"
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order_tracking:' . $this->order->id),
        ];
    }

    /**
     * The name under which the event will be broadcasted.
     */
    public function broadcastAs(): string
    {
        return 'StopsUpdated';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'stops' => $this->order->stops()->get()->toArray(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
