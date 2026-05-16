<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminDepartmentMiddleware
{
    public function handle(Request $request, Closure $next, string $departmentsString): Response
    {
        $admin = auth('admin')->user();

        if (! $admin) {
            return redirect()->route('orchestrator.login')->with('error', 'Authentication required.');
        }

        // Super admins bypass all department checks
        if ($admin->is_super_admin || $admin->level === 'super_admin') {
            return $next($request);
        }

        $allowedDepartments = explode('|', $departmentsString);

        // Check if admin's department is in allowed list
        if ($admin->department && in_array($admin->department, $allowedDepartments)) {
            return $next($request);
        }

        // Admin has no department assigned yet — allow with warning
        if (! $admin->department) {
            Log::info('Admin without department accessing restricted route', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'route' => $request->path(),
                'required_departments' => $departmentsString,
            ]);

            return $next($request);
        }

        // Deny: admin's department doesn't match
        abort(403, 'Unauthorized access. This section requires one of: '.$departmentsString);
    }
}
