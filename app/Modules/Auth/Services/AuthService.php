<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Events\UserRegistered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;

/**
 * Handles all authentication business logic.
 * Controllers delegate entirely to this service.
 */
class AuthService
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    /**
     * Register a new user (customer or driver).
     */
    public function register(array $data): array
    {
        $referrerId = null;
        if (!empty($data['referral_code'])) {
            $referrer = User::where('referral_code', $data['referral_code'])->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'password'       => $data['password'],
            'user_type'      => $data['user_type'] ?? 'customer',
            'referred_by_id' => $referrerId,
        ]);

        if ($referrerId) {
            \App\Modules\Logistics\Models\Referral::create([
                'referrer_id' => $referrerId,
                'referred_id' => $user->id,
                'status'      => 'pending',
                'reward_type' => 'promo_code',
            ]);
        }

        // Assign default role matching user_type
        $user->assignRole($user->user_type);

        // Create wallet for customers and drivers
        if (in_array($user->user_type, ['customer', 'driver'], true)) {
            $user->wallet()->create([
                'balance'  => 0,
                'currency' => $data['currency'] ?? 'GHS',
            ]);
        }

        event(new UserRegistered($user));

        return [
            'user'   => $user,
            'tokens' => $this->tokenService->issueTokens($user),
        ];
    }

    /**
     * Authenticate a user by email/phone + password.
     * Returns user and token on success, throws on failure.
     */
    public function login(array $credentials): array
    {
        $field = isset($credentials['email']) ? 'email' : 'phone';
        $user  = User::where($field, $credentials[$field])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials.');
        }

        if (!$user->is_active) {
            throw new \Illuminate\Auth\AuthenticationException('Your account has been deactivated. Please contact support.');
        }

        $user->update(['last_login_at' => now()]);

        return [
            'user'   => $user,
            'tokens' => $this->tokenService->issueTokens($user),
        ];
    }

    /**
     * Request an OTP for a phone number.
     */
    public function requestOtp(string $phone): void
    {
        $user = User::where('phone', $phone)->firstOrFail();
        
        if (!$user->is_active) {
            throw new \Illuminate\Auth\AuthenticationException('Your account has been deactivated.');
        }

        app(\App\Modules\Notifications\Services\OtpService::class)->generateForPhone($phone);
    }

    /**
     * Login via OTP (phone-based, for mobile apps).
     */
    public function loginWithPhone(string $phone, string $code): array
    {
        $otpService = app(\App\Modules\Notifications\Services\OtpService::class);
        
        if (!$otpService->verify($phone, $code)) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid or expired verification code.');
        }

        $user = User::where('phone', $phone)->firstOrFail();

        if (!$user->is_active) {
            throw new \Illuminate\Auth\AuthenticationException('Your account has been deactivated.');
        }

        if (!$user->is_verified) {
            $user->update(['is_verified' => true]);
        }

        $user->update(['last_login_at' => now()]);

        return [
            'user'   => $user,
            'tokens' => $this->tokenService->issueTokens($user),
        ];
    }

    /**
     * Refresh a session using a refresh token.
     */
    public function refreshToken(string $refreshToken): array
    {
        return $this->tokenService->refresh($refreshToken);
    }

    /**
     * Revoke the current user's token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Revoke all tokens for a user (force logout all devices).
     */
    public function logoutAll(User $user): void
    {
        $this->tokenService->revokeAll($user);
    }

    /**
     * Update a user's FCM token for push notifications.
     */
    public function updateFcmToken(User $user, string $fcmToken): void
    {
        $user->update(['fcm_token' => $fcmToken]);
    }
}
