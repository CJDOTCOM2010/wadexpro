<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',             // 'Motorcycle', 'Car (Standard)', 'Car (Premium)', 'Van (Cargo)', 'Truck', 'Tricycle'
        'slug',
        'description',
        'icon_url',
        'image_url',
        'base_fare',
        'per_km_rate',
        'per_minute_rate',
        'min_fare',
        'capacity',         // max passengers
        'max_weight_kg',    // for cargo types
        'is_active',
        'service_types',    // ['ride', 'courier', 'charter'] — JSON array
        'sort_order',
    ];

    protected $casts = [
        'base_fare'       => 'decimal:2',
        'per_km_rate'     => 'decimal:2',
        'per_minute_rate' => 'decimal:2',
        'min_fare'        => 'decimal:2',
        'capacity'        => 'integer',
        'max_weight_kg'   => 'decimal:2',
        'is_active'       => 'boolean',
        'service_types'   => 'array',
        'sort_order'      => 'integer',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
