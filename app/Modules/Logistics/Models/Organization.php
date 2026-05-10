<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'tax_id',
        'billing_email',
        'billing_address',
        'balance',
        'credit_limit',
        'billing_type',
        'billing_cycle',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'balance'      => 'decimal:4',
        'credit_limit' => 'decimal:4',
        'is_active'    => 'boolean',
        'settings'     => 'array',
    ];

    public function members()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function rideRequests()
    {
        return $this->hasMany(RideRequest::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
