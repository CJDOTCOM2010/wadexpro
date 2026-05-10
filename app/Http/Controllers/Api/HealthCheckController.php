<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthCheckController extends Controller
{
    /**
     * Check the operational status of critical platform dependencies.
     */
    public function ping(): JsonResponse
    {
        $health = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
            ]
        ];

        // If any core service is down, respond with 503 Service Unavailable
        $statusCode = 200;
        foreach ($health['services'] as $service) {
            if ($service['status'] !== 'ok') {
                $health['status'] = 'degraded';
                $statusCode = 503;
                break;
            }
        }

        return response()->json($health, $statusCode);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'latency_ms' => $this->measure(fn() => DB::select('SELECT 1'))];
        } catch (\Exception $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            return ['status' => 'ok', 'latency_ms' => $this->measure(fn() => Redis::ping())];
        } catch (\Exception $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    private function measure(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        return round((microtime(true) - $start) * 1000, 2);
    }
}
