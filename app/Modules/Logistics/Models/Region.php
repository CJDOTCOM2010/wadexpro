<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'slug',
        'currency_code',
        'currency_symbol',
        'tax_percentage',
        'timezone',
        'boundary', // JSON Polygon
        'is_active'
    ];

    protected $casts = [
        'boundary' => 'array',
        'is_active' => 'boolean',
        'tax_percentage' => 'decimal:2'
    ];

    /**
     * Get the rates for this region.
     */
    public function rates()
    {
        return $this->hasMany(RegionRate::class);
    }

    /**
     * Get the users in this region.
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
}
