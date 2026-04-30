<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\PermitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApplicationDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_draft_even_when_save_as_draft_false(): void
    {
        $client = Client::factory()->create();
        $permitType = PermitType::factory()->create();

        $response = $this->actingAs($client, 'client')->post('/client/applications', [
            'permit_type_id' => $permitType->id,
            'form_data' => ['foo' => 'bar'],
            'save_as_draft' => false,
        ]);

        $response->assertRedirect();

        $application = PermitApplication::firstOrFail();
        $this->assertSame('draft', $application->status);
        $this->assertNull($application->submitted_at);
    }
}
