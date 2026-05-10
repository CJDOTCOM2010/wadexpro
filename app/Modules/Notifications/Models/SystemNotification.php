<?php

namespace App\Modules\Notifications\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'data',
        'action_url', 'is_read', 'read_at', 'channel', 'is_sent', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data'     => 'array',
            'is_read'  => 'boolean',
            'is_sent'  => 'boolean',
            'read_at'  => 'datetime',
            'sent_at'  => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }
}
