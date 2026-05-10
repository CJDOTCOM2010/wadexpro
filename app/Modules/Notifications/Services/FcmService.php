<?php

namespace App\Modules\Notifications\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Core dispatch logic via Firebase HTTP v1 API.
     */
    private function dispatch(array $tokens, string $title, string $body, array $data = [])
    {
        // Integration Logic:
        // In a live environment, this would call the Google API using a service account token.
        // For development/initial production, we log the payload to the 'engagement' channel.

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'timestamp' => now()->toIso8601String(),
            ]),
            'priority' => 'high'
        ];

        Log::channel('engagement')->info('FCM Dispatch', [
            'tokens_count' => count($tokens),
            'payload' => $payload
        ]);

        // Simulating successful dispatch for now.
        // Once FCM_SERVER_KEY is provided, we use Http::withHeaders(...)->post(...)
        return true;
    }
}
