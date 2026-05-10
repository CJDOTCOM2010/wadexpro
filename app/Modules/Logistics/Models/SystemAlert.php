<?php

namespace App\Modules\Logistics\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAlert extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'severity',
        'message',
        'metadata',
        'user_id',
        'ride_id',
        'is_resolved',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
