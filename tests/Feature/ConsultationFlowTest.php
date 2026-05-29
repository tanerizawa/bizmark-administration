<?php

namespace Tests\Feature;

use App\Models\ConsultRequest;
use App\Models\Kbli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConsultationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock external AI service calls
        Http::preventStrayRequests();
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'base_cost' => 5000000,
                                'complexity_multiplier' => 1.2,
                                'location_multiplier' => 1.0,
                                'estimated_permits' => ['NIB', 'OSS', 'AMDAL'],
                                'timeline_days' => 30,
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    /**
     * Test complete consultation submission flow with AI estimation
     */
    public function test_complete_consultation_submission_flow(): void
    {
        // Create valid KBLI (5-digit code required)
        $kbli = Kbli::create([
            'code' => '01111', // 5-digit valid format
            'description' => 'Pertanian Jagung',
            'category' => 'A',
            'sector' => 'Agriculture',
            'sub_sector' => 'Crop Production',
            'complexity_level' => 'medium',
            'is_active' => true,
        ]);

        $submissionData = [
            'applicant_name' => 'Budi Santoso',
            'applicant_email' => 'budi@example.com',
            'contact_phone' => '081234567890',
            'kbli_code' => '01111',
            'business_size' => 'small',
            'location' => 'Jakarta Selatan',
            'location_type' => 'commercial',
            'geographic_region' => 'jakarta_capital',
            'entity_type' => 'pt',
            'investment_level' => '100m_500m',
            'employee_count' => 15,
            'target_timeline' => 'normal',
            'business_nature' => 'local_market',
            'deliverables' => 'Perizinan lengkap untuk usaha pertanian',
        ];

        // Submit consultation
        $response = $this->postJson('/api/consultation/submit', $submissionData);

        // Assert successful response
        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'data' => [
                'request_id',
                'kbli' => [
                    'code',
                    'description',
                    'category',
                    'complexity_level',
                ],
                'estimate' => [
                    'cost_summary',
                    'cost_breakdown',
                    'confidence_score',
                ],
                'rag' => [
                    'confidence',
                ],
                'next_steps',
            ],
            'meta' => [
                'processing_time_ms',
                'created_at',
            ],
        ]);

        // Verify database record created
        $consultRequest = ConsultRequest::first();
        $this->assertNotNull($consultRequest);
        $this->assertEquals('Budi Santoso', $consultRequest->name);
        $this->assertEquals('budi@example.com', $consultRequest->email);
        $this->assertEquals('081234567890', $consultRequest->phone);
        $this->assertEquals('01111', $consultRequest->kbli_code);
        $this->assertEquals('small', $consultRequest->business_size);
        $this->assertEquals('auto_estimated', $consultRequest->estimate_status);
        $this->assertNotNull($consultRequest->auto_estimate);
        $this->assertArrayHasKey('cost_summary', $consultRequest->auto_estimate);

        // Verify KBLI usage incremented
        $kbli->refresh();
        $this->assertEquals(1, $kbli->usage_count);
    }

    /**
     * Test consultation result page access
     */
    public function test_consultation_result_page_is_accessible(): void
    {
        $kbli = Kbli::create([
            'code' => '01111',
            'description' => 'Pertanian Jagung',
            'category' => 'A',
            'sector' => 'Agriculture',
            'sub_sector' => 'Crop Production',
            'is_active' => true,
        ]);

        $consultRequest = ConsultRequest::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'kbli_code' => '01111',
            'business_size' => 'small',
            'location' => 'Jakarta',
            'location_type' => 'commercial',
            'investment_level' => 'under_100m',
            'project_description' => 'Test consultation project',
            'estimate_status' => 'auto_estimated',
            'auto_estimate' => [
                'cost_summary' => [
                    'grand_total' => 5000000,
                    'formatted' => 'Rp 5.000.000',
                ],
            ],
        ]);

        $response = $this->get("/estimasi-biaya/hasil/{$consultRequest->id}");

        $response->assertStatus(200);
        $response->assertSee('Estimasi Biaya Berhasil');
        $response->assertSee('Pertanian Jagung');
        $response->assertSee("#{$consultRequest->id}");
    }

    /**
     * Test quick estimate API without saving
     */
    public function test_quick_estimate_returns_preview_without_saving(): void
    {
        $kbli = Kbli::create([
            'code' => '01111',
            'description' => 'Pertanian Jagung',
            'category' => 'A',
            'sector' => 'Agriculture',
            'sub_sector' => 'Crop Production',
            'complexity_level' => 'medium',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/consultation/quick-estimate', [
            'kbli_code' => '01111',
            'business_size' => 'small',
            'location_type' => 'commercial',
            'geographic_region' => 'jakarta_capital',
            'investment_level' => 'under_100m',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'data' => [
                'kbli',
                'estimate' => [
                    'formatted',
                    'cost_range',
                    'confidence_score',
                ],
            ],
        ]);

        // Verify no database record created
        $this->assertEquals(0, ConsultRequest::count());
    }

    /**
     * Test consultation with invalid KBLI code
     */
    public function test_consultation_rejects_invalid_kbli_code(): void
    {
        $response = $this->postJson('/api/consultation/submit', [
            'applicant_name' => 'Test User',
            'contact_phone' => '081234567890',
            'kbli_code' => '1234', // Invalid: only 4 digits
            'business_size' => 'small',
            'location' => 'Jakarta',
            'location_type' => 'commercial',
            'geographic_region' => 'jakarta_capital',
            'entity_type' => 'pt',
            'investment_level' => 'under_100m',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonValidationErrors(['kbli_code']);
    }

    /**
     * Test consultation with non-existent KBLI
     */
    public function test_consultation_rejects_nonexistent_kbli(): void
    {
        $response = $this->postJson('/api/consultation/submit', [
            'applicant_name' => 'Test User',
            'contact_phone' => '081234567890',
            'kbli_code' => '99999', // Valid format but doesn't exist
            'business_size' => 'small',
            'location' => 'Jakarta',
            'location_type' => 'commercial',
            'geographic_region' => 'jakarta_capital',
            'entity_type' => 'pt',
            'investment_level' => 'under_100m',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['kbli_code']);
    }

    /**
     * Test consultation with additional activities
     */
    public function test_consultation_accepts_additional_activities(): void
    {
        $kbli1 = Kbli::create([
            'code' => '01111',
            'description' => 'Pertanian Jagung',
            'category' => 'A',
            'sector' => 'Agriculture',
            'sub_sector' => 'Crop Production',
            'is_active' => true,
        ]);

        $kbli2 = Kbli::create([
            'code' => '01112',
            'description' => 'Pertanian Padi',
            'category' => 'A',
            'sector' => 'Agriculture',
            'sub_sector' => 'Crop Production',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/consultation/submit', [
            'applicant_name' => 'Test User',
            'contact_phone' => '081234567890',
            'kbli_code' => '01111',
            'business_size' => 'small',
            'location' => 'Jakarta',
            'location_type' => 'commercial',
            'geographic_region' => 'jakarta_capital',
            'entity_type' => 'pt',
            'investment_level' => 'under_100m',
            'additional_activities' => [
                [
                    'kbli_code' => '01112',
                    'description' => 'Pertanian Padi',
                ],
            ],
        ]);

        $response->assertStatus(201);

        $consultRequest = ConsultRequest::first();
        $this->assertNotNull($consultRequest);
    }

    /**
     * Test consultation result page returns 404 for non-existent ID
     */
    public function test_result_page_returns_404_for_invalid_id(): void
    {
        $response = $this->get('/estimasi-biaya/hasil/99999');
        $response->assertStatus(404);
    }

    /**
     * Test consultation index page loads successfully
     */
    public function test_consultation_index_page_loads(): void
    {
        $response = $this->get('/estimasi-biaya');
        $response->assertStatus(200);
    }
}
