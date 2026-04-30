<?php

namespace Tests\Feature;

use Tests\TestCase;

class MidtransCallbackSignatureTest extends TestCase
{
    public function test_midtrans_callback_rejects_invalid_signature(): void
    {
        $this->postJson('/api/payment/callback', [
            'order_id' => 'PAY-202604-0001',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'invalid',
        ])->assertStatus(403);
    }
}
