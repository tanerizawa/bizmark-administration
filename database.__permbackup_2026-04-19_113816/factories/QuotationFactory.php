<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        $clientId = Client::query()->value('id') ?? Client::factory()->create()->id;
        $applicationId = PermitApplication::query()->value('id') ?? PermitApplication::factory()->create(['client_id' => $clientId])->id;

        $total = 10000000;
        $down = 3000000;

        return [
            'quotation_number' => 'QUO-'.Str::upper(Str::random(10)),
            'application_id' => $applicationId,
            'client_id' => $clientId,
            'base_price' => $total,
            'additional_fees' => [],
            'discount_amount' => 0,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'total_amount' => $total,
            'down_payment_percentage' => 30,
            'down_payment_amount' => $down,
            'payment_terms' => null,
            'valid_until' => now()->addDays(7),
            'terms_and_conditions' => null,
            'status' => 'accepted',
            'accepted_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
            'created_by' => User::query()->value('id'),
        ];
    }
}
