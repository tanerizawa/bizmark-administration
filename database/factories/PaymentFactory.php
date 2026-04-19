<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $clientId = Client::query()->value('id') ?? Client::factory()->create()->id;
        $quotationId = Quotation::query()->value('id') ?? Quotation::factory()->create(['client_id' => $clientId])->id;

        return [
            'payment_number' => Payment::generatePaymentNumber(),
            'payable_type' => \App\Models\PermitApplication::class,
            'payable_id' => \App\Models\PermitApplication::query()->value('id') ?? 1,
            'client_id' => $clientId,
            'quotation_id' => $quotationId,
            'amount' => 100000,
            'payment_type' => 'down_payment',
            'payment_method' => 'manual',
            'gateway_provider' => null,
            'gateway_transaction_id' => null,
            'gateway_response' => null,
            'status' => 'processing',
            'bank_name' => 'BCA',
            'account_number' => null,
            'account_holder' => fake()->name(),
            'transfer_proof_path' => null,
            'verified_by' => null,
            'verified_at' => null,
            'verification_notes' => null,
            'paid_at' => null,
        ];
    }
}

