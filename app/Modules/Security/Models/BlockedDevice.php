<?php

namespace App\Modules\Security\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BlockedDevice extends Model
{
    use HasUuids;

    protected $fillable = [
        'device_fingerprint',
        'device_type',       // 'android', 'ios', 'web'
        'user_id',
        'blocked_by',        // admin user UUID
        'reason',
        'notes',
        'blocked_at',
        'expires_at',        // null = permanent
        'is_active',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function blockedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'blocked_by');
    }

    public function isPermanent(): bool { return is_null($this->expires_at); }
    public function isExpired(): bool { return $this->expires_at && $this->expires_at->isPast(); }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public static function isBlocked(string $fingerprint): bool
    {
        return static::active()->where('device_fingerprint', $fingerprint)->exists();
    }
}
