<?php

namespace Tests\Feature;

use App\Models\EmailInbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailWebhookDedupTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_deduplicates_by_message_id(): void
    {
        $payload = [
            'from' => 'Sender <sender@example.com>',
            'to' => 'info@bizmark.id',
            'subject' => 'Test inbound',
            'message_id' => 'msg-123',
            'text' => 'Hello',
        ];

        $this->postJson('/webhook/email/receive', $payload)->assertStatus(200);
        $this->postJson('/webhook/email/receive', $payload)->assertStatus(200);

        $this->assertSame(1, EmailInbox::where('message_id', 'msg-123')->count());
    }
}

