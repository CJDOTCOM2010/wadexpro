<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use App\Modules\Monitoring\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RideRequest extends Model
{
    use HasUuid, Auditable, SoftDeletes;

    protected $table = 'ride_requests';

    protected $fillable = [
        'customer_id',
        'driver_id',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_address',
        'dropoff_lat',
        'dropoff_lng',
        'vehicle_type',
        'status',
        'estimated_price',
        'final_price',
        'estimated_duration_minutes',
        'estimated_distance_km',
        'scheduled_at'
    ];

    protected function casts(): array
    {
        return [
            'pickup_lat' => 'decimal:8',
            'pickup_lng' => 'decimal:8',
            'dropoff_lat' => 'decimal:8',
            'dropoff_lng' => 'decimal:8',
            'estimated_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'estimated_distance_km' => 'decimal:2',
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Get the customer (user) that requested the ride.
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    /**
     * Get the driver assigned to the ride request.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Scope a query to only include pending/searching rides.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'searching']);
    }
}
