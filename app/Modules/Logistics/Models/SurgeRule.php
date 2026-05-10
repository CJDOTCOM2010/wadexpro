<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurgeRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'surge_zone_id',
        'name',
        'demand_threshold',
        'supply_threshold',
        'multiplier',
        'is_active',
    ];

    protected $casts = [
        'demand_threshold' => 'integer',
        'supply_threshold' => 'integer',
        'multiplier'       => 'float',
        'is_active'        => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(SurgeZone::class, 'surge_zone_id');
    }
}
