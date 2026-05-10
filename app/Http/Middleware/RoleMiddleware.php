<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $rolesString
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $rolesString): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
                'code' => 'UNAUTHENTICATED'
            ], 401);
        }

        // Split roles by pipe (e.g. "admin|super_admin")
        $roles = explode('|', $rolesString);

        if (!in_array($request->user()->user_type, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Required role: ' . $rolesString,
                'code' => 'UNAUTHORIZED_ROLE'
            ], 403);
        }

        return $next($request);
    }
}
