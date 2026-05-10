<?php

namespace App\Modules\Payments\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuid;

    protected $fillable = [
        'reference', 'order_id', 'user_id', 'type', 'gateway',
        'gateway_ref', 'amount', 'currency', 'exchange_rate',
        'amount_in_base_currency', 'status', 'failure_reason',
        'metadata', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'                  => 'decimal:4',
            'exchange_rate'           => 'decimal:6',
            'amount_in_base_currency' => 'decimal:4',
            'metadata'                => 'array',
            'processed_at'            => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function order()
    {
        return $this->belongsTo(\App\Modules\Logistics\Models\Order::class);
    }
}
