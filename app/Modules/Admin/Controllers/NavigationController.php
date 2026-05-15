<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\AdminNavigation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NavigationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $section = $request->get('section');

        if ($section) {
            $items = AdminNavigation::getBySection($section);
        } else {
            $items = AdminNavigation::getAllVisible();
        }

        return $this->success($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'route' => 'required|string|max:200',
            'icon' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_visible' => 'nullable|boolean',
            'permission' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
        ]);

        $item = AdminNavigation::create($validated);

        return $this->success($item, 'Navigation item created.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = AdminNavigation::findOrFail($id);

        $validated = $request->validate([
            'section' => 'sometimes|string|max:100',
            'label' => 'sometimes|string|max:100',
            'route' => 'sometimes|string|max:200',
            'icon' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_visible' => 'nullable|boolean',
            'permission' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:50',
        ]);

        $item->update($validated);

        return $this->success($item, 'Navigation item updated.');
    }

    public function destroy(string $id): JsonResponse
    {
        $item = AdminNavigation::findOrFail($id);
        $item->delete();

        return $this->success(null, 'Navigation item deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:admin_navigations,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            AdminNavigation::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return $this->success(AdminNavigation::orderBy('sort_order')->get(), 'Navigation reordered.');
    }

    public function toggleVisibility(Request $request, string $id): JsonResponse
    {
        $item = AdminNavigation::findOrFail($id);
        $item->update(['is_visible' => ! $item->is_visible]);

        return $this->success($item, $item->is_visible ? 'Navigation item visible.' : 'Navigation item hidden.');
    }

    public function seed(): JsonResponse
    {
        AdminNavigation::seedDefaults();

        return $this->success(AdminNavigation::orderBy('section', 'asc')->orderBy('sort_order')->get(), 'Navigation seeded with defaults.');
    }
}
