<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'user_type',         // 'customer', 'driver'
        'order_id',
        'subject',
        'category',          // 'ride_dispute', 'payment_issue', 'account_problem', 'driver_complaint', 'refund_request', 'general'
        'priority',          // 'low', 'medium', 'high', 'critical'
        'status',            // 'open', 'in_progress', 'waiting_customer', 'resolved', 'closed'
        'assigned_to',       // admin user UUID
        'resolved_by',
        'resolved_at',
        'closed_at',
        'first_response_at',
        'satisfaction_rating', // 1-5 after resolution
        'internal_notes',
    ];

    protected $casts = [
        'resolved_at'          => 'datetime',
        'closed_at'            => 'datetime',
        'first_response_at'    => 'datetime',
        'satisfaction_rating'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function scopeOpen($query) { return $query->where('status', 'open'); }
    public function scopeUnassigned($query) { return $query->whereNull('assigned_to'); }
    public function scopeUrgent($query) { return $query->whereIn('priority', ['high', 'critical']); }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'critical' => 'bg-red-600 text-white animate-pulse',
            'high'     => 'bg-red-100 text-red-700',
            'medium'   => 'bg-amber-100 text-amber-700',
            default    => 'bg-gray-100 text-gray-600',
        };
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->ticket_number = 'TKT-' . strtoupper(substr(uniqid(), -6));
        });
    }
}
