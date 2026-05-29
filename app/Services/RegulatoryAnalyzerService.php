<?php

namespace App\Services;

use App\Models\RegulatoryChange;
use Illuminate\Support\Facades\Log;

/**
 * P7 — Analyzes a raw regulatory document using AI.
 * Scores relevance, generates summary in ID/EN, identifies affected services.
 */
class RegulatoryAnalyzerService
{
    private const SERVICE_CATEGORIES = [
        'amdal' => 'Analisis Mengenai Dampak Lingkungan (AMDAL)',
        'oss_nib' => 'Perizinan OSS & NIB',
        'izin_lokasi' => 'Izin Lokasi & Tata Ruang',
        'imb_pbg' => 'IMB / Persetujuan Bangunan Gedung',
        'ukl_upl' => 'UKL-UPL / Dokumen Lingkungan',
        'k3' => 'Keselamatan & Kesehatan Kerja (K3)',
        'halal' => 'Sertifikasi Halal',
        'pirt' => 'PIRT / Izin Produksi Pangan',
        'ekspor_impor' => 'Izin Ekspor/Impor',
    ];

    public function __construct(private OpenRouterService $ai) {}

    /**
     * Analyze one raw document, save to DB if new, return RegulatoryChange or null.
     */
    public function analyze(array $document): ?RegulatoryChange
    {
        $hash = hash('sha256', $document['url'].'|'.$document['title']);

        if (RegulatoryChange::where('document_hash', $hash)->exists()) {
            return null; // Already processed
        }

        $prompt = $this->buildPrompt($document);

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are a regulatory analyst specializing in Indonesian business licensing law. Respond ONLY with valid JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ]);

        if (! $result['success']) {
            Log::warning('[RegAnalyzer] AI call failed for: '.$document['title']);

            return null;
        }

        $parsed = $this->parseAiResponse($result['content']);

        if ($parsed === null) {
            Log::warning('[RegAnalyzer] AI JSON parse failed for: '.$document['title']);

            return null;
        }

        $publishedAt = null;
        try {
            $publishedAt = \Carbon\Carbon::parse($document['published_at'])->toDateString();
        } catch (\Throwable) {
            $publishedAt = now()->toDateString();
        }

        return RegulatoryChange::create([
            'source_url' => $document['url'],
            'document_number' => $document['document_number'],
            'title' => $document['title'],
            'published_at' => $publishedAt,
            'summary_id' => $parsed['summary_id'] ?? null,
            'summary_en' => $parsed['summary_en'] ?? null,
            'affected_service_categories' => $parsed['categories'] ?? [],
            'relevance_score' => min(1.0, max(0.0, (float) ($parsed['relevance_score'] ?? 0))),
            'document_hash' => $hash,
        ]);
    }

    private function buildPrompt(array $document): string
    {
        $categories = implode("\n", array_map(
            fn ($k, $v) => "- $k: $v",
            array_keys(self::SERVICE_CATEGORIES),
            self::SERVICE_CATEGORIES
        ));

        return <<<PROMPT
Analyze this Indonesian regulatory document and provide a JSON response.

Document:
Title: {$document['title']}
Number: {$document['document_number']}
Published: {$document['published_at']}
Source: {$document['source']}

Service categories for relevance scoring:
{$categories}

Respond with ONLY this JSON structure (no markdown):
{
  "summary_id": "3-5 sentence summary in Bahasa Indonesia explaining what changes and who is affected",
  "summary_en": "3-5 sentence summary in English",
  "categories": ["list", "of", "relevant", "category", "keys", "from", "above"],
  "relevance_score": 0.0
}

relevance_score: 0.0 = not relevant to Bizmark services, 1.0 = highly relevant (affects multiple services or major regulatory change).
Only include categories with actual relevance (>20% relevance to that service).
PROMPT;
    }

    private function parseAiResponse(string $content): ?array
    {
        // Strip markdown code fences if present
        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $content);
        $cleaned = preg_replace('/\s*```$/m', '', $cleaned);
        $decoded = json_decode(trim($cleaned), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }
}
