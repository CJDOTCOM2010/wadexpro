<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class HealthCheckController extends Controller
{
    /**
     * Infrastructure health diagnostic.
     * Returns 200 if all core dependencies are healthy.
     */
    public function check()
    {
        $status = [
            'app' => 'UP',
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'timestamp' => now()->toISOString(),
            'version' => '1.5.0-PROD'
        ];

        $isHealthy = collect($status)->every(fn($v) => $v === 'UP' || $v === $status['timestamp'] || $v === $status['version']);

        return response()->json($status, $isHealthy ? 200 : 503);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            return 'UP';
        } catch (\Exception $e) {
            return 'DOWN';
        }
    }

    private function checkCache(): string
    {
        try {
            Cache::put('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok' ? 'UP' : 'DOWN';
        } catch (\Exception $e) {
            return 'DOWN';
        }
    }
}
