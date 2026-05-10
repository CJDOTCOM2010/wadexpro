<?php

namespace App\Modules\Payments\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'user_usage_limit',
        'is_active'
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
        'value'      => 'decimal:2',
        'min_spend'  => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function usages()
    {
        return $this->hasMany(PromoUsage::class);
    }
}
