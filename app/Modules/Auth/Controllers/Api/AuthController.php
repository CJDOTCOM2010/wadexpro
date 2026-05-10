<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Mobile Login: Phone or Email authentication.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password'   => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken($request->device_name ?? 'mobile_device')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => $this->formatUser($user),
        ]);
    }

    /**
     * Request OTP for Phone Login.
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        try {
            app(\App\Modules\Auth\Services\AuthService::class)->requestOtp($request->phone);
            return response()->json(['message' => 'Verification code sent.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Login via OTP.
     */
    public function loginWithOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        try {
            $data = app(\App\Modules\Auth\Services\AuthService::class)->loginWithPhone($request->phone, $request->code);
            
            return response()->json([
                'status' => 'success',
                'token'  => $data['tokens']['access_token'],
                'user'   => $this->formatUser($data['user']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Mobile Registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'required|string|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('mobile_device')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => $this->formatUser($user),
        ], 201);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['driver.activeVehicle', 'wallet']);

        return response()->json([
            'status' => 'success',
            'user'   => $this->formatUser($user),
        ]);
    }

    /**
     * Mobile Logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Standardize user payload for mobile apps.
     */
    private function formatUser(User $user)
    {
        return [
            'id'             => $user->id,
            'uuid'           => $user->uuid,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'avatar_url'     => $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
            'user_type'      => $user->user_type ?? 'customer',
            'referral_code'  => $user->referral_code,
            'wallet_balance' => $user->wallet ? $user->wallet->balance : 0,
            'driver_profile' => $user->driver ? [
                'id'           => $user->driver->id,
                'status'       => $user->driver->status,
                'is_online'    => (bool) $user->driver->is_online,
                'is_available' => (bool) $user->driver->is_available,
                'vehicle'      => $user->driver->activeVehicle ? [
                    'id'           => $user->driver->activeVehicle->id,
                    'make'         => $user->driver->activeVehicle->make,
                    'model'        => $user->driver->activeVehicle->model,
                    'plate_number' => $user->driver->activeVehicle->plate_number,
                    'color'        => $user->driver->activeVehicle->color,
                ] : null,
            ] : null,
        ];
    }
}
