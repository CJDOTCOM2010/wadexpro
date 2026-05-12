<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Traits\HasUuid;

class Banner extends Model
{
    use HasUuid;

    protected $table = 'banners';

    protected $fillable = [
        'title', 'image_url', 'link_url', 'link_target',
        'placement', 'audience', 'is_active',
        'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: currently live banners for a given placement & audience.
     */
    public function scopeLive($query, string $placement = 'home', string $audience = 'customer')
    {
        return $query->where('is_active', true)
            ->where('placement', $placement)
            ->where(function ($q) use ($audience) {
                $q->where('audience', 'all')
                  ->orWhere('audience', $audience);
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order');
    }
}
