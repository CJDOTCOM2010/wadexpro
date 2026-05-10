<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageSection extends Model
{
    protected $fillable = [
        'region_id',
        'lang_code',
        'section_key',
        'content',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'content' => 'json',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the region this section belongs to.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
