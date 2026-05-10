<?php

namespace App\Modules\Logistics\Services;

use App\Models\User;
use App\Modules\Logistics\Models\PromoCode;
use App\Modules\Logistics\Models\PromoCodeUse;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class PromoCodeService
{
    /**
     * Validate a promo code for a specific user and order amount.
     *
     * @throws Exception
     */
    public function validate(string $code, User $user, float $amount, ?string $vehicleType = null, ?string $region = null): array
    {
        $promo = PromoCode::where('code', $code)->first();

        if (!$promo) {
            throw new Exception("Invalid promo code.");
        }

        if (!$promo->isValid()) {
            throw new Exception("This promo code has expired or reached its usage limit.");
        }

        if ($amount < $promo->min_order_amount) {
            throw new Exception("Order amount must be at least {$promo->currency} {$promo->min_order_amount} to use this code.");
        }

        // Check user-specific usage limit
        $userUses = PromoCodeUse::where('promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->count();

        if ($promo->max_uses_per_user && $userUses >= $promo->max_uses_per_user) {
            throw new Exception("You have already used this promo code the maximum number of times.");
        }

        // Check vehicle type restrictions
        if ($promo->applicable_vehicle_types && $vehicleType) {
            if (!in_array($vehicleType, $promo->applicable_vehicle_types)) {
                throw new Exception("This promo code is not valid for the selected vehicle type.");
            }
        }

        // Check region restrictions
        if ($promo->applicable_regions && $region) {
            if (!in_array($region, $promo->applicable_regions)) {
                throw new Exception("This promo code is not available in your region.");
            }
        }

        // Calculate discount
        $discountValue = 0;
        if ($promo->type === 'percentage') {
            $discountValue = ($amount * ($promo->value / 100));
            if ($promo->max_discount && $discountValue > $promo->max_discount) {
                $discountValue = $promo->max_discount;
            }
        } else {
            $discountValue = $promo->value;
        }

        // Ensure discount doesn't exceed amount
        if ($discountValue > $amount) {
            $discountValue = $amount;
        }

        return [
            'promo' => $promo,
            'discount_amount' => round($discountValue, 2)
        ];
    }

    /**
     * Apply a promo code to a ride request.
     *
     * @throws Exception
     */
    public function apply(string $code, User $user, RideRequest $ride): float
    {
        return DB::transaction(function () use ($code, $user, $ride) {
            $validation = $this->validate(
                $code, 
                $user, 
                (float) $ride->estimated_price, 
                $ride->vehicle_type,
                $ride->region
            );

            /** @var PromoCode $promo */
            $promo = $validation['promo'];
            $discount = $validation['discount_amount'];

            // Log usage
            PromoCodeUse::create([
                'promo_code_id' => $promo->id,
                'user_id' => $user->id,
                'ride_request_id' => $ride->id,
                'discount_applied' => $discount,
            ]);

            // Increment usage counter
            $promo->increment('times_used');

            return $discount;
        });
    }
}
