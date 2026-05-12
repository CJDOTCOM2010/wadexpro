<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\HR\Models\Employee;

class AdminDepartmentMiddleware
{
    /**
     * Handle an incoming request.
     * Ensure the authenticated admin user belongs to the required department,
     * or is a Super Admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $departmentsString
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $departmentsString): Response
    {
        $user = auth('admin')->user();
        
        if (!$user) {
            return redirect()->route('orchestrator.login')->with('error', 'Authentication required.');
        }

        // We assume user_type 'super_admin' bypasses all department checks
        if ($user->user_type === 'super_admin') {
            return $next($request);
        }

        // Only admins can access
        if ($user->user_type !== 'admin') {
            abort(403, 'Unauthorized. Root access required.');
        }

        $allowedDepartments = explode('|', $departmentsString);
        
        // Find the employee record linked to this user
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee || !in_array($employee->department, $allowedDepartments)) {
            // For UI feedback, redirect back to dashboard with flash message
            return redirect()->route('orchestrator.dashboard')
                ->with('error', 'Access Denied: Your department (' . ($employee->department ?? 'Unassigned') . ') does not have clearance for this module. Required: ' . str_replace('|', ' or ', $departmentsString) . '.');
        }

        return $next($request);
    }
}
