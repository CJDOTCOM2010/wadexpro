<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'code',
        'name',
        'currency_code',
        'language_default_code',
        'flag_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all content sections for this region.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(LandingPageSection::class);
    }
}
