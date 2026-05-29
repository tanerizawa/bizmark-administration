<?php

namespace Tests\Unit\Services;

use App\Services\CivicStackService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CivicStackServiceTest extends TestCase
{
    private CivicStackService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.civic_stack.url' => 'http://civic-test:8000', 'services.civic_stack.timeout' => 5]);
        $this->service = new CivicStackService;
    }

    // ----------------------------------------------------------------
    // simbgSearch
    // ----------------------------------------------------------------

    public function test_simbg_search_returns_data_on_success(): void
    {
        Http::fake(['http://civic-test:8000/simbg/search*' => Http::response(['found' => true, 'city' => 'Jakarta Selatan'], 200)]);
        Cache::flush();

        $result = $this->service->simbgSearch('Jakarta Selatan');

        $this->assertNotNull($result);
        $this->assertTrue($result['found']);
    }

    public function test_simbg_search_returns_null_for_empty_city(): void
    {
        Http::fake();

        $result = $this->service->simbgSearch('   ');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_simbg_search_caches_result(): void
    {
        Http::fake(['http://civic-test:8000/simbg/search*' => Http::response(['found' => true], 200)]);
        Cache::flush();

        // Two calls — only one HTTP request
        $this->service->simbgSearch('Surabaya');
        $this->service->simbgSearch('Surabaya');

        Http::assertSentCount(1);
    }

    public function test_simbg_search_returns_null_on_connection_error(): void
    {
        Http::fake(['*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('refused')]);
        Cache::flush();
        Log::shouldReceive('warning')->once();

        $result = $this->service->simbgSearch('Bandung');

        $this->assertNull($result);
    }

    public function test_simbg_search_returns_null_on_non_2xx(): void
    {
        Http::fake(['*' => Http::response(['error' => 'internal'], 500)]);
        Cache::flush();
        Log::shouldReceive('warning')->once();

        $result = $this->service->simbgSearch('Medan');

        $this->assertNull($result);
    }

    // ----------------------------------------------------------------
    // bpjphSearch
    // ----------------------------------------------------------------

    public function test_bpjph_search_returns_data_on_success(): void
    {
        Http::fake(['http://civic-test:8000/bpjph/search*' => Http::response(['found' => true, 'company' => 'PT Halal'], 200)]);
        Cache::flush();

        $result = $this->service->bpjphSearch('PT Halal');

        $this->assertNotNull($result);
        $this->assertTrue($result['found']);
    }

    public function test_bpjph_search_returns_null_for_empty_input(): void
    {
        Http::fake();

        $result = $this->service->bpjphSearch('');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_bpjph_search_returns_null_on_connection_error(): void
    {
        Http::fake(['*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('refused')]);
        Cache::flush();
        Log::shouldReceive('warning')->once();

        $result = $this->service->bpjphSearch('PT Test Food');

        $this->assertNull($result);
    }

    // ----------------------------------------------------------------
    // nibLookup
    // ----------------------------------------------------------------

    public function test_nib_lookup_returns_data_on_success(): void
    {
        Http::fake(['http://civic-test:8000/oss-nib/nib/*' => Http::response(['found' => true, 'nib' => '1234567890123'], 200)]);
        Cache::flush();

        $result = $this->service->nibLookup('1234567890123');

        $this->assertNotNull($result);
        $this->assertEquals('1234567890123', $result['nib']);
    }

    public function test_nib_lookup_returns_null_for_empty_query(): void
    {
        Http::fake();

        $result = $this->service->nibLookup('');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_nib_lookup_returns_null_on_404(): void
    {
        Http::fake(['*' => Http::response(['found' => false], 404)]);
        Cache::flush();
        Log::shouldReceive('warning')->once();

        $result = $this->service->nibLookup('9999999999999');

        $this->assertNull($result);
    }

    // ----------------------------------------------------------------
    // jdihSearch
    // ----------------------------------------------------------------

    public function test_jdih_search_returns_array_on_success(): void
    {
        $fakeResponse = [
            ['found' => true, 'result' => ['title' => 'PP No.5/2025'], 'source_url' => 'https://peraturan.go.id/pp/5'],
        ];
        Http::fake(['http://civic-test:8000/jdih/search*' => Http::response($fakeResponse, 200)]);
        Cache::flush();

        $result = $this->service->jdihSearch('perizinan usaha', 'pp', 5);

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_jdih_search_returns_null_for_empty_keyword(): void
    {
        Http::fake();

        $result = $this->service->jdihSearch('');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_jdih_search_caches_for_60_minutes(): void
    {
        Http::fake(['http://civic-test:8000/jdih/search*' => Http::response([], 200)]);
        Cache::flush();

        $this->service->jdihSearch('izin usaha', 'uu', 5);
        $this->service->jdihSearch('izin usaha', 'uu', 5);

        Http::assertSentCount(1);
    }

    public function test_jdih_search_different_types_use_separate_cache_keys(): void
    {
        Http::fake(['http://civic-test:8000/jdih/search*' => Http::response([], 200)]);
        Cache::flush();

        $this->service->jdihSearch('izin usaha', 'pp', 5);
        $this->service->jdihSearch('izin usaha', 'uu', 5);

        Http::assertSentCount(2);
    }

    public function test_jdih_search_returns_null_on_connection_error(): void
    {
        Http::fake(['*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('refused')]);
        Cache::flush();
        Log::shouldReceive('warning')->once();

        $result = $this->service->jdihSearch('cipta kerja', 'uu');

        $this->assertNull($result);
    }
}
