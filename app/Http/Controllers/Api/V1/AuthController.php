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
        $request->validate([
            'phone' => 'required|string',
            'user_type' => 'nullable|string'
        ]);

        // Mock OTP generation
        $otp = '123456';
        
        // In production, send via SMS gateway. Here we cache it for 5 minutes.
        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(5));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully (Test Mode: 123456)',
        ]);
    }

    public function verifyOtp(Request $request)
    {
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

        // Retrieve or create user
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => 'Wadex User',
                'user_type' => $request->user_type ?? 'customer',
                'password' => bcrypt('password') 
            ]
        );

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
    }
}
