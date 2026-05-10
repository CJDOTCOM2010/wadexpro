<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        $promotions = Promotion::orderByDesc('created_at')->get();
        return response()->json(['data' => $promotions]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'                     => 'required|string|unique:promotions,code',
            'name'                     => 'required|string',
            'description'              => 'nullable|string',
            'type'                     => 'required|in:percentage,fixed',
            'value'                    => 'required|numeric|min:0',
            'min_order_amount'         => 'required|numeric|min:0',
            'max_discount'             => 'nullable|numeric|min:0',
            'starts_at'                => 'nullable|date',
            'expires_at'               => 'nullable|date|after_or_equal:starts_at',
            'max_uses'                 => 'nullable|integer|min:1',
            'is_active'                => 'boolean',
            'applicable_vehicle_types' => 'nullable|array',
        ]);

        $promotion = Promotion::create($validated);
        return response()->json(['message' => 'Promotion created.', 'data' => $promotion], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        
        $validated = $request->validate([
            'code'                     => 'sometimes|string|unique:promotions,code,' . $id,
            'name'                     => 'required|string',
            'description'              => 'nullable|string',
            'type'                     => 'required|in:percentage,fixed',
            'value'                    => 'required|numeric|min:0',
            'min_order_amount'         => 'required|numeric|min:0',
            'max_discount'             => 'nullable|numeric|min:0',
            'starts_at'                => 'nullable|date',
            'expires_at'               => 'nullable|date|after_or_equal:starts_at',
            'max_uses'                 => 'nullable|integer|min:1',
            'is_active'                => 'boolean',
            'applicable_vehicle_types' => 'nullable|array',
        ]);

        $promotion->update($validated);
        return response()->json(['message' => 'Promotion updated.', 'data' => $promotion]);
    }

    public function destroy(string $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Promotion deleted.']);
    }

    public function toggle(string $id): JsonResponse
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => !$promotion->is_active]);
        return response()->json(['message' => 'Promotion status updated.', 'data' => $promotion]);
    }
}
