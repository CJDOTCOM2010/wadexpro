<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\HasUuid;
use App\Models\User;
use App\Modules\Logistics\Models\Order;

class SupportTicket extends Model
{
    use HasUuid;

    protected $table = 'support_tickets';

    protected $fillable = [
        'ticket_number', 'user_id', 'user_type', 'order_id',
        'subject', 'category', 'priority', 'status',
        'assigned_to', 'resolved_by',
        'resolved_at', 'closed_at', 'first_response_at',
        'satisfaction_rating', 'internal_notes',
    ];

    protected $casts = [
        'resolved_at'       => 'datetime',
        'closed_at'         => 'datetime',
        'first_response_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            $ticket->ticket_number = 'TKT-' . strtoupper(substr(uniqid(), -6));
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high'   => 'amber',
            'low'    => 'green',
            default  => 'blue',
        };
    }
}
