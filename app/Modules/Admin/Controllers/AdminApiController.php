<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminLogService;
use App\Modules\Admin\Services\ModuleManagementService;
use App\Modules\Admin\Services\SystemSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminApiController extends Controller
{
    public function __construct(
        private AdminLogService $logService,
        private SystemSettingsService $settingsService,
        private ModuleManagementService $moduleService
    ) {}

    // -------------------------------------------------------------------------
    // Dashboard Overview
    // -------------------------------------------------------------------------

    /**
     * Get dashboard overview metrics.
     */
    public function dashboardOverview(): JsonResponse
    {
        $totalUsers = DB::table('users')->where('user_type', 'customer')->count();
        $totalDrivers = DB::table('drivers')->count();
        $activeDrivers = DB::table('drivers')->where('is_online', true)->count();
        $totalRides = DB::table('ride_requests')->count();
        $completedRides = DB::table('ride_requests')->where('status', 'completed')->count();
        $pendingRides = DB::table('ride_requests')->whereIn('status', ['pending', 'searching'])->count();
        $activeRides = DB::table('ride_requests')->whereIn('status', ['driver_assigned', 'driver_arrived', 'in_progress'])->count();

        $todayRevenue = DB::table('transactions')
            ->where('status', 'completed')
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $monthRevenue = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $pendingDriverApprovals = DB::table('drivers')->where('status', 'pending_verification')->count();

        $activeSosEvents = DB::table('sos_events')
            ->whereIn('status', ['triggered', 'acknowledged'])
            ->count();

        return response()->json([
            'data' => [
                'users' => [
                    'total_customers'  => $totalUsers,
                    'total_drivers'    => $totalDrivers,
                    'active_drivers'   => $activeDrivers,
                ],
                'rides' => [
                    'total'     => $totalRides,
                    'completed' => $completedRides,
                    'pending'   => $pendingRides,
                    'active'    => $activeRides,
                ],
                'revenue' => [
                    'today'  => round((float) $todayRevenue, 2),
                    'month'  => round((float) $monthRevenue, 2),
                    'currency' => 'GHS',
                ],
                'alerts' => [
                    'pending_driver_approvals' => $pendingDriverApprovals,
                    'active_sos_events'        => $activeSosEvents,
                ],
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Module Management
    // -------------------------------------------------------------------------

    /**
     * List all system modules.
     */
    public function listModules(): JsonResponse
    {
        return response()->json(['data' => $this->moduleService->getAllModules()]);
    }

    /**
     * Toggle a module's enabled state.
     */
    public function toggleModule(Request $request, string $slug): JsonResponse
    {
        $result = $this->moduleService->toggleModule($slug);

        if ($result['success']) {
            $this->logService->log(
                $request->user()->id,
                'module_toggled',
                'module',
                $slug,
                $result['message']
            );
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    // -------------------------------------------------------------------------
    // System Settings
    // -------------------------------------------------------------------------

    /**
     * Get all settings grouped.
     */
    public function getSettings(): JsonResponse
    {
        return response()->json(['data' => $this->settingsService->getAllGrouped()]);
    }

    /**
     * Update settings for a group.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group'    => 'required|string|max:50',
            'settings' => 'required|array',
        ]);

        $oldValues = $this->settingsService->getGroup($validated['group']);
        $this->settingsService->setGroup($validated['group'], $validated['settings']);

        $this->logService->log(
            $request->user()->id,
            'settings_updated',
            'system_settings',
            null,
            "Updated settings group: {$validated['group']}",
            $oldValues,
            $validated['settings']
        );

        return response()->json(['message' => 'Settings updated successfully.']);
    }

    // -------------------------------------------------------------------------
    // User Management
    // -------------------------------------------------------------------------

    /**
     * List all users with filtering.
     */
    public function listUsers(Request $request): JsonResponse
    {
        $query = DB::table('users')
            ->select('id', 'name', 'email', 'phone', 'user_type', 'is_active', 'is_verified', 'created_at', 'last_login_at')
            ->orderByDesc('created_at');

        if ($type = $request->query('type')) {
            $query->where('user_type', $type);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        if ($request->query('active') !== null) {
            $query->where('is_active', filter_var($request->query('active'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->paginate($request->query('per_page', 25)));
    }

    /**
     * Get user details.
     */
    public function showUser(string $id): JsonResponse
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Get related data
        $driver = DB::table('drivers')->where('user_id', $id)->first();
        $wallet = DB::table('wallets')->where('user_id', $id)->first();
        $recentRides = DB::table('ride_requests')
            ->where('customer_id', $id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'user'         => $user,
                'driver'       => $driver,
                'wallet'       => $wallet,
                'recent_rides' => $recentRides,
            ],
        ]);
    }

    /**
     * Activate/deactivate a user.
     */
    public function toggleUserStatus(Request $request, string $id): JsonResponse
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $newStatus = !$user->is_active;

        DB::table('users')->where('id', $id)->update([
            'is_active'  => $newStatus,
            'updated_at' => now(),
        ]);

        $this->logService->log(
            $request->user()->id,
            $newStatus ? 'user_activated' : 'user_deactivated',
            'user',
            $id,
            ($newStatus ? 'Activated' : 'Deactivated') . " user: {$user->name}"
        );

        return response()->json([
            'message' => $newStatus ? 'User activated.' : 'User deactivated.',
            'is_active' => $newStatus,
        ]);
    }

    // -------------------------------------------------------------------------
    // Driver Management
    // -------------------------------------------------------------------------

    /**
     * List drivers with filtering.
     */
    public function listDrivers(Request $request): JsonResponse
    {
        $query = DB::table('drivers')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->select(
                'drivers.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'users.avatar_url'
            )
            ->orderByDesc('drivers.created_at');

        if ($status = $request->query('status')) {
            $query->where('drivers.status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'ilike', "%{$search}%")
                  ->orWhere('users.email', 'ilike', "%{$search}%")
                  ->orWhere('users.phone', 'ilike', "%{$search}%")
                  ->orWhere('drivers.license_number', 'ilike', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->query('per_page', 25)));
    }

    /**
     * Approve a driver.
     */
    public function approveDriver(Request $request, string $id): JsonResponse
    {
        $driver = DB::table('drivers')->where('id', $id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        DB::table('drivers')->where('id', $id)->update([
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $this->logService->log(
            $request->user()->id,
            'driver_approved',
            'driver',
            $id,
            'Driver approved for active duty.'
        );

        return response()->json(['message' => 'Driver approved successfully.']);
    }

    /**
     * Suspend a driver.
     */
    public function suspendDriver(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::table('drivers')->where('id', $id)->update([
            'status'     => 'suspended',
            'is_online'  => false,
            'is_available' => false,
            'updated_at' => now(),
        ]);

        $this->logService->log(
            $request->user()->id,
            'driver_suspended',
            'driver',
            $id,
            "Driver suspended. Reason: {$validated['reason']}"
        );

        return response()->json(['message' => 'Driver suspended.']);
    }

    // -------------------------------------------------------------------------
    // Activity Logs
    // -------------------------------------------------------------------------

    /**
     * Get admin activity logs.
     */
    public function activityLogs(Request $request): JsonResponse
    {
        $filters = [
            'user_id'       => $request->query('user_id'),
            'action'        => $request->query('action'),
            'resource_type' => $request->query('resource_type'),
            'from'          => $request->query('from'),
            'to'            => $request->query('to'),
        ];

        $logs = $this->logService->getLogs(
            array_filter($filters),
            (int) $request->query('per_page', 25)
        );

        return response()->json($logs);
    }

    // -------------------------------------------------------------------------
    // Ride Management
    // -------------------------------------------------------------------------

    /**
     * List all rides with details.
     */
    public function listRides(Request $request): JsonResponse
    {
        $query = DB::table('ride_requests')
            ->leftJoin('users as customers', 'ride_requests.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'ride_requests.driver_id', '=', 'drivers.id')
            ->leftJoin('users as driver_users', 'drivers.user_id', '=', 'driver_users.id')
            ->select(
                'ride_requests.*',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                'driver_users.name as driver_name',
                'driver_users.phone as driver_phone'
            )
            ->orderByDesc('ride_requests.created_at');

        if ($status = $request->query('status')) {
            $query->where('ride_requests.status', $status);
        }

        if ($date = $request->query('date')) {
            $query->whereDate('ride_requests.created_at', $date);
        }

        return response()->json($query->paginate($request->query('per_page', 25)));
    }

    /**
     * Get ride details.
     */
    public function showRide(string $id): JsonResponse
    {
        $ride = DB::table('ride_requests')
            ->leftJoin('users as customers', 'ride_requests.customer_id', '=', 'customers.id')
            ->leftJoin('drivers', 'ride_requests.driver_id', '=', 'drivers.id')
            ->leftJoin('users as driver_users', 'drivers.user_id', '=', 'driver_users.id')
            ->select(
                'ride_requests.*',
                'customers.name as customer_name',
                'customers.email as customer_email',
                'customers.phone as customer_phone',
                'driver_users.name as driver_name',
                'driver_users.phone as driver_phone',
                'drivers.rating as driver_rating'
            )
            ->where('ride_requests.id', $id)
            ->first();

        if (!$ride) {
            return response()->json(['message' => 'Ride not found.'], 404);
        }

        return response()->json(['data' => $ride]);
    }
}
