<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SplashScreen extends Model
{
    use HasUuid;

    protected $fillable = [
        'app_type',
        'tagline',
        'app_name',
        'logo_url',
        'logo_media_type',
        'background_url',
        'background_media_type',
        'bg_color',
        'secondary_color',
        'duration_ms',
        'show_ripple',
        'show_logo',
        'show_background',
        'show_tagline',
        'show_app_name',
        'show_bg_color',
        'bg_color_opacity',
        'show_accent_color',
        'accent_color_opacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'show_ripple' => 'boolean',
            'show_logo' => 'boolean',
            'show_background' => 'boolean',
            'show_tagline' => 'boolean',
            'show_app_name' => 'boolean',
            'show_bg_color' => 'boolean',
            'bg_color_opacity' => 'float',
            'show_accent_color' => 'boolean',
            'accent_color_opacity' => 'float',
            'is_active' => 'boolean',
            'duration_ms' => 'integer',
        ];
    }
}
