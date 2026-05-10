<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Organization;
use App\Modules\Logistics\Models\OrganizationMember;
use App\Modules\Logistics\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    use ApiResponse;

    public function __construct(private OrganizationService $orgService) {}

    /**
     * List all corporate partners.
     */
    public function index(Request $request)
    {
        $orgs = Organization::withCount(['members', 'rideRequests', 'orders'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('tax_id', 'like', "%{$request->search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->success($orgs, 'Organizations retrieved.');
    }

    /**
     * Register a new enterprise partner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'tax_id'         => 'nullable|string|max:100',
            'billing_email'  => 'required|email|max:255',
            'billing_type'   => 'required|in:PREPAID,POSTPAID',
            'credit_limit'   => 'required_if:billing_type,POSTPAID|numeric|min:0',
            'initial_deposit' => 'nullable|numeric|min:0',
        ]);

        $org = Organization::create(array_merge($validated, [
            'slug'    => Str::slug($validated['name']) . '-' . rand(1000, 9999),
            'balance' => $validated['initial_deposit'] ?? 0,
        ]));

        return $this->success($org, 'Organization created successfully.', 201);
    }

    /**
     * Get detailed insight for a specific organization.
     */
    public function show(string $id)
    {
        $org = Organization::with(['members.user'])->findOrFail($id);
        
        // Aggregate performance metrics
        $stats = [
            'total_spend'   => $org->rideRequests()->sum('estimated_price') + $org->orders()->sum('total_amount'),
            'active_rides'  => $org->rideRequests()->where('status', 'in_progress')->count(),
            'total_trips'   => $org->rideRequests()->count() + $org->orders()->count(),
        ];

        return $this->success([
            'organization' => $org,
            'stats'        => $stats
        ], 'Organization details retrieved.');
    }

    /**
     * Batch add members to an organization.
     */
    public function addMembers(Request $request, string $id)
    {
        $org = Organization::findOrFail($id);
        
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role'     => 'required|in:ORG_ADMIN,ORG_MANAGER,ORG_STAFF'
        ]);

        foreach ($validated['user_ids'] as $userId) {
            $this->orgService->addMember($org->id, $userId, $validated['role']);
        }

        return $this->success(null, 'Members sync successfully.');
    }

    /**
     * Update billing limits.
     */
    public function updateBilling(Request $request, string $id)
    {
        $org = Organization::findOrFail($id);
        
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'billing_type' => 'required|in:PREPAID,POSTPAID',
            'is_active'    => 'required|boolean'
        ]);

        $org->update($validated);

        return $this->success($org, 'Billing settings updated.');
    }
}
