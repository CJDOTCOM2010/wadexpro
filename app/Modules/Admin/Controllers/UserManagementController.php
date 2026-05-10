<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Auth\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;

/**
 * Platform-wide user management for Admin and Super Admin.
 */
class UserManagementController extends Controller
{
    use ApiResponse;

    /**
     * GET /v1/admin/users
     * Filterable, paginated user list.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->user_type, fn ($q) => $q->where('user_type', $request->user_type))
            ->when($request->search, fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'ilike', "%{$request->search}%")
                       ->orWhere('email', 'ilike', "%{$request->search}%")
                       ->orWhere('phone', 'like', "%{$request->search}%")
                )
            )
            ->when($request->is_active !== null, fn ($q) =>
                $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->with('roles')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return $this->paginated($users);
    }

    /**
     * GET /v1/admin/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        $user = User::with(['roles.permissions', 'wallet', 'driver', 'employee'])->findOrFail($id);

        return $this->success(new UserResource($user), 'User retrieved.');
    }

    /**
     * PATCH /v1/admin/users/{id}/status
     * Activate or deactivate a user account.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate(['is_active' => ['required', 'boolean']]);

        $user = User::findOrFail($id);

        // Prevent deactivating own account
        if ($user->id === $request->user()->id) {
            return $this->error('You cannot deactivate your own account.', 422);
        }

        $user->update(['is_active' => $request->is_active]);

        // Revoke all tokens if deactivating
        if (!$request->is_active) {
            $user->tokens()->delete();
        }

        return $this->success(
            new UserResource($user),
            'User status updated.'
        );
    }

    /**
     * DELETE /v1/admin/users/{id}
     * Soft delete: anonymise personal data (GDPR right to be forgotten).
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === request()->user()->id) {
            return $this->error('You cannot delete your own account via admin panel.', 422);
        }

        // Revoke all tokens first
        $user->tokens()->delete();

        // Anonymise personal data
        $user->update([
            'name'       => 'Deleted User',
            'email'      => 'deleted_' . $user->id . '@deleted.wadexp.com',
            'phone'      => null,
            'avatar_url' => null,
            'fcm_token'  => null,
            'is_active'  => false,
        ]);

        return $this->success(null, 'User anonymised and deactivated.');
    }
}
