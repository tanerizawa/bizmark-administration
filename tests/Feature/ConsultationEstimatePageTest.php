<?php

namespace Tests\Feature;

use App\Models\ConsultRequest;
use App\Models\Kbli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationEstimatePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_estimasi_biaya_index_page_is_accessible(): void
    {
        $response = $this->get('/estimasi-biaya');

        $response->assertOk();
        $response->assertSee('Estimasi Biaya Perizinan');
    }

    public function test_result_page_still_renders_when_kbli_is_soft_deleted(): void
    {
        $kbli = Kbli::create([
            'code' => '62010',
            'description' => 'Aktivitas Pemrograman Komputer',
            'sector' => 'J',
            'category' => 'Menengah Rendah',
            'is_active' => true,
            'complexity_level' => 'medium',
        ]);

        $consultation = ConsultRequest::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'kbli_code' => $kbli->code,
            'business_size' => 'small',
            'location' => 'Jakarta',
            'location_type' => 'commercial',
            'investment_level' => '100m_500m',
            'employee_count' => 12,
            'project_description' => 'Pengurusan izin usaha',
            'estimate_status' => 'auto_estimated',
            'auto_estimate' => [
                'confidence_score' => 0.8,
                'cost_summary' => [
                    'formatted' => [
                        'subtotal' => 'Rp 10.000.000',
                        'grand_total' => 'Rp 12.000.000',
                        'range' => 'Rp 10.000.000 - Rp 12.000.000',
                    ],
                ],
                'cost_breakdown' => [],
            ],
        ]);

        $kbli->delete();

        $response = $this->get("/estimasi-biaya/hasil/{$consultation->id}");

        $response->assertOk();
        $response->assertSee('Hasil Estimasi Biaya');
        $response->assertSee('62010');
    }
}
