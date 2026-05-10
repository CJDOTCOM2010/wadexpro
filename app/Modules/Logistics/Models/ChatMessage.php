<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasUuid;

    // The table uses useCurrent() for created_at, but Laravel expects updated_at too.
    // If updated_at is missing in the table, we should handle it.
    public $timestamps = false; 

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'message_type',
        'attachment_url',
        'metadata',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at'  => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * The conversation associated with the message.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * The user who sent the message.
     */
    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }
}
