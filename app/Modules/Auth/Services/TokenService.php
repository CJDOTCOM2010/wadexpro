<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TokenService
{
    /**
     * Refresh token lifetime in days.
     */
    private const REFRESH_TOKEN_TTL = 30;

    /**
     * Issue a new pair of tokens (Access + Refresh).
     */
    public function issueTokens(User $user): array
    {
        // 1. Create Access Token (Sanctum)
        // Note: For real sliding window, you'd set a short expiry on this in config/sanctum.php
        $accessToken = $user->createToken('access-token', ['*'], now()->addMinutes(30))->plainTextToken;

        // 2. Create Refresh Token
        $refreshToken = Str::random(64);
        
        DB::table('refresh_tokens')->insert([
            'id'         => (string) Str::uuid(),
            'user_id'    => $user->id,
            'token'      => hash('sha256', $refreshToken),
            'expires_at' => now()->addDays(self::REFRESH_TOKEN_TTL),
            'is_revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in'    => 1800, // 30 minutes in seconds
        ];
    }

    /**
     * Refresh an existing session.
     */
    public function refresh(string $refreshToken): array
    {
        $hashedToken = hash('sha256', $refreshToken);

        $storedToken = DB::table('refresh_tokens')
            ->where('token', $hashedToken)
            ->where('is_revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$storedToken) {
            throw new \Exception('Invalid or expired refresh token.');
        }

        // 1. Revoke the old refresh token (Rotation)
        DB::table('refresh_tokens')->where('id', $storedToken->id)->update([
            'is_revoked' => true,
            'updated_at' => now(),
        ]);

        $user = User::findOrFail($storedToken->user_id);

        // 2. Issue new tokens
        return $this->issueTokens($user);
    }

    /**
     * Revoke all tokens for a user.
     */
    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
        DB::table('refresh_tokens')->where('user_id', $user->id)->update([
            'is_revoked' => true,
            'updated_at' => now(),
        ]);
    }
}
