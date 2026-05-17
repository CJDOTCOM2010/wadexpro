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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'show_ripple' => 'boolean',
            'show_logo' => 'boolean',
            'show_background' => 'boolean',
            'show_tagline' => 'boolean',
            'is_active' => 'boolean',
            'duration_ms' => 'integer',
        ];
    }
}
