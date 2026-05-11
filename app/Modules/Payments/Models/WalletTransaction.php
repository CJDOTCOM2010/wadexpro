<?php

namespace App\Modules\Payments\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',             // 'credit', 'debit'
        'category',         // 'top_up', 'ride_payment', 'payout', 'refund', 'bonus', 'penalty', 'referral_reward'
        'amount',
        'balance_before',
        'balance_after',
        'reference',        // unique transaction ref
        'description',
        'metadata',
        'status',           // 'completed', 'pending', 'failed', 'reversed'
        'performed_by',     // admin user UUID (if admin-initiated)
        'transacted_at',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
        'metadata'       => 'array',
        'transacted_at'  => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeCredits($query) { return $query->where('type', 'credit'); }
    public function scopeDebits($query) { return $query->where('type', 'debit'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->transacted_at = $model->transacted_at ?? now();
            if (!$model->reference) {
                $model->reference = 'WTX-' . strtoupper(uniqid());
            }
        });
    }
}
