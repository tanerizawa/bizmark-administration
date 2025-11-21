<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FreeAIAnalysisService
{
    private const MODEL = 'gpt-3.5-turbo';
    private const MAX_TOKENS = 1000;
    private const TEMPERATURE = 0.7;
    private const CACHE_TTL = 7 * 24 * 60 * 60; // 7 days

    /**
     * Analyze business and recommend permits
     */
    public function analyze(array $formData): array
    {
        $startTime = microtime(true);

        // Check cache first (similar inquiries)
        $cacheKey = $this->generateCacheKey($formData);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            Log::info('FreeAIAnalysisService: Using cached analysis', ['cache_key' => $cacheKey]);
            return array_merge($cached, [
                'cached' => true,
                'processing_time' => 0
            ]);
        }

        try {
            // Build prompt
            $prompt = $this->buildPrompt($formData);

            // Call OpenAI API
            $response = OpenAI::chat()->create([
                'model' => self::MODEL,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => self::TEMPERATURE,
                'max_tokens' => self::MAX_TOKENS,
            ]);

            // Parse response
            $content = $response->choices[0]->message->content;
            $analysis = $this->parseResponse($content);

            // Add metadata
            $analysis['ai_model_used'] = self::MODEL;
            $analysis['ai_tokens_used'] = $response->usage->totalTokens;
            $analysis['ai_processing_time'] = (int) ((microtime(true) - $startTime) * 1000);
            $analysis['generated_at'] = now()->toIso8601String();
            $analysis['version'] = '1.0';
            $analysis['cached'] = false;

            // Cache result
            Cache::put($cacheKey, $analysis, self::CACHE_TTL);

            Log::info('FreeAIAnalysisService: Analysis completed', [
                'tokens' => $analysis['ai_tokens_used'],
                'time_ms' => $analysis['ai_processing_time']
            ]);

            return $analysis;

        } catch (\Exception $e) {
            Log::error('FreeAIAnalysisService: Analysis failed', [
                'error' => $e->getMessage(),
                'form_data' => $formData
            ]);

            // Return fallback analysis
            return $this->getFallbackAnalysis($formData);
        }
    }

    /**
     * Generate cache key based on business characteristics
     */
    private function generateCacheKey(array $formData): string
    {
        $key = implode('_', [
            $formData['kbli_code'] ?? 'no-kbli',
            $formData['business_scale'] ?? 'unknown',
            $formData['location_category'] ?? 'unknown',
            $formData['estimated_investment'] ?? 'unknown'
        ]);

        return 'ai_analysis_' . md5($key);
    }

    /**
     * Get system prompt for AI
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Anda adalah AI assistant ahli perizinan usaha di Indonesia dengan pengetahuan mendalam tentang:
- Peraturan OSS (Online Single Submission)
- Perizinan berusaha berbasis risiko
- KBLI (Klasifikasi Baku Lapangan Usaha Indonesia)
- Perizinan lingkungan (AMDAL, UKL-UPL)
- Perizinan operasional (NIB, SIUP, TDP, dll)

Tugas Anda: Memberikan rekomendasi perizinan dasar untuk usaha yang dijelaskan user.

OUTPUT FORMAT: JSON only (no markdown, no explanation outside JSON)

JSON Structure:
{
    "recommended_permits": [
        {
            "code": "PERMIT_CODE",
            "name": "Nama Izin Lengkap",
            "priority": "critical|high|medium",
            "estimated_timeline": "X-Y hari",
            "estimated_cost_range": "Rp X - Rp Y",
            "description": "Penjelasan singkat 1-2 kalimat"
        }
    ],
    "total_estimated_cost": {
        "min": 1000000,
        "max": 5000000,
        "currency": "IDR"
    },
    "total_estimated_timeline": "X-Y hari",
    "complexity_score": 7.5,
    "risk_factors": [
        "Risk factor 1",
        "Risk factor 2"
    ],
    "next_steps": [
        "Step 1",
        "Step 2"
    ],
    "limitations": "Analisis ini bersifat umum. Untuk analisis detail dengan dokumen checklist lengkap, timeline breakdown, dan pendampingan konsultan bersertifikat, silakan daftar ke portal kami."
}

RULES:
1. Recommend ONLY 3-5 most critical permits (prioritize by importance)
2. Use realistic cost ranges based on actual consultant fees + government fees
3. Timeline should be realistic (consider bureaucracy)
4. Complexity score: 1-10 (10 = most complex)
5. Risk factors: Focus on compliance risks, location issues, scale challenges
6. Next steps: Practical, actionable items
7. ALWAYS include limitations disclaimer
8. OUTPUT MUST BE VALID JSON ONLY
PROMPT;
    }

    /**
     * Build user prompt from form data
     */
    private function buildPrompt(array $formData): string
    {
        $kbli = $formData['kbli_code'] ?? 'Tidak disebutkan';
        $kbliDesc = $formData['kbli_description'] ?? '';
        $businessActivity = $formData['business_activity'] ?? 'Tidak disebutkan';
        $scale = $this->translateScale($formData['business_scale'] ?? 'unknown');
        $location = $formData['location_province'] ?? 'Tidak disebutkan';
        $locationCategory = $this->translateLocationCategory($formData['location_category'] ?? 'unknown');
        $investment = $this->translateInvestment($formData['estimated_investment'] ?? 'unknown');

        return <<<PROMPT
Analisis kebutuhan perizinan untuk usaha berikut:

INFORMASI USAHA:
- Jenis Usaha: {$businessActivity}
- Kode KBLI: {$kbli} {$kbliDesc}
- Skala Usaha: {$scale}
- Lokasi: {$location} ({$locationCategory})
- Estimasi Investasi: {$investment}

Berikan rekomendasi perizinan dalam format JSON seperti yang diminta.
PROMPT;
    }

    /**
     * Parse AI response (handle both JSON and markdown-wrapped JSON)
     */
    private function parseResponse(string $content): array
    {
        // Try direct JSON parse
        $json = json_decode($content, true);
        if ($json !== null) {
            return $json;
        }

        // Try extracting JSON from markdown code block
        if (preg_match('/```(?:json)?\s*(\{.+\})\s*```/s', $content, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json !== null) {
                return $json;
            }
        }

        // If all fails, throw exception
        throw new \Exception('Failed to parse AI response as JSON: ' . substr($content, 0, 200));
    }

    /**
     * Get fallback analysis if AI fails
     */
    private function getFallbackAnalysis(array $formData): array
    {
        return [
            'recommended_permits' => [
                [
                    'code' => 'OSS_NIB',
                    'name' => 'Nomor Induk Berusaha (NIB)',
                    'priority' => 'critical',
                    'estimated_timeline' => '1-3 hari',
                    'estimated_cost_range' => 'Rp 2.000.000 - Rp 5.000.000',
                    'description' => 'Izin dasar untuk memulai usaha di Indonesia, wajib untuk semua jenis usaha.'
                ],
                [
                    'code' => 'BUSINESS_LICENSE',
                    'name' => 'Izin Usaha Sesuai KBLI',
                    'priority' => 'high',
                    'estimated_timeline' => '7-14 hari',
                    'estimated_cost_range' => 'Rp 5.000.000 - Rp 15.000.000',
                    'description' => 'Izin operasional sesuai dengan jenis usaha Anda.'
                ]
            ],
            'total_estimated_cost' => [
                'min' => 7000000,
                'max' => 20000000,
                'currency' => 'IDR'
            ],
            'total_estimated_timeline' => '14-30 hari',
            'complexity_score' => 5.0,
            'risk_factors' => [
                'Analisis detail diperlukan untuk menentukan izin spesifik',
                'Timeline dapat bervariasi tergantung kelengkapan dokumen'
            ],
            'next_steps' => [
                'Lengkapi dokumen legalitas perusahaan',
                'Konsultasi lebih lanjut untuk analisis detail',
                'Daftar ke portal untuk pendampingan lengkap'
            ],
            'limitations' => 'Ini adalah analisis fallback. Untuk analisis detail dengan AI, silakan coba lagi atau daftar ke portal kami.',
            'ai_model_used' => 'fallback',
            'ai_tokens_used' => 0,
            'ai_processing_time' => 0,
            'generated_at' => now()->toIso8601String(),
            'version' => '1.0-fallback',
            'cached' => false
        ];
    }

    /**
     * Translation helpers
     */
    private function translateScale(string $scale): string
    {
        return match($scale) {
            'micro' => 'Mikro (< 10 karyawan)',
            'small' => 'Kecil (10-50 karyawan)',
            'medium' => 'Menengah (50-100 karyawan)',
            'large' => 'Besar (> 100 karyawan)',
            default => 'Tidak disebutkan'
        };
    }

    private function translateLocationCategory(string $category): string
    {
        return match($category) {
            'industrial' => 'Kawasan Industri',
            'commercial' => 'Area Komersial',
            'residential' => 'Area Residensial',
            'rural' => 'Pedesaan',
            default => 'Tidak disebutkan'
        };
    }

    private function translateInvestment(string $investment): string
    {
        return match($investment) {
            'under_100m' => '< Rp 100 juta',
            '100m_500m' => 'Rp 100 - 500 juta',
            '500m_2b' => 'Rp 500 juta - 2 miliar',
            'over_2b' => '> Rp 2 miliar',
            default => 'Tidak disebutkan'
        };
    }
}
