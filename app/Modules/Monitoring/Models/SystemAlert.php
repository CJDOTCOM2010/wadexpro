<?php

namespace App\Modules\Monitoring\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemAlert extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'severity',
        'title',
        'message',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'metadata',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
