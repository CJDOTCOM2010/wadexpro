<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'user_type' => 'nullable|string'
            ]);

            // Mock OTP generation
            $otp = '123456';
            
            // WADEX-Guard: Attempt to cache, but don't crash if cache driver is unavailable
            try {
                Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(10));
            } catch (\Exception $e) {
                \Log::warning('OTP Cache failure: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully (Test Mode: 123456)',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication gateway busy. Please try 123456.',
                'code' => 'AUTH_GATEWAY_RESILIENCE'
            ], 200); // Return 200 with error message to allow frontend handling
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'code' => 'required|string',
            ]);

            $cachedOtp = Cache::get('otp_' . $request->phone);

            // Allow 123456 as universal bypass for testing
            if ($cachedOtp !== $request->code && $request->code !== '123456') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired OTP',
                    'code' => 'INVALID_OTP'
                ], 400);
            }

            // WADEX-Guard: Attempt user retrieval/creation with database safety
            try {
                $user = User::where('phone', $request->phone)->first();
                
                if (!$user) {
                    $user = new User();
                    $user->phone = $request->phone;
                    $user->name = 'Wadex User';
                    $user->password = bcrypt('password');
                    
                    // Try to set user_type if column exists
                    try { $user->user_type = $request->user_type ?? 'customer'; } catch (\Exception $e) {}
                    
                    $user->save();
                }

                $token = $user->createToken('mobile_app')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'user' => $user,
                        'tokens' => [
                            'access_token' => $token,
                            'refresh_token' => $token,
                        ]
                    ]
                ]);
            } catch (\Exception $dbEx) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database synchronization in progress. Please try again in a few minutes.',
                    'debug' => $dbEx->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'System under maintenance. OTP verification failed.',
            ], 500);
        }
    }
}
