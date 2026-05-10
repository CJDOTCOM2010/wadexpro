<?php

namespace App\Modules\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TwoFactorService
{
    /**
     * TOTP code length.
     */
    private const CODE_LENGTH = 6;

    /**
     * TOTP period in seconds.
     */
    private const PERIOD = 30;

    /**
     * Secret key length in bytes.
     */
    private const SECRET_LENGTH = 20;

    /**
     * Generate a 2FA secret for a user.
     *
     * @return array{secret: string, qr_uri: string}
     */
    public function generateSecret(string $userId, string $email): array
    {
        $secret = $this->createBase32Secret();

        // Store the secret (encrypted) in user metadata
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'two_factor_secret'    => encrypt($secret),
                'two_factor_confirmed' => false,
                'updated_at'           => now(),
            ]);

        $appName = config('app.name', 'WadExp');
        $qrUri = "otpauth://totp/{$appName}:{$email}?secret={$secret}&issuer={$appName}&digits=" . self::CODE_LENGTH . '&period=' . self::PERIOD;

        return [
            'secret' => $secret,
            'qr_uri' => $qrUri,
        ];
    }

    /**
     * Confirm 2FA setup by verifying the first code.
     */
    public function confirmSetup(string $userId, string $code): bool
    {
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user || !$user->two_factor_secret) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);

        if ($this->verifyTotp($secret, $code)) {
            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes();

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'two_factor_confirmed'      => true,
                    'two_factor_recovery_codes'  => encrypt(json_encode($recoveryCodes)),
                    'updated_at'                => now(),
                ]);

            return true;
        }

        return false;
    }

    /**
     * Verify a 2FA code for a user.
     */
    public function verify(string $userId, string $code): bool
    {
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user || !$user->two_factor_secret || !$user->two_factor_confirmed) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);

        // Check TOTP code (current and adjacent windows)
        if ($this->verifyTotp($secret, $code)) {
            return true;
        }

        // Check recovery codes
        if ($user->two_factor_recovery_codes) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            if (in_array($code, $recoveryCodes, true)) {
                // Remove used recovery code
                $recoveryCodes = array_values(array_diff($recoveryCodes, [$code]));

                DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                        'updated_at'               => now(),
                    ]);

                return true;
            }
        }

        return false;
    }

    /**
     * Disable 2FA for a user.
     */
    public function disable(string $userId): void
    {
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'two_factor_secret'         => null,
                'two_factor_confirmed'      => false,
                'two_factor_recovery_codes' => null,
                'updated_at'               => now(),
            ]);
    }

    /**
     * Check if 2FA is enabled for a user.
     */
    public function isEnabled(string $userId): bool
    {
        $user = DB::table('users')
            ->where('id', $userId)
            ->select('two_factor_confirmed')
            ->first();

        return $user && $user->two_factor_confirmed;
    }

    /**
     * HMAC-based TOTP verification with window tolerance.
     */
    private function verifyTotp(string $secret, string $code, int $window = 1): bool
    {
        $now = time();
        $decodedSecret = $this->base32Decode($secret);

        for ($i = -$window; $i <= $window; $i++) {
            $timeSlice = (int) floor(($now + ($i * self::PERIOD)) / self::PERIOD);
            $expectedCode = $this->generateTotp($decodedSecret, $timeSlice);

            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate TOTP code for a given time slice.
     */
    private function generateTotp(string $decodedSecret, int $timeSlice): string
    {
        $timeHex = str_pad(pack('N*', 0, $timeSlice), 8, "\0", STR_PAD_LEFT);
        $hash = hash_hmac('sha1', $timeHex, $decodedSecret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $code = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        ) % (10 ** self::CODE_LENGTH);

        return str_pad((string) $code, self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Base32 secret key.
     */
    private function createBase32Secret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Decode Base32 string.
     */
    private function base32Decode(string $input): string
    {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $input = rtrim($input, '=');

        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($input); $i++) {
            $val = strpos($map, $input[$i]);
            if ($val === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Generate recovery codes.
     *
     * @return array<string>
     */
    private function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }
}
