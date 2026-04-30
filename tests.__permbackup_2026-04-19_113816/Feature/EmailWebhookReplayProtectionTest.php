<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailWebhookReplayProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_replay_protection_requires_headers_when_enabled(): void
    {
        config([
            'email_webhook.replay_protection_enabled' => true,
            'email_webhook.cache_store' => 'array',
        ]);

        $this->postJson('/webhook/email/receive', [
            'from' => 'sender@example.com',
            'to' => 'info@bizmark.id',
            'subject' => 'Test',
            'message_id' => 'msg-1',
            'text' => 'Hello',
        ])->assertStatus(401);
    }

    public function test_replay_protection_accepts_valid_headers_when_enabled(): void
    {
        config([
            'email_webhook.replay_protection_enabled' => true,
            'email_webhook.cache_store' => 'array',
        ]);

        $this->postJson('/webhook/email/receive', [
            'from' => 'sender@example.com',
            'to' => 'info@bizmark.id',
            'subject' => 'Test',
            'message_id' => 'msg-2',
            'text' => 'Hello',
        ], [
            'X-Timestamp' => (string) now()->getTimestamp(),
            'X-Nonce' => str_repeat('a', 16),
        ])->assertStatus(200);
    }
}
