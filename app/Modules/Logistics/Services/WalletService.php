<?php

namespace App\Modules\Logistics\Services;

use App\Models\User;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Payments\Services\WalletService as CoreWalletService;

/**
 * Legacy Bridge for Wallet operations.
 * Delegating all logic to the central Payments module.
 */
class WalletService
{
    public function __construct(protected CoreWalletService $coreWalletService)
    {
    }

    public function getWallet(User $user)
    {
        return $this->coreWalletService->getOrCreateWallet($user->id);
    }

    public function credit(User $user, float $amount, string $type, array $metadata = [])
    {
        return $this->coreWalletService->credit($user->id, $amount, $type, null, $metadata['ride_id'] ?? null);
    }

    public function debit(User $user, float $amount, string $type, array $metadata = [])
    {
        return $this->coreWalletService->debit($user->id, $amount, $type, null, $metadata['ride_id'] ?? null);
    }

    public function settleRide(RideRequest $ride)
    {
        return $this->coreWalletService->settleRide($ride);
    }

    public function getBalance(User $user): float
    {
        return $this->coreWalletService->getBalance($user->id);
    }
}
