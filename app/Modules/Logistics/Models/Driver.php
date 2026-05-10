<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasUuid, HasFactory;

    protected $table = 'drivers';
    protected $fillable = [
        'user_id', 'license_number', 'license_expires_at', 'license_class',
        'is_online', 'is_available', 'current_lat', 'current_lng',
        'last_location_at', 'rating', 'total_deliveries',
        'total_cancellations', 'status',
        'id_card_front_url', 'id_card_back_url', 'driver_photo_url',
        'verified_at', 'verification_notes', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_online'        => 'boolean',
            'is_available'     => 'boolean',
            'current_lat'      => 'decimal:8',
            'current_lng'      => 'decimal:8',
            'last_location_at' => 'datetime',
            'rating'           => 'decimal:2',
            'license_expires_at' => 'date',
            'verified_at'      => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function activeVehicle()
    {
        return $this->hasOne(Vehicle::class)->where('is_active', true)->latestOfMany();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class)
            ->whereIn('status', ['assigned', 'picked_up', 'in_transit'])
            ->latestOfMany();
    }

    /** Create a new factory instance for the model. */
    protected static function newFactory()
    {
        return \Database\Factories\DriverFactory::new();
    }
}
