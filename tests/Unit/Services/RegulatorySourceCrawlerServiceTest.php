<?php

namespace Tests\Unit\Services;

use App\Services\CivicStackService;
use App\Services\RegulatorySourceCrawlerService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RegulatorySourceCrawlerServiceTest extends TestCase
{
    private function makeService(array $jdihMap = []): RegulatorySourceCrawlerService
    {
        $civic = $this->createMock(CivicStackService::class);

        $civic->method('jdihSearch')
            ->willReturnCallback(function (string $keyword, string $type, int $limit) use ($jdihMap) {
                $key = "{$keyword}|{$type}";

                return $jdihMap[$key] ?? null;
            });

        return new RegulatorySourceCrawlerService($civic);
    }

    // ----------------------------------------------------------------
    // crawlAll
    // ----------------------------------------------------------------

    public function test_crawl_all_returns_empty_when_civic_stack_is_down(): void
    {
        Log::shouldReceive('info')->times(3);
        $service = $this->makeService();

        $result = $service->crawlAll();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_crawl_all_merges_pp_permen_uu(): void
    {
        $ppItem = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/pp/1',
            'fetched_at' => '2026-05-01',
            'result' => ['title' => 'PP No.1/2026', 'regulation_id' => 'PP-1-2026'],
        ];
        $permenItem = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/permen/1',
            'fetched_at' => '2026-04-01',
            'result' => ['title' => 'Permen No.1/2026', 'regulation_id' => 'PERMEN-1-2026'],
        ];
        $uuItem = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/uu/11',
            'fetched_at' => '2020-11-03',
            'result' => ['title' => 'UU Cipta Kerja', 'regulation_id' => 'UU-11-2020'],
        ];

        Log::shouldReceive('info')->times(3);

        $service = $this->makeService([
            'perizinan usaha berusaha|pp' => [$ppItem],
            'investasi izin usaha|permen' => [$permenItem],
            'cipta kerja usaha mikro kecil menengah|uu' => [$uuItem],
        ]);

        $result = $service->crawlAll();

        $this->assertCount(3, $result);
    }

    public function test_crawl_all_normalises_document_shape(): void
    {
        $item = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/pp/28',
            'fetched_at' => '2025-03-01',
            'result' => [
                'title' => 'PP No.28/2025 Perizinan',
                'regulation_id' => 'PP-28-2025',
            ],
        ];

        Log::shouldReceive('info')->times(3);

        $service = $this->makeService([
            'perizinan usaha berusaha|pp' => [$item],
        ]);

        $result = $service->crawlAll();
        $doc = $result[0];

        $this->assertArrayHasKey('title', $doc);
        $this->assertArrayHasKey('url', $doc);
        $this->assertArrayHasKey('document_number', $doc);
        $this->assertArrayHasKey('published_at', $doc);
        $this->assertArrayHasKey('source', $doc);
        $this->assertArrayHasKey('raw', $doc);

        $this->assertEquals('PP No.28/2025 Perizinan', $doc['title']);
        $this->assertEquals('https://peraturan.go.id/pp/28', $doc['url']);
        $this->assertEquals('PP-28-2025', $doc['document_number']);
        $this->assertEquals('2025-03-01', $doc['published_at']);
        $this->assertStringContainsString('peraturan.go.id', $doc['source']);
    }

    public function test_crawl_all_skips_items_without_found_flag(): void
    {
        $items = [
            ['found' => false, 'result' => ['title' => 'Not Found']],
            ['found' => true, 'source_url' => 'https://peraturan.go.id/pp/5', 'fetched_at' => '2025-01-01', 'result' => ['title' => 'PP No.5/2025', 'regulation_id' => 'PP-5-2025']],
        ];

        Log::shouldReceive('info')->times(3);

        $service = $this->makeService([
            'perizinan usaha berusaha|pp' => $items,
        ]);

        $result = $service->crawlAll();

        // Only the 'found' item is included
        $this->assertCount(1, $result);
        $this->assertEquals('PP No.5/2025', $result[0]['title']);
    }

    public function test_crawl_all_handles_partial_failures(): void
    {
        $ppItem = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/pp/5',
            'fetched_at' => '2025-01-01',
            'result' => ['title' => 'PP No.5/2025', 'regulation_id' => 'PP-5-2025'],
        ];

        Log::shouldReceive('info')->times(3);

        // Only PP returns data; PERMEN and UU return null (service down)
        $service = $this->makeService([
            'perizinan usaha berusaha|pp' => [$ppItem],
        ]);

        $result = $service->crawlAll();

        $this->assertCount(1, $result);
        $this->assertEquals('PP No.5/2025', $result[0]['title']);
    }

    public function test_crawl_all_uses_fallback_title_when_missing(): void
    {
        $item = [
            'found' => true,
            'source_url' => 'https://peraturan.go.id/pp/99',
            'fetched_at' => '2025-06-01',
            'result' => [],   // no title
        ];

        Log::shouldReceive('info')->times(3);

        $service = $this->makeService([
            'perizinan usaha berusaha|pp' => [$item],
        ]);

        $result = $service->crawlAll();

        $this->assertEquals('Tanpa Judul', $result[0]['title']);
    }
}
