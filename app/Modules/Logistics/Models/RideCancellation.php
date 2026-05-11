<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RideCancellation extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id',
        'cancelled_by',      // user UUID
        'cancelled_by_type', // 'customer', 'driver', 'admin'
        'reason_code',       // 'driver_too_far', 'changed_mind', 'waited_too_long', 'wrong_details', 'admin_override'
        'reason_text',
        'penalty_applied',
        'penalty_amount',
        'penalty_charged_to', // 'customer', 'driver', 'none'
        'cancelled_at',
    ];

    protected $casts = [
        'penalty_applied' => 'boolean',
        'penalty_amount'  => 'decimal:2',
        'cancelled_at'    => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'cancelled_by');
    }

    public function scopeByDriver($query) { return $query->where('cancelled_by_type', 'driver'); }
    public function scopeByCustomer($query) { return $query->where('cancelled_by_type', 'customer'); }
    public function scopeWithPenalty($query) { return $query->where('penalty_applied', true); }
}
