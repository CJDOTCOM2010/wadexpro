<?php

namespace App\Modules\Payments\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasUuid;

    protected $fillable = ['user_id', 'balance', 'currency', 'is_frozen'];

    protected function casts(): array
    {
        return [
            'balance'   => 'decimal:4',
            'is_frozen' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest();
    }
}
