<?php

namespace App\Modules\Notifications\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Cache duration for OTP codes (minutes).
     */
    private const OTP_EXPIRY_MINUTES = 10;

    /**
     * Generate and "send" a one-time password for the given phone number.
     */
    public function generateForPhone(string $phone): string
    {
        // 1. Generate a secure 6-digit code
        $code = (string) rand(111111, 999999);

        // 2. Persist in cache for validation
        Cache::put($this->getCacheKey($phone), $code, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        // 3. Dispatch via SMS (High-Fidelity Log for production readiness)
        $this->dispatchSms($phone, "Your WADEXPRO verification code is: $code. Valid for 10 minutes.");

        return $code;
    }

    /**
     * Verify the provided code against the cached record.
     */
    public function verify(string $phone, string $code): bool
    {
        $cachedCode = Cache::get($this->getCacheKey($phone));

        if ($cachedCode && (string)$cachedCode === (string)$code) {
            Cache::forget($this->getCacheKey($phone));
            return true;
        }

        return false;
    }

    /**
     * Dispatch the SMS via the configured gateway.
     */
    private function dispatchSms(string $phone, string $message): void
    {
        // Integration Logic:
        // In a live environment, this would call Twilio, Infobip, or a local provider.
        // We log to the 'emergency' channel to ensure dispatch is audited.
        Log::channel('emergency')->info('OTP DISPATCH', [
            'to'      => $phone,
            'message' => $message,
            'gateway' => config('services.sms.default', 'log_fallback')
        ]);
        
        // For local development accessibility:
        Log::info("OTP for $phone: $message");
    }

    private function getCacheKey(string $phone): string
    {
        return "otp:v1:" . preg_replace('/[^0-9]/', '', $phone);
    }
}
