<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Organization;
use App\Modules\Logistics\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrganizationService
{
    /**
     * Invite or add a user to an organization.
     */
    public function addMember(string $orgId, string $userId, string $role = 'ORG_STAFF'): OrganizationMember
    {
        return OrganizationMember::updateOrCreate(
            ['organization_id' => $orgId, 'user_id' => $userId],
            ['role' => $role, 'can_use_org_wallet' => true]
        );
    }

    /**
     * Resolve the billable entity and billing source for a logistics request.
     */
    public function resolveBillingPlan(User $user, bool $requestCorporate = false): array
    {
        if (!$requestCorporate) {
            return ['source' => 'PERSONAL', 'org_id' => null];
        }

        // Check if user belongs to an active organization
        $member = OrganizationMember::where('user_id', $user->id)
            ->where('can_use_org_wallet', true)
            ->with('organization')
            ->first();

        if (!$member || !$member->organization->is_active) {
            return ['source' => 'PERSONAL', 'org_id' => null, 'error' => 'No active corporate account found.'];
        }

        return [
            'source' => 'CORPORATE',
            'org_id' => $member->organization_id,
            'organization' => $member->organization
        ];
    }

    /**
     * Verify if an organization has sufficient funds or credit for a transaction.
     */
    public function canAfford(Organization $org, float $amount): bool
    {
        if ($org->billing_type === 'PREPAID') {
            return $org->balance >= $amount;
        }

        // For Postpaid, check against credit limit
        // Current balance for Postpaid reflects current debt (negative or increasing)
        return ($org->credit_limit - $org->balance) >= $amount;
    }

    /**
     * Process a corporate payment by debiting the organization's balance.
     */
    public function processCorporatePayment(Organization $org, float $amount, string $description): void
    {
        DB::transaction(function () use ($org, $amount, $description) {
            $org->decrement('balance', $amount);
            
            // Log transaction in a corporate_ledger if we had one (Future Enhancement)
            Log::info("Corporate Payment: Org {$org->id} debited {$amount} for: {$description}");
        });
    }
}
