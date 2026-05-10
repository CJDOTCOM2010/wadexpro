<?php

namespace App\Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JournalEntry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reference',
        'description',
        'entry_date',
        'source_type',
        'source_id',
        'is_posted',
        'is_reversed',
        'reversal_of',
        'created_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_posted' => 'boolean',
        'is_reversed' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalLine::class, 'journal_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
