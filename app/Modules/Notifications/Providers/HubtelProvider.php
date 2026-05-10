<?php

namespace App\Modules\Notifications\Providers;

use Illuminate\Support\Facades\Http;

class HubtelProvider
{
    private string $clientId;
    private string $clientSecret;
    private string $senderId;
    private string $baseUrl = 'https://smsc.hubtel.com/v1/messages/send';

    public function __construct()
    {
        $this->clientId = config('sms.hubtel.client_id', '');
        $this->clientSecret = config('sms.hubtel.client_secret', '');
        $this->senderId = config('sms.hubtel.sender_id', 'WADEXP');
    }

    public function send(string $phone, string $message): bool
    {
        // Hubtel basic auth format
        $auth = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::withHeaders([
            'Authorization' => "Basic {$auth}",
        ])->get($this->baseUrl, [
            'From' => $this->senderId,
            'To' => $phone,
            'Content' => $message,
            'RegisteredDelivery' => 'true'
        ]);

        return $response->successful();
    }
}
