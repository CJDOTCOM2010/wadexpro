<?php

namespace App\Modules\Logistics\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyExchangeService
{
    /**
     * Convert an amount between currencies.
     * For MVP, we use fixed rates or a mock provider.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $rates = $this->getRates($from);
        
        if (isset($rates[$to])) {
            return $amount * $rates[$to];
        }

        return $amount; // Fallback to same if conversion fails
    }

    /**
     * Get exchange rates for a base currency.
     */
    public function getRates(string $base): array
    {
        return Cache::remember("fx_rates:{$base}", 3600, function () use ($base) {
            // In production, fetch from Fixer.io or similar
            // Mocking GHS as base
            if ($base === 'GHS') {
                return [
                    'USD' => 0.075,
                    'NGN' => 110.0,
                    'EUR' => 0.069,
                    'GHS' => 1.0
                ];
            }
            
            // Mocking USD as base
            if ($base === 'USD') {
                return [
                    'GHS' => 13.3,
                    'NGN' => 1450.0,
                    'USD' => 1.0
                ];
            }

            return ['USD' => 1.0];
        });
    }
}
