<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PlatformLedger extends Model
{
    use HasUuid;

    protected $table = 'platform_ledgers';

    protected $fillable = [
        'ride_request_id',
        'transaction_id',
        'type',
        'amount',
        'currency',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'metadata' => 'array',
    ];

    /**
     * Get the ride associated with this ledger entry.
     */
    public function ride()
    {
        return $this->belongsTo(RideRequest::class, 'ride_request_id');
    }
}
