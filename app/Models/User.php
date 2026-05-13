<?php

namespace App\Models;

use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\Transaction;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use \Laravel\Sanctum\HasApiTokens, HasFactory, Notifiable, \Illuminate\Database\Eloquent\Concerns\HasUuids, \App\Core\Traits\HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user_type',
        'referral_code',
        'referred_by_id',
        'fcm_token',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(Str::random(6));
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the user's transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the user's referrals (users they have referred).
     */
    public function referrals()
    {
        return $this->hasMany(\App\Modules\Payments\Models\Referral::class, 'inviter_id');
    }

    /**
     * Get the user who referred this user.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    /**
     * Get the user's driver profile (if they are a driver).
     */
    public function driver()
    {
        return $this->hasOne(\App\Modules\Logistics\Models\Driver::class);
    }

    /**
     * Get the user's staff profile (if they are an employee).
     */
    public function staffProfile()
    {
        return $this->hasOne(\App\Modules\Admin\Models\StaffProfile::class);
    }
}
