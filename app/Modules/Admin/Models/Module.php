<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a toggleable system module.
 * The Super Admin controls enabled/disabled state from the dashboard.
 */
class Module extends Model
{
    use HasUuid;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_enabled',
        'version',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'config'     => 'array',
        ];
    }
}
