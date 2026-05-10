<?php

namespace App\Modules\CMS\Controllers;

use App\Modules\CMS\Models\Menu;
use App\Modules\CMS\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /* ================================================================
     * PUBLIC ENDPOINTS (cached, no auth)
     * ================================================================ */

    /**
     * Get a full menu tree by slug (used by Blade SSR and mobile apps).
     */
    public function show(string $slug): JsonResponse
    {
        $tree = Cache::remember("menu.{$slug}", 3600, function () use ($slug) {
            $menu = Menu::active()->where('slug', $slug)->first();
            return $menu ? $menu->getTree() : [];
        });

        return response()->json(['data' => $tree]);
    }


    /* ================================================================
     * ADMIN ENDPOINTS (auth + super_admin)
     * ================================================================ */

    /**
     * List all menus.
     */
    public function index(): JsonResponse
    {
        $menus = Menu::withCount('allItems')->orderBy('name')->get();
        return response()->json(['data' => $menus]);
    }

    /**
     * Create a new menu.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'slug'      => 'nullable|string|max:100|unique:menus,slug',
            'location'  => 'required|in:header,footer,mobile',
            'alignment' => 'nullable|in:left,center,right',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $menu = Menu::create($data);

        return response()->json(['data' => $menu, 'message' => 'Menu created.'], 201);
    }

    /**
     * Update a menu.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'slug'      => "sometimes|string|max:100|unique:menus,slug,{$id}",
            'location'  => 'sometimes|in:header,footer,mobile',
            'alignment' => 'sometimes|in:left,center,right',
            'is_active' => 'boolean',
        ]);

        $menu->update($data);
        $this->clearMenuCache($menu->slug);

        return response()->json(['data' => $menu, 'message' => 'Menu updated.']);
    }

    /**
     * Delete a menu (cascades to all items).
     */
    public function destroy(string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $slug = $menu->slug;
        $menu->delete();
        $this->clearMenuCache($slug);

        return response()->json(['message' => 'Menu deleted.']);
    }


    /* ================================================================
     * MENU ITEMS
     * ================================================================ */

    /**
     * Get menu items as a tree.
     */
    public function items(string $menuId): JsonResponse
    {
        $menu = Menu::findOrFail($menuId);
        return response()->json(['data' => $menu->getTree()]);
    }

    /**
     * Create a menu item.
     */
    public function storeItem(Request $request, string $menuId): JsonResponse
    {
        $menu = Menu::findOrFail($menuId);

        $data = $request->validate([
            'label'       => 'required|string|max:100',
            'url'         => 'nullable|string|max:500',
            'parent_id'   => 'nullable|uuid|exists:menu_items,id',
            'icon'        => 'nullable|string|max:200',
            'image_url'   => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'group_label' => 'nullable|string|max:100',
            'type'        => 'required|in:link,group,divider,cta_button',
            'layout'      => 'nullable|in:standard,extended_grid',
            'badge_text'  => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'meta_data'   => 'nullable|array',
            'target'      => 'in:_self,_blank',
            'css_class'   => 'nullable|string|max:200',
            'sort_order'  => 'integer',
            'is_active'   => 'boolean',
        ]);

        $data['menu_id'] = $menu->id;
        $data['sort_order'] = $data['sort_order'] ?? MenuItem::where('menu_id', $menu->id)->max('sort_order') + 1;

        $item = MenuItem::create($data);
        $this->clearMenuCache($menu->slug);

        return response()->json(['data' => $item, 'message' => 'Menu item created.'], 201);
    }

    /**
     * Update a menu item.
     */
    public function updateItem(Request $request, string $itemId): JsonResponse
    {
        $item = MenuItem::findOrFail($itemId);

        $data = $request->validate([
            'label'       => 'sometimes|string|max:100',
            'url'         => 'nullable|string|max:500',
            'parent_id'   => 'nullable|uuid|exists:menu_items,id',
            'icon'        => 'nullable|string|max:200',
            'image_url'   => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'group_label' => 'nullable|string|max:100',
            'type'        => 'sometimes|in:link,group,divider,cta_button',
            'layout'      => 'nullable|in:standard,extended_grid',
            'badge_text'  => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'meta_data'   => 'nullable|array',
            'target'      => 'in:_self,_blank',
            'css_class'   => 'nullable|string|max:200',
            'sort_order'  => 'integer',
            'is_active'   => 'boolean',
        ]);

        $item->update($data);
        $this->clearMenuCache($item->menu->slug);

        return response()->json(['data' => $item, 'message' => 'Menu item updated.']);
    }

    /**
     * Delete a menu item.
     */
    public function destroyItem(string $itemId): JsonResponse
    {
        $item = MenuItem::findOrFail($itemId);
        $slug = $item->menu->slug;
        $item->delete();
        $this->clearMenuCache($slug);

        return response()->json(['message' => 'Menu item deleted.']);
    }

    /**
     * Reorder menu items (drag-and-drop).
     */
    public function reorder(Request $request, string $menuId): JsonResponse
    {
        $menu = Menu::findOrFail($menuId);
        $items = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|uuid|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer',
            'items.*.parent_id'  => 'nullable|uuid',
        ]);

        foreach ($items['items'] as $entry) {
            MenuItem::where('id', $entry['id'])->update([
                'sort_order' => $entry['sort_order'],
                'parent_id'  => $entry['parent_id'] ?? null,
            ]);
        }

        $this->clearMenuCache($menu->slug);

        return response()->json(['message' => 'Menu reordered.']);
    }

    /* ── Cache Helper ── */

    private function clearMenuCache(string $slug): void
    {
        Cache::forget("menu.{$slug}");
    }
}
