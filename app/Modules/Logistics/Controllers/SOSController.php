<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\SystemAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SOSController extends Controller
{
    public function __construct(protected \App\Modules\Logistics\Services\SosService $sosService) {}

    /**
     * Display a listing of active SOS events.
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->sosService->getActiveEvents();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('SOS Service unavailable: ' . $e->getMessage());
            $data = [];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Trigger an SOS event (for testing/mobile simulation).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $result = $this->sosService->trigger(
            $request->user()->id,
            $request->lat,
            $request->lng,
            $request->ride_request_id
        );

        return response()->json($result);
    }

    /**
     * Update SOS status (Acknowledge/Resolve).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $status = $request->status; // 'acknowledged', 'resolved', 'false_alarm'

        if ($status === 'acknowledged') {
            $this->sosService->acknowledge($id, $request->user()->id);
        } elseif (in_array($status, ['resolved', 'false_alarm'])) {
            $this->sosService->resolve(
                $id,
                $request->user()->id,
                $request->string('notes', ''),
                $status === 'false_alarm'
            );
        }

        return response()->json(['message' => 'SOS event updated.']);
    }
}
