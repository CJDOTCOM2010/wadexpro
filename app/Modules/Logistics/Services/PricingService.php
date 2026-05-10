<?php

namespace App\Modules\Logistics\Services;

class PricingService
{
    /**
     * Calculate delivery price based on distance and weight.
     * Prices are in the system's base currency (e.g. GHS).
     */
    public function calculate(float $distanceKm, float $weightKg, string $priority = 'standard'): array
    {
        // These base fees would typically come from system settings,
        // but are hardcoded for the scaffold.
        $baseFee = 15.00;
        $perKmRate = 2.50;
        $perKgRate = 1.00; // Applied to weight over 5kg
        $priorityMultiplier = $priority === 'express' ? 1.5 : 1.0;

        $distanceFee = $distanceKm * $perKmRate;
        $weightFee = max(0, $weightKg - 5) * $perKgRate;
        
        $subtotal = ($baseFee + $distanceFee + $weightFee) * $priorityMultiplier;
        
        // 5% Tax rate
        $taxRate = 0.05;
        $taxAmount = $subtotal * $taxRate;
        
        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'delivery_fee' => round($distanceFee + $baseFee, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($total, 2)
        ];
    }

    /**
     * Haversine formula to calculate approximate distance between two lat/lng pairs in kilometers.
     * In production, use OSRM/Google Maps Distance Matrix for routed distance.
     */
    public function calculateStraightLineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
