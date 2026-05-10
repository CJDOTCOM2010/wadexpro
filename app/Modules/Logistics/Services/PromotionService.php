<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Promotion;
use App\Modules\Logistics\Jobs\ProcessPromotionRedemption;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    /**
     * Validate a promo code and calculate the discount.
     */
    public function applyPromo(string $code, float $rideAmount, string $vehicleType, ?User $user = null): array
    {
        $promo = Promotion::active()->where('code', strtoupper($code))->first();

        if (!$promo) {
            throw ValidationException::withMessages(['promo_code' => 'Invalid or expired promo code.']);
        }

        if ($promo->isLimitReached()) {
            throw ValidationException::withMessages(['promo_code' => 'This promo code has reached its usage limit.']);
        }

        if ($rideAmount < $promo->min_order_amount) {
            throw ValidationException::withMessages(['promo_code' => "Minimum ride amount of {$promo->min_order_amount} required."]);
        }

        if (!empty($promo->applicable_vehicle_types) && !in_array($vehicleType, $promo->applicable_vehicle_types)) {
            throw ValidationException::withMessages(['promo_code' => "This promo is not valid for {$vehicleType} rides."]);
        }

        $discount = 0;
        if ($promo->type === 'percentage') {
            $discount = ($rideAmount * ($promo->value / 100));
            if ($promo->max_discount > 0) {
                $discount = min($discount, $promo->max_discount);
            }
        } else {
            $discount = min($promo->value, $rideAmount);
        }

        return [
            'promo_id'        => $promo->id,
            'code'            => $promo->code,
            'discount_amount' => round($discount, 2),
            'final_price'     => round(max(0, $rideAmount - $discount), 2),
            'type'            => $promo->type,
            'value'           => $promo->value,
        ];
    }

    /**
     * Increment the usage count of a promotion (Asynchronously).
     */
    public function incrementUsage(string $promoId): void
    {
        ProcessPromotionRedemption::dispatch($promoId);
    }
}
