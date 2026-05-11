<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',   // 'customer', 'driver', 'admin'
        'message',
        'attachments',   // JSON array of file URLs
        'is_internal',   // internal admin note, not visible to user
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    public function scopePublic($query) { return $query->where('is_internal', false); }
    public function scopeInternal($query) { return $query->where('is_internal', true); }
}
