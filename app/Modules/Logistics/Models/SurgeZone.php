<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurgeZone extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'center_lat',
        'center_lng',
        'radius_km',
        'min_multiplier',
        'max_multiplier',
        'current_multiplier',
        'is_active',
    ];

    protected $casts = [
        'center_lat'         => 'float',
        'center_lng'         => 'float',
        'radius_km'          => 'float',
        'min_multiplier'     => 'float',
        'max_multiplier'     => 'float',
        'current_multiplier' => 'float',
        'is_active'          => 'boolean',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(SurgeRule::class);
    }
}
