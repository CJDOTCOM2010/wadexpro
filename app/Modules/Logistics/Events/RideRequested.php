<?php

namespace App\Modules\Logistics\Events;

use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public RideRequest $rideRequest,
        public array $notifiedDriverIds
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('admin:notifications'),
            new Channel('user_notifications:' . $this->rideRequest->customer_id),
        ];

        foreach ($this->notifiedDriverIds as $driverId) {
            $channels[] = new Channel('driver_notifications:' . $driverId);
        }

        return $channels;
    }

    /**
     * The name under which the event will be broadcasted.
     */
    public function broadcastAs(): string
    {
        return 'RideRequested';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'ride_id'          => $this->rideRequest->id,
            'pickup_address'   => $this->rideRequest->pickup_address,
            'pickup_lat'       => $this->rideRequest->pickup_lat,
            'pickup_lng'       => $this->rideRequest->pickup_lng,
            'dropoff_address'  => $this->rideRequest->dropoff_address,
            'dropoff_lat'      => $this->rideRequest->dropoff_lat,
            'dropoff_lng'      => $this->rideRequest->dropoff_lng,
            'estimated_price'  => $this->rideRequest->estimated_price,
            'estimated_distance' => $this->rideRequest->estimated_distance_km,
            'vehicle_type'     => $this->rideRequest->vehicle_type,
            'customer_name'    => $this->rideRequest->customer->name ?? 'Guest',
            'timestamp'        => now()->toIso8601String(),
        ];
    }
}
