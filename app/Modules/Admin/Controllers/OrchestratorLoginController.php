<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrchestratorLoginController extends Controller
{
    /**
     * Display the high-fidelity Super Admin login page.
     */
    public function show(): View
    {
        return view('admin.login');
    }

    /**
     * Handle the Super Admin authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();

            // Strict Orchestrator Clearance Check - use is_super_admin or level
            if ($user->is_super_admin || $user->level === 'super_admin') {
                $request->session()->regenerate();

                return response()->json([
                    'status'   => 'success',
                    'redirect' => route('orchestrator.dashboard')
                ]);
            }

            // Not a super admin, terminate session for this guard
            Auth::guard('admin')->logout();
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Clearance level insufficient for Orchestrator access.'
            ], 403);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Identity verification failed. Please check your credentials.'
        ], 401);
    }

    /**
     * Terminate the Super Admin session.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        // We don't necessarily want to invalidate the whole session 
        // if the user is also logged in as a customer in another tab.
        // But we should regenerate the token for security.
        $request->session()->regenerateToken();

        return redirect()->route('orchestrator.login');
    }
}
