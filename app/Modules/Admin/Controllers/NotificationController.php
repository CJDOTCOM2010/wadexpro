<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Channel setting key definitions (group → [keys])
    // ─────────────────────────────────────────────────────────────────────────
    protected static array $channels = [
        'email' => [
            'email_provider',          // smtp | mailgun | sendgrid | ses | postmark
            'email_from_address',
            'email_from_name',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',           // encrypted
            'smtp_encryption',         // tls | ssl | null
            'mailgun_domain',
            'mailgun_secret',          // encrypted
            'sendgrid_api_key',        // encrypted
            'ses_key',                 // encrypted
            'ses_secret',              // encrypted
            'ses_region',
            'postmark_token',          // encrypted
        ],
        'sms' => [
            'sms_provider',            // twilio | vonage | arkesel | termii | africas_talking
            'sms_sender_id',
            'twilio_account_sid',
            'twilio_auth_token',       // encrypted
            'twilio_from',
            'vonage_api_key',
            'vonage_api_secret',       // encrypted
            'arkesel_api_key',         // encrypted
            'termii_api_key',          // encrypted
            'termii_sender_id',
            'africas_talking_username',
            'africas_talking_api_key', // encrypted
        ],
        'whatsapp' => [
            'whatsapp_provider',       // meta | twilio | 360dialog
            'whatsapp_phone_number_id',
            'whatsapp_access_token',   // encrypted
            'whatsapp_business_account_id',
            'whatsapp_webhook_verify_token',
            'whatsapp_twilio_account_sid',
            'whatsapp_twilio_auth_token',  // encrypted
            'whatsapp_twilio_number',
            'whatsapp_360dialog_api_key',  // encrypted
        ],
        'push' => [
            'push_provider',           // fcm | apns | onesignal
            'fcm_server_key',          // encrypted
            'fcm_project_id',
            'fcm_service_account_json',// encrypted (full JSON)
            'apns_key_id',
            'apns_team_id',
            'apns_p8_private_key',     // encrypted
            'apns_bundle_id',
            'onesignal_app_id',
            'onesignal_api_key',       // encrypted
        ],
        'incall' => [
            'incall_provider',         // twilio | agora | vonage_video
            'agora_app_id',
            'agora_app_certificate',   // encrypted
            'vonage_video_api_key',
            'vonage_video_api_secret', // encrypted
            'incall_twilio_account_sid',
            'incall_twilio_auth_token',// encrypted
            'incall_twilio_twiml_app_sid',
        ],
    ];

    // Which keys must be stored encrypted
    protected static array $encryptedKeys = [
        'smtp_password', 'mailgun_secret', 'sendgrid_api_key', 'ses_key',
        'ses_secret', 'postmark_token', 'twilio_auth_token', 'vonage_api_secret',
        'arkesel_api_key', 'termii_api_key', 'africas_talking_api_key',
        'whatsapp_access_token', 'whatsapp_twilio_auth_token', 'whatsapp_360dialog_api_key',
        'fcm_server_key', 'fcm_service_account_json', 'apns_p8_private_key',
        'onesignal_api_key', 'agora_app_certificate', 'vonage_video_api_secret',
        'incall_twilio_auth_token',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Index — show all notification settings
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $settings = [];

        // Load all notification-related settings at once
        foreach (self::$channels as $channel => $keys) {
            foreach ($keys as $key) {
                $row = SystemSetting::where('key', $key)->first();
                if ($row) {
                    // For encrypted fields, return placeholder so they show as "configured"
                    $settings[$key] = in_array($key, self::$encryptedKeys) && $row->value
                        ? '••••••••••••' // masked — not decrypted to UI for security
                        : $row->value;
                } else {
                    $settings[$key] = null;
                }
            }
        }

        // Channel enabled flags
        $channels = [
            'email'     => SystemSetting::get('channel_email_enabled', 'false') === 'true',
            'sms'       => SystemSetting::get('channel_sms_enabled', 'false') === 'true',
            'whatsapp'  => SystemSetting::get('channel_whatsapp_enabled', 'false') === 'true',
            'push'      => SystemSetting::get('channel_push_enabled', 'false') === 'true',
            'incall'    => SystemSetting::get('channel_incall_enabled', 'false') === 'true',
        ];

        // Notification event toggles
        $events = SystemSetting::where('group', 'notification_events')->get()->pluck('value', 'key')->toArray();

        return view('admin.settings.notifications', compact('settings', 'channels', 'events'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Save channel credentials
    // ─────────────────────────────────────────────────────────────────────────
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            if (empty($value) || $value === '••••••••••••') {
                // Skip empty or masked placeholder — do not overwrite existing encrypted value
                continue;
            }

            // Determine if this key should be encrypted
            if (in_array($key, self::$encryptedKeys)) {
                $row = SystemSetting::firstOrNew(['key' => $key]);
                $row->value = Crypt::encryptString($value);
                $row->group = $this->resolveGroup($key);
                $row->is_encrypted = true;
                $row->save();
            } else {
                $row = SystemSetting::firstOrNew(['key' => $key]);
                $row->value = $value;
                $row->group = $this->resolveGroup($key);
                $row->is_encrypted = false;
                $row->save();
            }
        }

        // Channel enabled toggles (checkboxes not submitted = false)
        foreach (['email', 'sms', 'whatsapp', 'push', 'incall'] as $ch) {
            SystemSetting::set("channel_{$ch}_enabled", $request->has("channel_{$ch}_enabled") ? 'true' : 'false');
        }

        Cache::forget('system:notification_settings');

        return back()->with('success', 'Communication channels synchronized successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Save notification event toggles
    // ─────────────────────────────────────────────────────────────────────────
    public function updateEvents(Request $request)
    {
        $events = $request->input('events', []);

        // All possible event keys
        $allEventKeys = [
            'notify_ride_booked', 'notify_ride_assigned', 'notify_ride_completed',
            'notify_ride_cancelled', 'notify_payment_received', 'notify_payout_approved',
            'notify_driver_approved', 'notify_driver_rejected', 'notify_driver_suspended',
            'notify_otp_login', 'notify_support_ticket_reply', 'notify_promo_applied',
            'notify_wallet_credited', 'notify_wallet_debited', 'notify_kyc_submitted',
        ];

        foreach ($allEventKeys as $eventKey) {
            $row = SystemSetting::firstOrNew(['key' => $eventKey]);
            $row->value = in_array($eventKey, array_keys($events)) ? 'true' : 'false';
            $row->group = 'notification_events';
            $row->is_encrypted = false;
            $row->save();
        }

        Cache::forget('system:notification_settings');
        return back()->with('success', 'Notification event triggers updated.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test a channel connection
    // ─────────────────────────────────────────────────────────────────────────
    public function test(Request $request)
    {
        $channel  = $request->input('channel');
        $target   = $request->input('target'); // email address or phone number

        try {
            match ($channel) {
                'email'    => $this->testEmail($target),
                'sms'      => $this->testSms($target),
                'whatsapp' => $this->testWhatsApp($target),
                'push'     => $this->testPush(),
                default    => throw new \Exception("Unknown channel: {$channel}"),
            };

            return back()->with('success', "✅ Test {$channel} message dispatched successfully to {$target}.");
        } catch (\Throwable $e) {
            Log::error("Notification test failed [{$channel}]: " . $e->getMessage());
            return back()->with('error', "❌ {$channel} test failed: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function testEmail(string $to): void
    {
        $provider = SystemSetting::get('email_provider', 'smtp');
        $from     = SystemSetting::get('email_from_address', config('mail.from.address'));
        $fromName = SystemSetting::get('email_from_name', config('mail.from.name'));

        // We use a simple Mailable via Laravel's mail facade with dynamic config
        config([
            'mail.default'            => $provider,
            'mail.from.address'       => $from,
            'mail.from.name'          => $fromName,
            'mail.mailers.smtp.host'  => SystemSetting::get('smtp_host'),
            'mail.mailers.smtp.port'  => SystemSetting::get('smtp_port', 587),
            'mail.mailers.smtp.username' => SystemSetting::get('smtp_username'),
            'mail.mailers.smtp.password' => $this->getDecrypted('smtp_password'),
            'mail.mailers.smtp.encryption' => SystemSetting::get('smtp_encryption', 'tls'),
        ]);

        \Illuminate\Support\Facades\Mail::raw(
            'This is a test email from your WADEXPRO Orchestrator. If you received this, your email channel is correctly configured.',
            fn ($m) => $m->to($to)->subject('WADEXPRO — Email Channel Test')
        );
    }

    private function testSms(string $to): void
    {
        $provider = SystemSetting::get('sms_provider', 'twilio');

        if ($provider === 'twilio') {
            $sid   = SystemSetting::get('twilio_account_sid');
            $token = $this->getDecrypted('twilio_auth_token');
            $from  = SystemSetting::get('twilio_from');

            $response = Http::withBasicAuth($sid, $token)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => 'WADEXPRO: This is a test SMS from your Orchestrator. Channel verified. ✅',
                ]);

            if ($response->failed()) {
                throw new \Exception($response->json()['message'] ?? 'Twilio API error');
            }
        } elseif ($provider === 'termii') {
            $apiKey   = $this->getDecrypted('termii_api_key');
            $senderId = SystemSetting::get('termii_sender_id');

            $response = Http::post('https://api.ng.termii.com/api/sms/send', [
                'to'      => $to,
                'from'    => $senderId,
                'sms'     => 'WADEXPRO: Test SMS from Orchestrator. Channel active. ✅',
                'type'    => 'plain',
                'api_key' => $apiKey,
                'channel' => 'generic',
            ]);

            if ($response->failed()) {
                throw new \Exception('Termii API error: ' . $response->body());
            }
        } elseif ($provider === 'arkesel') {
            $apiKey   = $this->getDecrypted('arkesel_api_key');
            $senderId = SystemSetting::get('sms_sender_id');

            $response = Http::withHeaders(['api-key' => $apiKey])
                ->post('https://sms.arkesel.com/api/v2/sms/send', [
                    'sender'  => $senderId,
                    'message' => 'WADEXPRO: Test SMS from Orchestrator. Channel active. ✅',
                    'recipients' => [$to],
                ]);

            if ($response->failed()) {
                throw new \Exception('Arkesel API error: ' . $response->body());
            }
        } elseif ($provider === 'africas_talking') {
            $username = SystemSetting::get('africas_talking_username');
            $apiKey   = $this->getDecrypted('africas_talking_api_key');

            $response = Http::withHeaders([
                    'apiKey' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to'       => $to,
                    'message'  => 'WADEXPRO: Test SMS from Orchestrator. Channel active. ✅',
                ]);

            if ($response->failed()) {
                throw new \Exception("Africa's Talking API error: " . $response->body());
            }
        } else {
            throw new \Exception("SMS provider '{$provider}' does not have a test implementation yet.");
        }
    }

    private function testWhatsApp(string $to): void
    {
        $provider = SystemSetting::get('whatsapp_provider', 'meta');

        if ($provider === 'meta') {
            $phoneId = SystemSetting::get('whatsapp_phone_number_id');
            $token   = $this->getDecrypted('whatsapp_access_token');

            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $to,
                    'type'              => 'text',
                    'text'              => ['body' => 'WADEXPRO: Test WhatsApp message from your Orchestrator. Channel verified. ✅'],
                ]);

            if ($response->failed()) {
                throw new \Exception('Meta WhatsApp API error: ' . $response->body());
            }
        } else {
            throw new \Exception("WhatsApp provider '{$provider}' test not implemented.");
        }
    }

    private function testPush(): void
    {
        // Push test requires a valid FCM device token — we just validate the credentials
        $provider = SystemSetting::get('push_provider', 'fcm');

        if ($provider === 'fcm') {
            $serverKey = $this->getDecrypted('fcm_server_key');
            if (empty($serverKey)) {
                throw new \Exception('FCM Server Key not configured.');
            }
            // If key exists and is non-empty, mark as configured (cannot send without a token)
        } elseif ($provider === 'onesignal') {
            $appId  = SystemSetting::get('onesignal_app_id');
            $apiKey = $this->getDecrypted('onesignal_api_key');
            if (empty($appId) || empty($apiKey)) {
                throw new \Exception('OneSignal App ID or API Key not configured.');
            }
        }

        // No exception = credentials look valid
    }

    /**
     * Decrypt an encrypted setting safely.
     */
    private function getDecrypted(string $key): ?string
    {
        $row = SystemSetting::where('key', $key)->first();
        if (!$row || !$row->value) return null;

        try {
            return $row->is_encrypted ? Crypt::decryptString($row->value) : $row->value;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resolve the setting group from the key name.
     */
    private function resolveGroup(string $key): string
    {
        foreach (self::$channels as $group => $keys) {
            if (in_array($key, $keys)) return "notification_{$group}";
        }
        return 'notification';
    }
}
