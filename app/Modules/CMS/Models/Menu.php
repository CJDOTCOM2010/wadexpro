<?php

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'location', 'alignment', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /* ── Relationships ── */

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /* ── Helpers ── */

    /**
     * Get the full menu tree (top-level items with nested children).
     * Uses eager loading to avoid N+1 queries.
     */
    public function getTree(): array
    {
        $items = $this->allItems()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->buildTree($items);
    }

    private function buildTree($items, $parentId = null): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildTree($items, $item->id);
                $node = $item->toArray();
                $node['children'] = $children;
                $branch[] = $node;
            }
        }

        return $branch;
    }
}
