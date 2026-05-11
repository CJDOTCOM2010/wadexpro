<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'vehicle_id',
        'document_type',   // 'roadworthy', 'insurance', 'registration', 'inspection'
        'document_number',
        'file_url',
        'status',          // 'pending', 'approved', 'rejected', 'expired'
        'rejection_reason',
        'issued_at',
        'expires_at',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'issued_at'   => 'date',
        'expires_at'  => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= $days && !$this->expires_at->isPast();
    }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopeExpired($query) { return $query->where('expires_at', '<', now()); }
}
