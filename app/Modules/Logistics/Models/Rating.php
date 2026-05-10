<?php

namespace App\Modules\Logistics\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasUuid;

    protected $fillable = ['order_id', 'rated_by', 'rated_user', 'score', 'comment'];

    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function rater()
    {
        return $this->belongsTo(\App\Models\User::class, 'rated_by');
    }

    public function ratedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'rated_user');
    }
}
