<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasUuid;

    protected $fillable = [
        'driver_id', 'plate_number', 'type', 'make', 'model',
        'year', 'max_weight_kg', 'color', 'insurance_number',
        'insurance_expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'           => 'boolean',
            'insurance_expires_at' => 'date',
            'max_weight_kg'       => 'decimal:2',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
