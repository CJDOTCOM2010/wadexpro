<?php

namespace App\Modules\CMS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CMS\Models\CmsPage;
use App\Modules\CMS\Models\CmsSection;
use App\Modules\CMS\Services\CmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function __construct(
        private CmsService $cmsService
    ) {}

    /**
     * Public: Get a published page by slug (for landing website).
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $region = $request->query('region');
        $page = $this->cmsService->getPageBySlug($slug, $region);

        if (!$page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return response()->json(['data' => $page]);
    }

    /**
     * Public: List all published pages (for navigation).
     */
    public function navigation(Request $request): JsonResponse
    {
        $region = $request->query('region');
        $pages = $this->cmsService->getPublishedPages($region);

        return response()->json(['data' => $pages]);
    }

    /**
     * Admin: List all pages.
     */
    public function index(): JsonResponse
    {
        $pages = CmsPage::query()
            ->withCount('sections')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $pages]);
    }

    /**
     * Admin: Create a new page.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:cms_pages,slug',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'status'           => 'nullable|in:published,draft,archived',
            'template'         => 'nullable|string|max:50',
            'region'           => 'nullable|string|max:10',
            'sort_order'       => 'nullable|integer',
        ]);

        $page = $this->cmsService->createPage($validated);

        return response()->json([
            'message' => 'Page created successfully.',
            'data' => $page,
        ], 201);
    }

    /**
     * Admin: Get full page with sections and blocks.
     */
    public function edit(string $id): JsonResponse
    {
        $page = CmsPage::with(['sections.blocks'])->findOrFail($id);

        return response()->json(['data' => $page]);
    }

    /**
     * Admin: Update a page.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'slug'             => 'sometimes|string|max:255|unique:cms_pages,slug,' . $page->id,
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'status'           => 'nullable|in:published,draft,archived',
            'template'         => 'nullable|string|max:50',
            'region'           => 'nullable|string|max:10',
            'sort_order'       => 'nullable|integer',
        ]);

        $page = $this->cmsService->updatePage($page, $validated);

        return response()->json([
            'message' => 'Page updated successfully.',
            'data' => $page,
        ]);
    }

    /**
     * Admin: Delete a page.
     */
    public function destroy(string $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);
        $this->cmsService->deletePage($page);

        return response()->json(['message' => 'Page deleted successfully.']);
    }

    /**
     * Admin: Reorder sections within a page.
     */
    public function reorderSections(Request $request, string $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'section_ids'   => 'required|array',
            'section_ids.*' => 'required|string|exists:cms_sections,id',
        ]);

        $this->cmsService->reorderSections($page, $validated['section_ids']);

        return response()->json(['message' => 'Sections reordered successfully.']);
    }
}
