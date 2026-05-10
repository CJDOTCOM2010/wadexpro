<?php

namespace App\Modules\CMS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CMS\Models\CmsBlock;
use App\Modules\CMS\Models\CmsPage;
use App\Modules\CMS\Models\CmsSection;
use App\Modules\CMS\Services\CmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsSectionController extends Controller
{
    public function __construct(
        private CmsService $cmsService
    ) {}

    /**
     * Get available section types.
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'data' => [
                'section_types' => CmsSection::TYPES,
                'block_types'   => CmsBlock::TYPES,
            ],
        ]);
    }

    /**
     * Add a section to a page.
     */
    public function store(Request $request, string $pageId): JsonResponse
    {
        $page = CmsPage::findOrFail($pageId);

        $validated = $request->validate([
            'type'       => 'required|string|max:50',
            'title'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
            'settings'   => 'nullable|array',
        ]);

        $section = $this->cmsService->createSection($page, $validated);

        return response()->json([
            'message' => 'Section created successfully.',
            'data'    => $section->load('blocks'),
        ], 201);
    }

    /**
     * Update a section.
     */
    public function update(Request $request, string $sectionId): JsonResponse
    {
        $section = CmsSection::findOrFail($sectionId);

        $validated = $request->validate([
            'type'       => 'sometimes|string|max:50',
            'title'      => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
            'settings'   => 'nullable|array',
        ]);

        $section = $this->cmsService->updateSection($section, $validated);

        return response()->json([
            'message' => 'Section updated successfully.',
            'data'    => $section->load('blocks'),
        ]);
    }

    /**
     * Delete a section.
     */
    public function destroy(string $sectionId): JsonResponse
    {
        $section = CmsSection::findOrFail($sectionId);
        $this->cmsService->deleteSection($section);

        return response()->json(['message' => 'Section deleted successfully.']);
    }

    /**
     * Add a block to a section.
     */
    public function storeBlock(Request $request, string $sectionId): JsonResponse
    {
        $section = CmsSection::findOrFail($sectionId);

        $validated = $request->validate([
            'type'       => 'required|string|max:50',
            'key'        => 'nullable|string|max:100',
            'content'    => 'nullable|string',
            'media_url'  => 'nullable|string|max:500',
            'link_url'   => 'nullable|string|max:500',
            'link_text'  => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'properties' => 'nullable|array',
        ]);

        $block = $this->cmsService->createBlock($section, $validated);

        return response()->json([
            'message' => 'Block created successfully.',
            'data'    => $block,
        ], 201);
    }

    /**
     * Update a block.
     */
    public function updateBlock(Request $request, string $blockId): JsonResponse
    {
        $block = CmsBlock::findOrFail($blockId);

        $validated = $request->validate([
            'type'       => 'sometimes|string|max:50',
            'key'        => 'nullable|string|max:100',
            'content'    => 'nullable|string',
            'media_url'  => 'nullable|string|max:500',
            'link_url'   => 'nullable|string|max:500',
            'link_text'  => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'properties' => 'nullable|array',
        ]);

        $block = $this->cmsService->updateBlock($block, $validated);

        return response()->json([
            'message' => 'Block updated successfully.',
            'data'    => $block,
        ]);
    }

    /**
     * Delete a block.
     */
    public function destroyBlock(string $blockId): JsonResponse
    {
        $block = CmsBlock::findOrFail($blockId);
        $this->cmsService->deleteBlock($block);

        return response()->json(['message' => 'Block deleted successfully.']);
    }
}
