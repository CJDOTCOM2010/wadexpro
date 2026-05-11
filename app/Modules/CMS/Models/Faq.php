<?php

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasUuids;

    protected $fillable = [
        'question',
        'answer',
        'category',       // 'general', 'riders', 'drivers', 'payments', 'safety'
        'audience',       // 'all', 'customer', 'driver'
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query) { return $query->where('is_active', true)->orderBy('sort_order'); }
    public function scopeForAudience($query, string $audience) { return $query->whereIn('audience', ['all', $audience]); }
    public function scopeByCategory($query, string $cat) { return $query->where('category', $cat); }
}
