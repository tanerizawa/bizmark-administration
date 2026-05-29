<?php

namespace Tests\Feature;

use App\Services\CivicStackService;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class CivicStackApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function mockCivic(array $methods = []): CivicStackService
    {
        $mock = $this->createMock(CivicStackService::class);

        foreach ($methods as $method => $returnValue) {
            $mock->method($method)->willReturn($returnValue);
        }

        $this->app->instance(CivicStackService::class, $mock);

        return $mock;
    }

    // ================================================================
    // GET /api/civic/simbg-hints
    // ================================================================

    public function test_simbg_hints_returns_data_when_service_responds(): void
    {
        $this->mockCivic(['simbgSearch' => ['found' => true, 'city' => 'Jakarta', 'permits' => []]]);

        $this->getJson('/api/civic/simbg-hints?q=Jakarta')
            ->assertOk()
            ->assertJson(['found' => true, 'city' => 'Jakarta']);
    }

    public function test_simbg_hints_returns_found_false_when_service_returns_null(): void
    {
        $this->mockCivic(['simbgSearch' => null]);

        $this->getJson('/api/civic/simbg-hints?q=Bandung')
            ->assertOk()
            ->assertJson(['found' => false]);
    }

    public function test_simbg_hints_requires_q_param(): void
    {
        $this->getJson('/api/civic/simbg-hints')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_simbg_hints_rejects_q_too_short(): void
    {
        $this->getJson('/api/civic/simbg-hints?q=ab')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_simbg_hints_rejects_q_too_long(): void
    {
        $this->getJson('/api/civic/simbg-hints?q='.str_repeat('a', 101))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    // ================================================================
    // GET /api/civic/bpjph-check
    // ================================================================

    public function test_bpjph_check_returns_data_when_service_responds(): void
    {
        $this->mockCivic(['bpjphSearch' => ['found' => true, 'company' => 'PT Halal Sejahtera', 'status' => 'certified']]);

        $this->getJson('/api/civic/bpjph-check?q=PT+Halal+Sejahtera')
            ->assertOk()
            ->assertJson(['found' => true, 'status' => 'certified']);
    }

    public function test_bpjph_check_returns_found_false_when_service_returns_null(): void
    {
        $this->mockCivic(['bpjphSearch' => null]);

        $this->getJson('/api/civic/bpjph-check?q=PT+Unknown+Food')
            ->assertOk()
            ->assertJson(['found' => false]);
    }

    public function test_bpjph_check_requires_q_param(): void
    {
        $this->getJson('/api/civic/bpjph-check')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_bpjph_check_rejects_q_too_short(): void
    {
        $this->getJson('/api/civic/bpjph-check?q=ab')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    // ================================================================
    // GET /api/civic/nib-lookup
    // ================================================================

    /**
     * NIB 0220101102834 = PT ASIACON CIPTA PRIMA
     * NPWP 0808401137447000, diterbitkan 2025-10-17.
     * Data dikonfirmasi via competitor reverse-engineering (badanperizinan.co.id), Mei 2026.
     */
    public function test_nib_lookup_real_nib_pt_asiacon_cipta_prima(): void
    {
        $payload = [
            'found' => true,
            'nib' => '0220101102834',
            'company_name' => 'PT ASIACON CIPTA PRIMA',
            'npwp' => '0808401137447000',
            'issued_at' => '2025-10-17',
        ];

        $mock = $this->createMock(CivicStackService::class);
        $mock->expects($this->once())
            ->method('nibLookup')
            ->with('0220101102834')
            ->willReturn($payload);
        $this->app->instance(CivicStackService::class, $mock);

        $this->getJson('/api/civic/nib-lookup?q=0220101102834')
            ->assertOk()
            ->assertJson(['found' => true, 'nib' => '0220101102834', 'company_name' => 'PT ASIACON CIPTA PRIMA']);
    }

    public function test_nib_lookup_returns_data_when_service_responds(): void
    {
        $this->mockCivic(['nibLookup' => ['found' => true, 'nib' => '1234567890123', 'company_name' => 'PT Maju']]);

        $this->getJson('/api/civic/nib-lookup?q=1234567890123')
            ->assertOk()
            ->assertJson(['found' => true, 'nib' => '1234567890123']);
    }

    public function test_nib_lookup_returns_found_false_when_service_returns_null(): void
    {
        $this->mockCivic(['nibLookup' => null]);

        $this->getJson('/api/civic/nib-lookup?q=9999999999999')
            ->assertOk()
            ->assertJson(['found' => false]);
    }

    public function test_nib_lookup_requires_q_param(): void
    {
        $this->getJson('/api/civic/nib-lookup')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_nib_lookup_rejects_q_too_short(): void
    {
        $this->getJson('/api/civic/nib-lookup?q=ab')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    // ================================================================
    // GET /api/civic/jdih-search
    // ================================================================

    /**
     * KBLI 46638 = Perdagangan Besar Mesin, Peralatan dan Perlengkapan Kantor.
     * Relevant regulation: PP No.3/2026 (Penyelenggaraan Bidang Perdagangan).
     */
    public function test_jdih_search_kbli_46638_perdagangan(): void
    {
        $regs = [
            [
                'found' => true,
                'result' => [
                    'regulation_id' => 'pp-no-3-tahun-2026',
                    'regulation_type' => 'pp',
                    'year' => '2026',
                    'title' => 'Perubahan Atas Peraturan Pemerintah Nomor 29 Tahun 2021 Tentang Penyelenggaraan Bidang Perdagangan',
                    'status' => 'ACTIVE',
                    'full_url' => 'https://peraturan.go.id/id/pp-no-3-tahun-2026',
                ],
                'source_url' => 'https://peraturan.go.id/id/pp-no-3-tahun-2026',
                'fetched_at' => '2026-05-05T16:00:00',
                'module' => 'jdih',
            ],
        ];

        $mock = $this->createMock(CivicStackService::class);
        $mock->expects($this->once())
            ->method('jdihSearch')
            ->with('perdagangan', 'pp', 5)
            ->willReturn($regs);
        $this->app->instance(CivicStackService::class, $mock);

        $this->getJson('/api/civic/jdih-search?q=perdagangan&type=pp')
            ->assertOk()
            ->assertJson($regs);
    }

    public function test_jdih_search_returns_data_when_service_responds(): void
    {
        $regs = [
            ['found' => true, 'result' => ['title' => 'PP No.28/2025'], 'source_url' => 'https://peraturan.go.id/pp/28'],
        ];
        $this->mockCivic(['jdihSearch' => $regs]);

        $this->getJson('/api/civic/jdih-search?q=perizinan&type=pp')
            ->assertOk()
            ->assertJson($regs);
    }

    public function test_jdih_search_defaults_type_to_pp(): void
    {
        $mock = $this->createMock(CivicStackService::class);
        $mock->expects($this->once())
            ->method('jdihSearch')
            ->with('perizinan usaha', 'pp', 5)
            ->willReturn([]);
        $this->app->instance(CivicStackService::class, $mock);

        $this->getJson('/api/civic/jdih-search?q=perizinan+usaha')
            ->assertOk();
    }

    public function test_jdih_search_returns_found_false_when_service_returns_null(): void
    {
        $this->mockCivic(['jdihSearch' => null]);

        $this->getJson('/api/civic/jdih-search?q=cipta+kerja&type=uu')
            ->assertOk()
            ->assertJson(['found' => false]);
    }

    public function test_jdih_search_requires_q_param(): void
    {
        $this->getJson('/api/civic/jdih-search')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_jdih_search_rejects_invalid_type(): void
    {
        $this->getJson('/api/civic/jdih-search?q=perizinan&type=invalid_type')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_jdih_search_accepts_valid_types(): void
    {
        $this->mockCivic(['jdihSearch' => []]);

        foreach (['uu', 'pp', 'perpres', 'permen', 'perda', 'kepmen'] as $type) {
            $this->getJson("/api/civic/jdih-search?q=perizinan&type={$type}")
                ->assertOk();
        }
    }
}
