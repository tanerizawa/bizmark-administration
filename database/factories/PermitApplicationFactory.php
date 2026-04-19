<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\PermitType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermitApplicationFactory extends Factory
{
    protected $model = PermitApplication::class;

    public function definition(): array
    {
        $clientId = Client::query()->value('id') ?? Client::factory()->create()->id;
        $permitTypeId = PermitType::query()->value('id') ?? PermitType::factory()->create()->id;

        return [
            'application_number' => 'APP-' . Str::upper(Str::random(10)),
            'client_id' => $clientId,
            'permit_type_id' => $permitTypeId,
            'status' => 'draft',
            'form_data' => [],
            'notes' => null,
            'admin_notes' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'quoted_price' => null,
            'quoted_at' => null,
            'quotation_expires_at' => null,
            'quotation_notes' => null,
            'down_payment_amount' => null,
            'down_payment_percentage' => 30,
            'payment_status' => null,
            'project_id' => null,
            'converted_at' => null,
            'submitted_at' => null,
        ];
    }
}

