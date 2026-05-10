<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'type',
        'status',
    ];

    /**
     * The messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * The participants in this conversation.
     */
    public function participants()
    {
        return $this->belongsToMany(\App\Models\User::class, 'chat_participants', 'conversation_id', 'user_id')
                    ->withPivot('last_read_at', 'joined_at');
    }

    /**
     * The ride associated with this conversation.
     */
    public function ride()
    {
        return $this->belongsTo(RideRequest::class, 'order_id', 'uuid');
    }
}
