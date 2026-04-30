<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuotationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    private PermitApplication $application;

    private Quotation $quotation;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        User::factory()->create(['role_id' => $role->id, 'is_active' => true]);

        $this->client = Client::factory()->create();
        $permitType = PermitType::factory()->create();

        $this->application = PermitApplication::create([
            'client_id' => $this->client->id,
            'permit_type_id' => $permitType->id,
            'status' => 'quoted',
            'form_data' => [],
        ]);

        $this->quotation = Quotation::create([
            'quotation_number' => 'QUO-TEST-001',
            'application_id' => $this->application->id,
            'client_id' => $this->client->id,
            'base_price' => 10000000,
            'additional_fees' => [],
            'discount_amount' => 0,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'total_amount' => 10000000,
            'down_payment_percentage' => 30,
            'down_payment_amount' => 3000000,
            'valid_until' => now()->addDays(7),
            'status' => 'sent',
        ]);
    }

    public function test_client_can_accept_quotation(): void
    {
        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/accept");

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('quotations', [
            'id' => $this->quotation->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('permit_applications', [
            'id' => $this->application->id,
            'status' => 'quotation_accepted',
        ]);
    }

    public function test_client_can_reject_quotation_with_reason(): void
    {
        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/reject", [
                'rejection_reason' => 'Harga terlalu tinggi untuk anggaran kami',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('quotations', [
            'id' => $this->quotation->id,
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('permit_applications', [
            'id' => $this->application->id,
            'status' => 'under_review',
        ]);
    }

    public function test_reject_requires_reason(): void
    {
        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/reject", [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    public function test_already_accepted_quotation_cannot_be_accepted_again(): void
    {
        $this->quotation->update(['status' => 'accepted', 'accepted_at' => now()]);
        $this->application->update(['status' => 'quotation_accepted']);

        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/accept");

        $response->assertRedirect();
        $response->assertSessionHas('info', 'Quotation sudah diterima sebelumnya');
    }

    public function test_already_accepted_quotation_cannot_be_rejected(): void
    {
        $this->quotation->update(['status' => 'accepted', 'accepted_at' => now()]);
        $this->application->update(['status' => 'quotation_accepted']);

        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/reject", [
                'rejection_reason' => 'Alasan penolakan',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Quotation yang sudah diterima tidak bisa ditolak');
    }

    public function test_expired_quotation_cannot_be_accepted(): void
    {
        $this->quotation->update(['valid_until' => now()->subDay()]);

        $response = $this->actingAs($this->client, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/accept");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('quotations', [
            'id' => $this->quotation->id,
            'status' => 'sent',
        ]);
    }

    public function test_client_cannot_accept_another_clients_quotation(): void
    {
        $otherClient = Client::factory()->create();

        $response = $this->actingAs($otherClient, 'client')
            ->post("/client/applications/{$this->application->id}/quotation/accept");

        $response->assertStatus(404);
    }

    public function test_unauthenticated_cannot_accept_quotation(): void
    {
        $this->post("/client/applications/{$this->application->id}/quotation/accept")
            ->assertRedirect('/login');
    }
}
