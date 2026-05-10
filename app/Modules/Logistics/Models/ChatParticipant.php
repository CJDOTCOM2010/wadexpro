<?php

namespace App\Modules\Logistics\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    public $timestamps = false;
    protected $table = 'chat_participants';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_at',
        'joined_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'joined_at'    => 'datetime',
    ];

    /**
     * The conversation the participant belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * The user account of the participant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
