<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationApiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_returns_localized_validation_message(): void
    {
        $response = $this->postJson('/api/consultation/submit', []);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Validasi gagal. Mohon periksa data Anda.');
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'applicant_name',
                'contact_phone',
                'kbli_code',
                'business_size',
                'location',
                'location_type',
                'geographic_region',
                'entity_type',
                'investment_level',
            ],
        ]);
    }

    public function test_quick_estimate_returns_validation_failure_message(): void
    {
        $response = $this->postJson('/api/consultation/quick-estimate', []);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Validasi estimasi cepat gagal.');
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                'kbli_code',
                'business_size',
                'location_type',
            ],
        ]);
    }
}
