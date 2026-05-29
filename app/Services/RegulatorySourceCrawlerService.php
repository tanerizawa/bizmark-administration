<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * P7 — Crawls regulatory sources for new/updated laws/regulations.
 *
 * Previously: called nonexistent JSON APIs on jdih.go.id and jdih.menlhk.go.id.
 * Fixed 2026-05-05: now delegates to CivicStackService (JDIH module) which
 * scrapes peraturan.go.id via Playwright — the actual working source.
 *
 * Returns array of normalised documents for AI analysis.
 */
class RegulatorySourceCrawlerService
{
    public function __construct(private readonly CivicStackService $civic) {}

    /**
     * Crawl regulatory sources and return array of normalised documents.
     * Each item: ['title', 'url', 'document_number', 'published_at', 'source', 'raw'].
     */
    public function crawlAll(): array
    {
        $documents = [];

        // PP (Peraturan Pemerintah) — primary source for business permits
        $pp = $this->fetchJdih('perizinan usaha berusaha', 'pp', 10);
        $documents = array_merge($documents, $pp);
        Log::info('[RegCrawler] JDIH PP: '.count($pp).' documents fetched');

        // PERMEN (Peraturan Menteri) — ministerial regulations
        $permen = $this->fetchJdih('investasi izin usaha', 'permen', 10);
        $documents = array_merge($documents, $permen);
        Log::info('[RegCrawler] JDIH PERMEN: '.count($permen).' documents fetched');

        // UU (Undang-Undang) — laws relevant to business operations
        $uu = $this->fetchJdih('cipta kerja usaha mikro kecil menengah', 'uu', 5);
        $documents = array_merge($documents, $uu);
        Log::info('[RegCrawler] JDIH UU: '.count($uu).' documents fetched');

        return $documents;
    }

    private function fetchJdih(string $keyword, string $type, int $limit): array
    {
        $raw = $this->civic->jdihSearch($keyword, $type, $limit);

        if (empty($raw) || ! is_array($raw)) {
            return [];
        }

        $results = [];
        foreach ($raw as $item) {
            if (! isset($item['found']) || $item['found'] === false) {
                continue;
            }

            $result = $item['result'] ?? $item['data'] ?? $item;
            $results[] = [
                'title' => $result['title'] ?? 'Tanpa Judul',
                'url' => $item['source_url'] ?? $result['full_url'] ?? null,
                'document_number' => $result['regulation_id'] ?? null,
                'published_at' => $item['fetched_at'] ?? now()->toDateString(),
                'source' => 'JDIH (peraturan.go.id)',
                'raw' => $item,
            ];
        }

        return $results;
    }
}
