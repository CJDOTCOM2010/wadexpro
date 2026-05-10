<?php

namespace App\Modules\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * OTP code length.
     */
    private const CODE_LENGTH = 6;

    /**
     * OTP expiration in minutes.
     */
    private const EXPIRY_MINUTES = 10;

    /**
     * Maximum verification attempts before lockout.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Generate and store an OTP code.
     *
     * @return array{code: string, expires_at: string}
     */
    public function generate(string $identifier, string $channel = 'sms', string $purpose = 'login'): array
    {
        // Invalidate any existing unused OTPs for this identifier/purpose
        DB::table('otp_verifications')
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->where('is_verified', false)
            ->delete();

        $code = $this->generateCode();
        $expiresAt = now()->addMinutes(self::EXPIRY_MINUTES);

        DB::table('otp_verifications')->insert([
            'id'         => (string) Str::uuid(),
            'identifier' => $identifier,
            'channel'    => $channel,
            'code'       => $code,
            'purpose'    => $purpose,
            'is_verified' => false,
            'expires_at' => $expiresAt,
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'code'       => $code,
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * Verify an OTP code.
     *
     * @return array{valid: bool, message: string}
     */
    public function verify(string $identifier, string $code, string $purpose = 'login'): array
    {
        $otp = DB::table('otp_verifications')
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->where('is_verified', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (!$otp) {
            return ['valid' => false, 'message' => 'No valid OTP found. Please request a new one.'];
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            DB::table('otp_verifications')->where('id', $otp->id)->delete();
            return ['valid' => false, 'message' => 'Maximum attempts exceeded. Please request a new OTP.'];
        }

        if ($otp->code !== $code) {
            DB::table('otp_verifications')
                ->where('id', $otp->id)
                ->increment('attempts');

            return ['valid' => false, 'message' => 'Invalid OTP code.'];
        }

        // Mark as verified
        DB::table('otp_verifications')
            ->where('id', $otp->id)
            ->update([
                'is_verified' => true,
                'updated_at'  => now(),
            ]);

        return ['valid' => true, 'message' => 'OTP verified successfully.'];
    }

    /**
     * Generate a random numeric code.
     */
    private function generateCode(): string
    {
        return str_pad((string) random_int(0, (int) (10 ** self::CODE_LENGTH - 1)), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
}
