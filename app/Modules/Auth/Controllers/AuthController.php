<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * POST /v1/auth/register
     * Register a new customer or driver account.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->created([
            'user'   => new UserResource($result['user']),
            'tokens' => $result['tokens'],
        ], 'Account created successfully.');
    }

    /**
     * POST /v1/auth/login
     * Authenticate via email/phone + password.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->success([
            'user'   => new UserResource($result['user']),
            'tokens' => $result['tokens'],
        ], 'Login successful.');
    }

    /**
     * POST /v1/auth/login/otp/send
     * Request a verification code for phone login.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string|exists:users,phone']);

        try {
            $this->authService->requestOtp($request->phone);
            return $this->success(null, 'Verification code sent.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * POST /v1/auth/login/otp
     * Authenticate via verified OTP (phone-based, mobile only).
     */
    public function loginWithOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
            'code'  => ['required', 'string'],
        ]);

        try {
            $result = $this->authService->loginWithPhone($request->phone, $request->code);

            return $this->success([
                'user'   => new UserResource($result['user']),
                'tokens' => $result['tokens'],
            ], 'OTP login successful.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    /**
     * POST /v1/auth/refresh
     * Rotate the session using a refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate(['refresh_token' => 'required|string']);

        try {
            $tokens = $this->authService->refreshToken($request->refresh_token);
            return $this->success($tokens, 'Tokens rotated successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 401);
        }
    }

    /**
     * POST /v1/auth/logout
     * Revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out successfully.');
    }

    /**
     * POST /v1/auth/logout/all
     * Revoke all tokens (all devices).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return $this->success(null, 'Logged out from all devices successfully.');
    }

    /**
     * GET /v1/auth/me
     * Return the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load(['roles.permissions', 'wallet'])),
            'Profile retrieved.'
        );
    }

    /**
     * PATCH /v1/auth/fcm-token
     * Update the Firebase Cloud Messaging token for push notifications.
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => ['required', 'string', 'max:255']]);
        $this->authService->updateFcmToken($request->user(), $request->fcm_token);

        return $this->success(null, 'FCM token updated.');
    }
}
