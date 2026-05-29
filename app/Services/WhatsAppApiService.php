<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppApiService
{
    private string $baseUrl;

    private string $phoneNumberId;

    private string $accessToken;

    public function __construct()
    {
        $version = config('services.whatsapp.api_version', 'v21.0');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id') ?? '';
        $this->accessToken = config('services.whatsapp.access_token') ?? '';
        $this->baseUrl = "https://graph.facebook.com/{$version}/{$this->phoneNumberId}";
    }

    /**
     * Kirim pesan teks ke nomor WhatsApp.
     */
    public function sendText(string $toPhone, string $message): bool
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            Log::warning('[WhatsApp] Credentials not configured — skipping send.', ['to' => $toPhone]);

            return false;
        }

        $response = Http::withToken($this->accessToken)
            ->timeout(10)
            ->post("{$this->baseUrl}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toPhone,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if (! $response->successful()) {
            Log::error('[WhatsApp] Failed to send message', [
                'to' => $toPhone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Validasi HMAC-SHA256 signature dari Meta webhook.
     */
    public function validateSignature(string $rawPayload, ?string $signature): bool
    {
        $appSecret = config('services.whatsapp.app_secret', '');
        if (empty($appSecret) || empty($signature)) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $rawPayload, $appSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Ekstrak pesan inbound dari payload Meta webhook.
     * Kembalikan array ['phone', 'name', 'message', 'message_id'] atau null.
     */
    public function extractInboundMessage(array $payload): ?array
    {
        $entry = $payload['entry'][0] ?? null;
        $changes = $entry['changes'][0]['value'] ?? null;
        if (! $changes) {
            return null;
        }

        $messages = $changes['messages'][0] ?? null;
        if (! $messages || ($messages['type'] ?? '') !== 'text') {
            return null;
        }

        $contact = $changes['contacts'][0] ?? null;

        return [
            'phone' => $messages['from'],
            'name' => $contact['profile']['name'] ?? null,
            'message' => $messages['text']['body'] ?? '',
            'message_id' => $messages['id'] ?? null,
        ];
    }
}
