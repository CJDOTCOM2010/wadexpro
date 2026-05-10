<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PromoCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'currency',
        'max_uses',
        'max_uses_per_user',
        'times_used',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_vehicle_types',
        'applicable_regions',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active'                => 'boolean',
        'starts_at'                => 'datetime',
        'expires_at'               => 'datetime',
        'applicable_vehicle_types' => 'array',
        'applicable_regions'       => 'array',
        'value'                    => 'float',
        'max_discount'             => 'float',
        'min_order_amount'         => 'float',
        'times_used'               => 'integer',
        'max_uses'                 => 'integer',
        'max_uses_per_user'        => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the uses for the promo code.
     */
    public function uses(): HasMany
    {
        return $this->hasMany(PromoCodeUse::class);
    }

    /**
     * Check if the promo code is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        if ($this->max_uses && $this->times_used >= $this->max_uses) return false;
        
        return true;
    }
}
