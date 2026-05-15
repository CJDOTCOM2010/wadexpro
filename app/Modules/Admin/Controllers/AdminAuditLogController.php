<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\AdminAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminAuditLogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = AdminAuditLog::with('admin:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($request->has('action')) {
            $query->where('action', $request->get('action'));
        }

        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->get('admin_id'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return $this->success($logs);
    }

    public function show(string $id): JsonResponse
    {
        $log = AdminAuditLog::with('admin:id,name,email')->findOrFail($id);

        return $this->success($log);
    }
}
