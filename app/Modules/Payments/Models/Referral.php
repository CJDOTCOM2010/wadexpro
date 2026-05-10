<?php

namespace App\Modules\Payments\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasUuid;

    protected $fillable = [
        'inviter_id',
        'referee_id',
        'referral_code',
        'status',
        'reward_amount',
        'completed_at'
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'completed_at'  => 'datetime',
    ];

    public function inviter()
    {
        return $this->belongsTo(\App\Models\User::class, 'inviter_id');
    }

    public function referee()
    {
        return $this->belongsTo(\App\Models\User::class, 'referee_id');
    }
}
