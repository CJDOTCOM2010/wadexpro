<?php

namespace App\Modules\Logistics\Jobs;

use App\Modules\Logistics\Models\TrackingEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGpsPing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TrackingEvent::create([
            'order_id'    => $this->data['order_id'],
            'driver_id'   => $this->data['driver_id'],
            'event_type'  => 'gps_ping',
            'lat'         => $this->data['lat'],
            'lng'         => $this->data['lng'],
            'speed_kmh'   => $this->data['speed_kmh'] ?? 0.0,
            'bearing'     => $this->data['bearing'] ?? 0.0,
            'metadata'    => $this->data['metadata'] ?? null,
            'recorded_at' => $this->data['recorded_at'],
        ]);
    }
}
