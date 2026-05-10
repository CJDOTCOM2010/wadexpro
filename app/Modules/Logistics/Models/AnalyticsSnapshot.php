<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSnapshot extends Model
{
    use HasUuid;

    protected $fillable = [
        'metric_name',
        'metric_value',
        'dimension_type',
        'dimension_id',
        'period',
        'start_at',
        'metadata'
    ];

    protected $casts = [
        'metric_value' => 'float',
        'start_at'     => 'datetime',
        'metadata'     => 'array',
    ];
}
