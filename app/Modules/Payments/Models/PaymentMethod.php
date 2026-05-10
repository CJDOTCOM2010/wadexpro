<?php

namespace App\Modules\Payments\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'gateway_token',
        'brand',
        'last_four',
        'is_default',
        'metadata'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'metadata'   => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
