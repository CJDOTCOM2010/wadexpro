<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\Region;
use App\Modules\Admin\Models\LandingPageSection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    /**
     * List all regions with their details.
     */
    public function getRegions(): JsonResponse
    {
        return response()->json(Region::all());
    }

    /**
     * Create or update a region.
     */
    public function upsertRegion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'sometimes|exists:regions,id',
            'code' => 'required|string|size:2',
            'name' => 'required|string',
            'currency_code' => 'required|string|size:3',
            'language_default_code' => 'required|string|size:2',
            'flag_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $region = Region::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        return response()->json($region);
    }

    /**
     * Get all sections for a specific region and language.
     */
    public function getSections(Request $request): JsonResponse
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'lang_code' => 'nullable|string|size:2',
        ]);

        $query = LandingPageSection::where('region_id', $request->region_id);
        
        if ($request->lang_code) {
            $query->where('lang_code', $request->lang_code);
        }

        return response()->json($query->orderBy('sort_order')->get());
    }

    /**
     * Update or create a content section.
     */
    public function upsertSection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'sometimes|exists:landing_page_sections,id',
            'region_id' => 'required|exists:regions,id',
            'lang_code' => 'required|string|size:2',
            'section_key' => 'required|string',
            'content' => 'required|array',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ]);

        $section = LandingPageSection::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            $validated
        );

        return response()->json($section);
    }

    /**
     * Public API for Landing Page to fetch content.
     */
    public function getPublicContent(Request $request): JsonResponse
    {
        $request->validate([
            'region' => 'required|string|size:2',
            'lang' => 'required|string|size:2',
        ]);

        $region = Region::where('code', $request->region)->first();

        if (!$region) {
            return response()->json(['error' => 'Region not found'], 404);
        }

        $sections = LandingPageSection::where('region_id', $region->id)
            ->where('lang_code', $request->lang)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'region' => $region,
            'sections' => $sections->groupBy('section_key')->map->first()
        ]);
    }
}
