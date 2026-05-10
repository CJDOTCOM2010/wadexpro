<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'driver_id', 'event_type', 'lat', 'lng',
        'speed_kmh', 'bearing', 'metadata', 'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'lat'         => 'decimal:8',
            'lng'         => 'decimal:8',
            'speed_kmh'   => 'decimal:1',
            'metadata'    => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
