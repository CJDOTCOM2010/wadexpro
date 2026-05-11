<?php

namespace App\Modules\Security\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FraudDetection extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'user_type',         // 'customer', 'driver'
        'event_type',        // 'multiple_accounts', 'abnormal_location', 'payment_dispute', 'promo_abuse', 'fake_gps', 'unusual_cancellation_rate'
        'risk_score',        // 0-100
        'risk_level',        // 'low', 'medium', 'high', 'critical'
        'details',           // JSON evidence
        'ip_address',
        'device_fingerprint',
        'order_id',
        'status',            // 'open', 'under_review', 'resolved', 'false_positive'
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'auto_flagged',
    ];

    protected $casts = [
        'details'      => 'array',
        'risk_score'   => 'integer',
        'resolved_at'  => 'datetime',
        'auto_flagged' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }

    public function scopeOpen($query) { return $query->where('status', 'open'); }
    public function scopeHighRisk($query) { return $query->whereIn('risk_level', ['high', 'critical']); }
    public function scopeUnresolved($query) { return $query->whereNull('resolved_at'); }

    public function getRiskBadgeAttribute(): string
    {
        return match($this->risk_level) {
            'critical' => 'bg-red-600 text-white',
            'high'     => 'bg-red-100 text-red-700',
            'medium'   => 'bg-amber-100 text-amber-700',
            default    => 'bg-gray-100 text-gray-600',
        };
    }
}
