<?php

namespace App\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'user_type',        // 'customer', 'driver', 'admin'
        'title',
        'body',
        'data',             // JSON payload for deep linking
        'channel',          // 'fcm', 'apns', 'web_push'
        'device_token',
        'topic',            // 'all_drivers', 'all_customers', specific topic
        'status',           // 'queued', 'sent', 'delivered', 'failed'
        'failure_reason',
        'sent_at',
        'delivered_at',
        'opened_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'sent_at'      => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at'    => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeSent($query) { return $query->where('status', 'sent'); }
    public function scopeFailed($query) { return $query->where('status', 'failed'); }
    public function scopeUnopened($query) { return $query->whereNull('opened_at'); }
}
