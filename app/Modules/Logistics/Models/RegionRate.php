<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class RegionRate extends Model
{
    use HasUuid;

    protected $fillable = [
        'region_id',
        'vehicle_type',
        'base_fare',
        'per_km',
        'per_minute',
        'minimum_fare',
        'booking_fee'
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km' => 'decimal:2',
        'per_minute' => 'decimal:2',
        'minimum_fare' => 'decimal:2',
        'booking_fee' => 'decimal:2'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
