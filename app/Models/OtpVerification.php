<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerification extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'email',
        'otp_code',
        'type',        // 'login', 'registration', 'password_reset', 'phone_verify'
        'expires_at',
        'verified_at',
        'attempts',
        'ip_address',
        'device_fingerprint',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'attempts'    => 'integer',
    ];

    protected $hidden = ['otp_code'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    public function hasExceededAttempts(int $max = 5): bool
    {
        return $this->attempts >= $max;
    }

    public function scopePending($query)
    {
        return $query->whereNull('verified_at')->where('expires_at', '>', now());
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
