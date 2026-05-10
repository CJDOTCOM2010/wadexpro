<?php

namespace App\Modules\Admin\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminLogService
{
    /**
     * Record an admin activity.
     */
    public function log(
        string $userId,
        string $action,
        ?string $resourceType = null,
        ?string $resourceId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        DB::table('admin_activity_logs')->insert([
            'id'            => (string) Str::uuid(),
            'user_id'       => $userId,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'description'   => $description,
            'old_values'    => $oldValues ? json_encode($oldValues) : null,
            'new_values'    => $newValues ? json_encode($newValues) : null,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    /**
     * Get paginated admin logs.
     */
    public function getLogs(array $filters = [], int $perPage = 25): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = DB::table('admin_activity_logs')
            ->join('users', 'admin_activity_logs.user_id', '=', 'users.id')
            ->select(
                'admin_activity_logs.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderByDesc('admin_activity_logs.created_at');

        if (!empty($filters['user_id'])) {
            $query->where('admin_activity_logs.user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('admin_activity_logs.action', $filters['action']);
        }

        if (!empty($filters['resource_type'])) {
            $query->where('admin_activity_logs.resource_type', $filters['resource_type']);
        }

        if (!empty($filters['from'])) {
            $query->where('admin_activity_logs.created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('admin_activity_logs.created_at', '<=', $filters['to']);
        }

        return $query->paginate($perPage);
    }
}
