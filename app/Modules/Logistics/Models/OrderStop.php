<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStop extends Model
{
    use HasUuid, HasFactory;

    protected $fillable = [
        'order_id', 'sequence', 'address', 'lat', 'lng',
        'contact_name', 'contact_phone', 'stop_type', 'status',
        'notes', 'arrived_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'lat'          => 'decimal:8',
            'lng'          => 'decimal:8',
            'arrived_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** Create a new factory instance for the model. */
    protected static function newFactory()
    {
        return \Database\Factories\OrderStopFactory::new();
    }
}
