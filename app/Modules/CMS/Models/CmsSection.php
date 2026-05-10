<?php

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsSection extends Model
{
    use HasUuids;

    protected $fillable = [
        'page_id', 'type', 'title', 'sort_order', 'is_visible', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'sort_order'  => 'integer',
            'is_visible'  => 'boolean',
            'settings'    => 'array',
        ];
    }

    /**
     * Available section types for the CMS block editor.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'hero'               => 'Hero Section',
        'services'           => 'Services Section',
        'how_it_works'       => 'How It Works',
        'benefits'           => 'Benefits Section',
        'driver_onboarding'  => 'Driver Onboarding',
        'business'           => 'Business / Enterprise',
        'app_download'       => 'App Download',
        'region_content'     => 'Region Content',
        'testimonials'       => 'Testimonials',
        'stats'              => 'Statistics Counter',
        'faq'                => 'FAQ',
        'cta'                => 'Call to Action',
        'custom'             => 'Custom HTML',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(CmsBlock::class, 'section_id')->orderBy('sort_order');
    }

    /**
     * Scope to only visible sections.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
