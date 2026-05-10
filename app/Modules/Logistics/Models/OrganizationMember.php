<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OrganizationMember extends Model
{
    use HasUuid;

    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'can_use_org_wallet'
    ];

    protected $casts = [
        'can_use_org_wallet' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope for specific roles
     */
    public function scopeIsAdmin($query)
    {
        return $query->where('role', 'ORG_ADMIN');
    }

    public function scopeIsManager($query)
    {
        return $query->where('role', 'ORG_MANAGER');
    }
}
