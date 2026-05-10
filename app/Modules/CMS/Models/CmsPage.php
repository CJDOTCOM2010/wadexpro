<?php

namespace App\Modules\CMS\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends Model
{
    use HasUuid;

    protected $fillable = [
        'title', 'slug', 'meta_description', 'meta_keywords',
        'status', 'template', 'region', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order'       => 'integer',
            'title'            => 'array',
            'meta_description' => 'array',
        ];
    }

    /**
     * Get all sections for this page, ordered by sort_order.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(CmsSection::class, 'page_id')->orderBy('sort_order');
    }

    /**
     * Scope to only published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to filter by region.
     */
    public function scopeForRegion($query, ?string $region = null)
    {
        if ($region) {
            return $query->where(function ($q) use ($region) {
                $q->where('region', $region)->orWhereNull('region');
            });
        }
        return $query->whereNull('region');
    }
}
