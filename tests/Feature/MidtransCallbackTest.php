<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransCallbackTest extends TestCase
{
    use RefreshDatabase;

    private string $serverKey = 'test-server-key-abc123';

    protected function setUp(): void
    {
        parent::setUp();
        config(['midtrans.server_key' => $this->serverKey]);
    }

    private function buildSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);
    }

    public function test_callback_rejects_missing_signature(): void
    {
        $this->postJson('/api/payment/callback', [
            'order_id' => 'PAY-202604-0001',
            'status_code' => '200',
            'gross_amount' => '5000000.00',
        ])->assertStatus(403);
    }

    public function test_callback_rejects_invalid_signature(): void
    {
        $this->postJson('/api/payment/callback', [
            'order_id' => 'PAY-202604-0001',
            'status_code' => '200',
            'gross_amount' => '5000000.00',
            'signature_key' => 'invalid-signature',
        ])->assertStatus(403);
    }

    public function test_callback_rejects_tampered_amount(): void
    {
        $orderId = 'PAY-202604-0002';
        $realAmount = '5000000.00';
        $tamperedAmount = '1.00';

        $signature = $this->buildSignature($orderId, '200', $realAmount);

        $this->postJson('/api/payment/callback', [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $tamperedAmount,
            'signature_key' => $signature,
        ])->assertStatus(403);
    }

    public function test_signature_validates_all_required_fields(): void
    {
        foreach (['order_id', 'status_code', 'gross_amount'] as $missing) {
            $payload = [
                'order_id' => 'PAY-TEST-001',
                'status_code' => '200',
                'gross_amount' => '1000.00',
                'signature_key' => 'some-key',
            ];
            unset($payload[$missing]);

            $this->postJson('/api/payment/callback', $payload)
                ->assertStatus(403, "Should reject when '$missing' is missing");
        }
    }

    public function test_sha512_signature_algorithm_is_correct(): void
    {
        $orderId = 'PAY-202604-0001';
        $statusCode = '200';
        $grossAmount = '5000000.00';

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);
        $computed = $this->buildSignature($orderId, $statusCode, $grossAmount);

        $this->assertEquals($expected, $computed);
        $this->assertNotEquals($computed, $this->buildSignature($orderId, '201', $grossAmount));
        $this->assertNotEquals($computed, $this->buildSignature($orderId, $statusCode, '1.00'));
    }
}
