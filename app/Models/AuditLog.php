<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'user_id',
        'user_type',      // 'admin', 'driver', 'customer'
        'event',          // 'created', 'updated', 'deleted', 'login', 'logout', 'approved', 'rejected', etc.
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'tags',
        'logged_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'tags'       => 'array',
        'logged_at'  => 'datetime',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('logged_at', '>=', now()->subDays($days));
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->logged_at = $model->logged_at ?? now();
        });
    }
}
