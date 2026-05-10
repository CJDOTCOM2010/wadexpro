<?php

namespace App\Modules\Payments\Controllers;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\PaymentMethod;
use App\Modules\Payments\Models\Promotion;
use App\Modules\Payments\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletManagerController extends Controller
{
    use ApiResponse;

    /**
     * Get the consolidated Wallet Hub state.
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        $referral = Referral::where('inviter_id', $user->id)->count();

        return $this->success([
            'balance'         => (float) ($wallet->balance ?? 0),
            'currency'        => $wallet->currency ?? 'GHS',
            'payment_methods' => $paymentMethods,
            'referral_count'  => $referral,
            'referral_code'   => $user->referral_code,
        ], 'Wallet state retrieved.');
    }

    /**
     * Add a new payment method (Card/MoMo).
     */
    public function addPaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'provider'    => 'required|in:CARD,MOMO',
            'provider_id' => 'required|string', // Masked id or phone
            'gateway_token' => 'required|string',
            'brand'       => 'nullable|string',
            'last_four'   => 'nullable|string|size:4',
            'is_default'  => 'boolean'
        ]);

        $user = Auth::user();

        // If this is default, unset other defaults
        if ($validated['is_default'] ?? false) {
            PaymentMethod::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $method = PaymentMethod::create(array_merge($validated, ['user_id' => $user->id]));

        return $this->success($method, 'Payment method added.', 201);
    }

    /**
     * Validate and apply a promotional code.
     */
    public function checkPromo(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $promo = Promotion::where('code', $request->code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return $this->error('Invalid or expired promotion code.', 404);
        }

        // Logic for usage limits could go here

        return $this->success($promo, 'Promotion is valid.');
    }

    /**
     * Fetch referral history and rewards.
     */
    public function referrals()
    {
        $referrals = Referral::where('inviter_id', Auth::id())
            ->with('referee:id,name')
            ->latest()
            ->get();

        return $this->success($referrals, 'Referral history retrieved.');
    }
}
