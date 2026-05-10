<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OnboardingSlide extends Model
{
    use HasUuid;

    protected $fillable = [
        'app_type',
        'title',
        'description',
        'image_url',
        'media_type',
        'bg_color',
        'text_color',
        'button_color',
        'layout_style',
        'button_text',
        'button_type',
        'sort_order',
        'is_active',
    ];

    /**
     * Available button styles for onboarding slides.
     */
    public const BUTTON_TYPES = [
        'action_below_text' => 'Primary Action (Below Text)',
        'bottom_arrow'      => 'Standard Bottom Arrow',
        'none'              => 'No Direct Button (Gestalt)',
    ];

    /**
     * Available layout styles for onboarding slides.
     */
    public const LAYOUT_STYLES = [
        'full_bleed'     => 'Full Bleed',
        'top_image'      => 'Top Image Segment',
        'bottom_image'   => 'Bottom Image Segment',
        'floating_card'  => 'Floating Card',
        'centered_mini'  => 'Centered Minimalist',
        'dark_premium'   => 'Dark Premium',
        'glassmorphic'   => 'Glassmorphic Bloom',
        'industrial'     => 'Industrial Bold',
        'side_by_side'   => 'Side-by-Side Modern',
        'clean_vector'   => 'Clean Vector',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForApp($query, string $appType)
    {
        return $query->where('app_type', $appType);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
