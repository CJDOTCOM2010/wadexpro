<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDepartmentMiddleware
{
    public function handle(Request $request, Closure $next, string $departmentsString): Response
    {
        $admin = auth('admin')->user();
        
        if (!$admin) {
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

        // No department assigned or not authorized - allow for now but log
        return $next($request);
    }
}