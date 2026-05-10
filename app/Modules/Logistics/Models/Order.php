<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use App\Modules\Monitoring\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasUuid, Auditable, HasFactory;

    protected $fillable = [
        'reference', 'customer_id', 'driver_id', 'status', 'priority',
        'scheduled_at', 'pickup_address', 'pickup_lat', 'pickup_lng',
        'pickup_contact_name', 'pickup_contact_phone', 'package_description',
        'package_weight_kg', 'package_size', 'payment_method', 'payment_status',
        'payment_gateway_ref', 'subtotal', 'delivery_fee', 'tax_amount',
        'discount_amount', 'total_amount', 'currency', 'estimated_duration_seconds',
        'actual_duration_seconds', 'estimated_distance_km', 'actual_distance_km',
        'notes', 'cancellation_reason', 'metadata',
        'assigned_at', 'picked_up_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'pickup_lat'          => 'decimal:8',
            'pickup_lng'          => 'decimal:8',
            'package_weight_kg'   => 'decimal:2',
            'subtotal'            => 'decimal:2',
            'delivery_fee'        => 'decimal:2',
            'tax_amount'          => 'decimal:2',
            'discount_amount'     => 'decimal:2',
            'total_amount'        => 'decimal:2',
            'estimated_distance_km' => 'decimal:3',
            'actual_distance_km'  => 'decimal:3',
            'metadata'            => 'array',
            'scheduled_at'        => 'datetime',
            'assigned_at'         => 'datetime',
            'picked_up_at'        => 'datetime',
            'delivered_at'        => 'datetime',
            'cancelled_at'        => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function stops()
    {
        return $this->hasMany(OrderStop::class)->orderBy('sequence');
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class)->latest('recorded_at');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function transaction()
    {
        return $this->hasOne(\App\Modules\Payments\Models\Transaction::class);
    }

    /** Create a new factory instance for the model. */
    protected static function newFactory()
    {
        return \Database\Factories\OrderFactory::new();
    }

    /** Auto-generate WAD-YYYY-NNNNNN reference */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (!$order->reference) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $order->reference = 'WAD-' . now()->year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
