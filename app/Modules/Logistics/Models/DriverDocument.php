<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'driver_id',
        'document_type',    // 'national_id', 'drivers_license', 'vehicle_insurance', 'roadworthy', 'profile_photo', 'police_clearance'
        'document_number',
        'file_url',
        'back_file_url',
        'status',           // 'pending', 'approved', 'rejected', 'expired'
        'rejection_reason',
        'issued_at',
        'expires_at',
        'reviewed_by',
        'reviewed_at',
        'metadata',
    ];

    protected $casts = [
        'issued_at'   => 'date',
        'expires_at'  => 'date',
        'reviewed_at' => 'datetime',
        'metadata'    => 'array',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function approve(string $reviewerId): void
    {
        $this->update([
            'status'      => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(string $reason, string $reviewerId): void
    {
        $this->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $reviewerId,
            'reviewed_at'      => now(),
        ]);
    }
}
