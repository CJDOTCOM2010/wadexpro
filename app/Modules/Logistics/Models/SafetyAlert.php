<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SafetyAlert extends Model
{
    use HasUuid;

    protected $fillable = [
        'ride_id',
        'type',
        'severity',
        'metadata',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_notes'
    ];

    protected $casts = [
        'metadata' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function ride()
    {
        return $this->belongsTo(RideRequest::class, 'ride_id');
    }

    public function resolver()
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }
}
