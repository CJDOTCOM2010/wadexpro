<?php

namespace App\Modules\Notifications\Services;

use App\Models\User;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    /**
     * Send a high-priority push notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        if (!$user->fcm_token) {
            Log::info("FCM: Skipping push to User {$user->id}. No token registered.");
            return false;
        }

        return $this->dispatch([$user->fcm_token], $title, $body, $data);
    }

    /**
     * Send push notification to multiple drivers.
     */
    public function broadcastToDrivers(iterable $drivers, string $title, string $body, array $data = [])
    {
        $tokens = [];
        foreach ($drivers as $driver) {
            $user = $driver->user ?? $driver;
            if ($user->fcm_token) {
                $tokens[] = $user->fcm_token;
            }
        }

        if (empty($tokens)) {
            Log::info("FCM: Skipping broadcast. No valid driver tokens found.");
            return false;
        }

        return $this->dispatch($tokens, $title, $body, $data);
    }

    /**
     * Core dispatch logic via Firebase Cloud Messaging HTTP v1 API.
     * Uses OAuth 2.0 service account credentials for authentication.
     */
    private function dispatch(array $tokens, string $title, string $body, array $data = [])
    {
        $projectId = SystemSetting::get('fcm_project_id');
        $serviceAccountJson = $this->getServiceAccountJson();

        // If credentials aren't configured, log and skip (graceful degradation)
        if (!$projectId || !$serviceAccountJson) {
            Log::channel('engagement')->info('FCM Dispatch (dry-run — no credentials)', [
                'tokens_count' => count($tokens),
                'title' => $title,
                'body' => $body,
            ]);
            return true;
        }

        $accessToken = $this->getAccessToken($serviceAccountJson);

        if (!$accessToken) {
            Log::error('FCM: Failed to obtain OAuth access token.');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $successCount = 0;

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(5)
                    ->post($url, [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'data' => array_map('strval', array_merge($data, [
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'timestamp'    => now()->toIso8601String(),
                            ])),
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'sound'          => 'default',
                                    'channel_id'     => 'wadexpro_high',
                                    'default_sound'  => true,
                                    'default_vibrate_timings' => true,
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound'            => 'default',
                                        'content-available' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    Log::warning('FCM: Send failed', [
                        'token'    => substr($token, 0, 20) . '...',
                        'status'   => $response->status(),
                        'response' => $response->json(),
                    ]);

                    // If token is invalid, clean it from the user record
                    if ($response->status() === 404 || $response->status() === 400) {
                        $this->invalidateToken($token);
                    }
                }
            } catch (\Exception $e) {
                Log::error('FCM: Exception during send', [
                    'error' => $e->getMessage(),
                    'token' => substr($token, 0, 20) . '...',
                ]);
            }
        }

        Log::channel('engagement')->info('FCM Dispatch', [
            'tokens_count' => count($tokens),
            'success_count' => $successCount,
            'title' => $title,
        ]);

        return $successCount > 0;
    }

    /**
     * Get OAuth 2.0 access token from Google service account credentials.
     * Cached for 50 minutes (tokens expire after 60 minutes).
     */
    private function getAccessToken(array $serviceAccount): ?string
    {
        return Cache::remember('fcm_access_token', 3000, function () use ($serviceAccount) {
            try {
                $now = time();
                $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
                $payload = base64_encode(json_encode([
                    'iss'   => $serviceAccount['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud'   => 'https://oauth2.googleapis.com/token',
                    'iat'   => $now,
                    'exp'   => $now + 3600,
                ]));

                $unsignedJwt = "{$header}.{$payload}";
                $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
                
                if (!$privateKey) {
                    Log::error('FCM: Invalid private key in service account.');
                    return null;
                }

                openssl_sign($unsignedJwt, $signature, $privateKey, OPENSSL_ALGO_SHA256);
                $jwt = "{$unsignedJwt}." . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('FCM: OAuth token request failed', ['response' => $response->json()]);
                return null;
            } catch (\Exception $e) {
                Log::error('FCM: OAuth token generation failed', ['error' => $e->getMessage()]);
                return null;
            }
        });
    }

    /**
     * Retrieve the Firebase service account JSON from system settings.
     */
    private function getServiceAccountJson(): ?array
    {
        $json = SystemSetting::get('fcm_service_account_json');

        if (!$json) {
            return null;
        }

        // Decrypt if stored encrypted
        try {
            $decrypted = decrypt($json);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            // Try parsing directly (might not be encrypted)
            $parsed = json_decode($json, true);
            return is_array($parsed) ? $parsed : null;
        }
    }

    /**
     * Invalidate a stale FCM token by nullifying it on the user record.
     */
    private function invalidateToken(string $token): void
    {
        User::where('fcm_token', $token)->update(['fcm_token' => null]);
        Log::info("FCM: Invalidated stale token: " . substr($token, 0, 20) . '...');
    }
}
