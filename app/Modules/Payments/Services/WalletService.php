<?php

namespace App\Modules\Payments\Services;

use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Models\PlatformLedger;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Get or create a wallet for a user.
     */
    public function getOrCreateWallet(string $userId, string $currency = 'GHS'): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance'   => 0.0000,
                'currency'  => $currency,
                'is_frozen' => false,
            ]
        );
    }

    /**
     * Credit a wallet (add funds).
     *
     * @return array{success: bool, balance: float, transaction_id: string}
     */
    public function credit(
        string $userId,
        float $amount,
        string $description,
        ?string $reference = null,
        ?string $relatedTransactionId = null
    ): array {
        return DB::transaction(function () use ($userId, $amount, $description, $reference, $relatedTransactionId) {
            $wallet = $this->getOrCreateWallet($userId);

            if ($wallet->is_frozen) {
                throw new \RuntimeException('Wallet is frozen. Contact support.');
            }

            $wallet->increment('balance', $amount);
            $wallet->refresh();

            $txnId = (string) Str::uuid();

            DB::table('wallet_transactions')->insert([
                'id'                     => $txnId,
                'wallet_id'              => $wallet->id,
                'type'                   => 'credit',
                'amount'                 => $amount,
                'balance_after'          => $wallet->balance,
                'description'            => $description,
                'reference'              => $reference ?? 'WLT-C-' . strtoupper(Str::random(10)),
                'related_transaction_id' => $relatedTransactionId,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            return [
                'success'        => true,
                'balance'        => (float) $wallet->balance,
                'transaction_id' => $txnId,
            ];
        });
    }

    /**
     * Debit a wallet (remove funds).
     *
     * @return array{success: bool, balance: float, transaction_id: string}
     */
    public function debit(
        string $userId,
        float $amount,
        string $description,
        ?string $reference = null,
        ?string $relatedTransactionId = null
    ): array {
        return DB::transaction(function () use ($userId, $amount, $description, $reference, $relatedTransactionId) {
            $wallet = $this->getOrCreateWallet($userId);

            if ($wallet->is_frozen) {
                throw new \RuntimeException('Wallet is frozen. Contact support.');
            }

            if ((float) $wallet->balance < $amount) {
                throw new \RuntimeException('Insufficient wallet balance.');
            }

            $wallet->decrement('balance', $amount);
            $wallet->refresh();

            $txnId = (string) Str::uuid();

            DB::table('wallet_transactions')->insert([
                'id'                     => $txnId,
                'wallet_id'              => $wallet->id,
                'type'                   => 'debit',
                'amount'                 => $amount,
                'balance_after'          => $wallet->balance,
                'description'            => $description,
                'reference'              => $reference ?? 'WLT-D-' . strtoupper(Str::random(10)),
                'related_transaction_id' => $relatedTransactionId,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            return [
                'success'        => true,
                'balance'        => (float) $wallet->balance,
                'transaction_id' => $txnId,
            ];
        });
    }

    /**
     * Get wallet balance.
     */
    public function getBalance(string $userId): float
    {
        $wallet = $this->getOrCreateWallet($userId);
        return (float) $wallet->balance;
    }

    /**
     * Get wallet transaction history.
     */
    public function getTransactionHistory(string $userId, int $limit = 20): \Illuminate\Support\Collection
    {
        $wallet = $this->getOrCreateWallet($userId);

        return DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Freeze a wallet (admin action).
     */
    public function freeze(string $userId): void
    {
        Wallet::where('user_id', $userId)->update(['is_frozen' => true]);
    }

    /**
     * Unfreeze a wallet (admin action).
     */
    public function unfreeze(string $userId): void
    {
        Wallet::where('user_id', $userId)->update(['is_frozen' => false]);
    }

    /**
     * Settle a ride request between customer, driver, and platform.
     */
    public function settleRide(RideRequest $ride): bool
    {
        if ($ride->status !== 'completed' && $ride->status !== 'delivered') {
            throw new \RuntimeException('Only completed rides can be settled.');
        }

        if ($ride->payment_status === 'settled') {
            return true;
        }

        $amount = (float) ($ride->final_price ?? $ride->estimated_price);
        $customer = $ride->customer;
        $driverUser = $ride->driver->user;

        return DB::transaction(function () use ($ride, $amount, $customer, $driverUser) {
            // 1. Debit Customer
            $customerResult = $this->debit(
                $customer->id,
                $amount,
                'Ride payment: ' . $ride->pickup_address . ' to ' . $ride->dropoff_address,
                'WDX-RIDE-PMT-' . $ride->id
            );

            // 2. Calculate Split (Default: 20% commission)
            $commissionRate = 0.20;
            $commissionAmount = $amount * $commissionRate;
            $driverAmount = $amount - $commissionAmount;

            // 3. Credit Driver
            $driverResult = $this->credit(
                $driverUser->id,
                $driverAmount,
                'Ride earnings split: ' . $ride->id,
                'WDX-RIDE-ERN-' . $ride->id
            );

            // 4. Record Platform Commission in Ledger
            PlatformLedger::create([
                'ride_request_id' => $ride->id,
                'transaction_id'  => $customerResult['transaction_id'],
                'type'           => 'commission',
                'amount'         => $commissionAmount,
                'currency'       => 'GHS',
                'description'    => 'Platform commission for Ride ' . $ride->id,
                'metadata'       => [
                    'commission_rate' => $commissionRate,
                    'gross_amount'    => $amount
                ]
            ]);

            // 5. Update Ride Record
            $ride->update([
                'payment_status'    => 'settled',
                'payment_reference' => $customerResult['transaction_id'],
                'commission_amount' => $commissionAmount,
                'driver_earnings'   => $driverAmount,
            ]);

            return true;
        });
    }
}
