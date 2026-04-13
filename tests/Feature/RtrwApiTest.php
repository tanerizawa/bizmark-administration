<?php

namespace Tests\Feature;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RtrwApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    // ==========================================
    // GET /api/rtrw/provinces
    // ==========================================

    public function test_rtrw_provinces_returns_list(): void
    {
        $response = $this->getJson('/api/rtrw/provinces');

        $response->assertOk()
                 ->assertJsonStructure([
                     'provinces' => [
                         '*' => ['code', 'name'],
                     ],
                     'total',
                     'disclaimer',
                 ]);

        $data = $response->json();
        $this->assertGreaterThan(20, $data['total']);
        $this->assertNotEmpty($data['disclaimer']);
    }

    public function test_rtrw_provinces_includes_jawa_barat(): void
    {
        $response = $this->getJson('/api/rtrw/provinces');

        $provinces = collect($response->json('provinces'));
        $jabar = $provinces->firstWhere('code', '32');
        $this->assertNotNull($jabar);
        $this->assertEquals('Jawa Barat', $jabar['name']);
    }

    // ==========================================
    // GET /api/rtrw/zona
    // ==========================================

    public function test_rtrw_zona_requires_lat_lng_province(): void
    {
        $response = $this->getJson('/api/rtrw/zona');
        $response->assertUnprocessable();
    }

    public function test_rtrw_zona_rejects_invalid_lat(): void
    {
        $response = $this->getJson('/api/rtrw/zona?lat=999&lng=107&province_code=32');
        $response->assertUnprocessable();
    }

    public function test_rtrw_zona_rejects_invalid_province_code(): void
    {
        $response = $this->getJson('/api/rtrw/zona?lat=-6.9&lng=107.6&province_code=999');
        $response->assertUnprocessable();
    }

    public function test_rtrw_zona_returns_404_for_unavailable_province(): void
    {
        $response = $this->getJson('/api/rtrw/zona?lat=-6.2&lng=106.8&province_code=31');
        $response->assertNotFound()
                 ->assertJson(['available' => false]);
    }

    public function test_rtrw_zona_returns_correct_structure_with_mock(): void
    {
        // Mock the GISTARU proxy HTTP call
        Http::fake([
            'gistaru.atrbpn.go.id/*' => Http::response([
                'results' => [
                    [
                        'layerId' => 0,
                        'layerName' => '_3204_KABUPATEN BANDUNG',
                        'attributes' => [
                            'Nama Objek' => 'Kawasan Pertanian Lahan Basah',
                            'Jenis Rencana Pola Ruang' => 'Kawasan Budi Daya',
                            'Wilayah Administrasi Kabupaten/Kota' => 'Kabupaten Bandung',
                            'Wilayah Administrasi Kecamatan' => 'Kecamatan Baleendah',
                            'Wilayah Administrasi Provinsi' => 'Provinsi Jawa Barat',
                            'Nomor dan Tahun Peraturan' => 'Peraturan Daerah No. 27 Tahun 2016',
                            'Catatan' => 'Kawasan pertanian pangan berkelanjutan',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->getJson('/api/rtrw/zona?lat=-6.99&lng=107.62&province_code=32');

        $response->assertOk()
                 ->assertJsonStructure([
                     'province',
                     'province_code',
                     'lat',
                     'lng',
                     'zones' => [
                         '*' => ['layer_id', 'layer_name', 'zona', 'jenis_zona', 'kabupaten_kota', 'kecamatan', 'provinsi', 'no_perda', 'remark'],
                     ],
                     'available',
                     'source',
                     'disclaimer',
                 ]);

        $data = $response->json();
        $this->assertTrue($data['available']);
        $this->assertEquals('Jawa Barat', $data['province']);
        $this->assertCount(1, $data['zones']);
        $this->assertEquals('Kawasan Pertanian Lahan Basah', $data['zones'][0]['zona']);
        $this->assertEquals('Kabupaten Bandung', $data['zones'][0]['kabupaten_kota']);
    }

    public function test_rtrw_zona_caches_result(): void
    {
        Cache::flush();

        Http::fake([
            'gistaru.atrbpn.go.id/*' => Http::response([
                'results' => [
                    [
                        'layerId' => 0,
                        'layerName' => 'Test Layer',
                        'attributes' => [
                            'Nama Objek' => 'Kawasan Industri',
                            'Wilayah Administrasi Kabupaten/Kota' => 'Kota Bandung',
                        ],
                    ],
                ],
            ]),
        ]);

        // First call
        $this->getJson('/api/rtrw/zona?lat=-6.92&lng=107.61&province_code=32')
             ->assertOk();

        // Second call should use cache (no HTTP call)
        Http::fake([
            'gistaru.atrbpn.go.id/*' => Http::response(['results' => []], 500),
        ]);

        $response = $this->getJson('/api/rtrw/zona?lat=-6.92&lng=107.61&province_code=32');
        $response->assertOk()
                 ->assertJsonPath('zones.0.zona', 'Kawasan Industri');
    }

    // ==========================================
    // GET /api/rtrw/layers/{province}
    // ==========================================

    public function test_rtrw_layers_returns_404_for_unknown_province(): void
    {
        $response = $this->getJson('/api/rtrw/layers/99');
        $response->assertNotFound();
    }

    public function test_rtrw_layers_returns_structure_with_mock(): void
    {
        Http::fake([
            'gistaru.atrbpn.go.id/*' => Http::response([
                'layers' => [
                    ['id' => 0, 'name' => 'Pola Ruang Kab. Bandung'],
                    ['id' => 1, 'name' => 'Pola Ruang Kab. Bogor'],
                ],
            ]),
        ]);

        $response = $this->getJson('/api/rtrw/layers/32');

        $response->assertOk()
                 ->assertJsonStructure([
                     'province',
                     'province_code',
                     'layers' => [
                         '*' => ['id', 'name'],
                     ],
                     'total',
                 ]);

        $this->assertEquals(2, $response->json('total'));
    }

    // ==========================================
    // RTRW disabled state
    // ==========================================

    public function test_rtrw_zona_returns_503_when_disabled(): void
    {
        config(['rtrw.enabled' => false]);

        $response = $this->getJson('/api/rtrw/zona?lat=-6.9&lng=107.6&province_code=32');
        $response->assertStatus(503);

        config(['rtrw.enabled' => true]);
    }

    // ==========================================
    // Page includes RTRW UI elements
    // ==========================================

    public function test_polygon_shp_maker_has_rtrw_panel(): void
    {
        $response = $this->get('/polygon-shp-maker');

        $response->assertOk();
        $response->assertSee('Informasi Tata Ruang', false);
        $response->assertSee('Cek Zona RTRW', false);
    }
}
