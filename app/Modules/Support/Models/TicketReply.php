<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\HasUuid;
use App\Models\User;

class TicketReply extends Model
{
    use HasUuid;

    protected $table = 'ticket_replies';

    protected $fillable = [
        'ticket_id', 'sender_id', 'sender_type',
        'message', 'attachments', 'is_internal',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
