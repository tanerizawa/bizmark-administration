<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FreeAIAnalysisService
{
    private const MODEL = 'gpt-3.5-turbo';
    private const MAX_TOKENS = 1000;
    private const TEMPERATURE = 0.7;
    private const CACHE_TTL = 7 * 24 * 60 * 60; // 7 days
    private const OPENROUTER_API_URL = 'https://openrouter.ai/api/v1/chat/completions';

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

            // Call OpenRouter API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(60)->post(self::OPENROUTER_API_URL, [
                'model' => 'openai/' . self::MODEL,
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

            if (!$response->successful()) {
                throw new \Exception('OpenRouter API error: ' . $response->body());
            }

            $responseData = $response->json();

            // Parse response
            $content = $responseData['choices'][0]['message']['content'] ?? '';
            if (empty($content)) {
                throw new \Exception('Empty response from OpenRouter');
            }

            $analysis = $this->parseResponse($content);

            // Add metadata
            $analysis['ai_model_used'] = self::MODEL;
            $analysis['ai_tokens_used'] = $responseData['usage']['total_tokens'] ?? 0;
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
            "government_fee": {
                "min": 0,
                "max": 500000,
                "note": "Gratis untuk NIB/NPWP, berbayar untuk izin operasional"
            },
            "consultant_fee": {
                "min": 1500000,
                "max": 3000000,
                "note": "Biaya konsultan BizMark untuk pendampingan"
            },
            "total_cost_range": "Rp 1,5 - 3,5 Juta",
            "description": "Penjelasan singkat 1-2 kalimat"
        }
    ],
    "total_estimated_cost": {
        "government_fees": {
            "min": 0,
            "max": 2000000
        },
        "consultant_fees": {
            "min": 5000000,
            "max": 15000000
        },
        "grand_total": {
            "min": 5000000,
            "max": 17000000
        },
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

BIAYA GUIDELINES:
1. **Government Fees** (Biaya Pemerintah):
   - NIB, NPWP, TDP: Rp 0 (GRATIS)
   - Izin Lingkungan (UKL-UPL): Rp 0 - 500rb
   - SIUP, SIUJK: Rp 500rb - 2jt
   - IMB/PBG: Rp 2jt - 10jt (tergantung luas bangunan)
   - AMDAL: Rp 5jt - 50jt (tergantung skala)

2. **Consultant Fees** (Biaya Konsultan BizMark):
   - Base fee per permit:
     * Foundational (NIB, NPWP): Rp 1,5jt - 2,5jt
     * Operational (SIUP, SIUJK): Rp 3jt - 5jt
     * Environmental (UKL-UPL): Rp 4jt - 8jt
     * Construction (IMB): Rp 5jt - 15jt
     * Complex (AMDAL): Rp 15jt - 50jt
   
   - Scale multipliers:
     * Mikro (<10 karyawan): 1.0x
     * Kecil (10-50): 1.5x
     * Menengah (50-100): 2.0x
     * Besar (>100): 2.5x
   
   - Location multipliers:
     * Jakarta, Surabaya: 1.3x
     * Kota besar lain: 1.1x
     * Kota kecil/kabupaten: 1.0x

3. **Total Cost = Government Fee + Consultant Fee**

RULES:
1. Recommend ONLY 3-5 most critical permits (prioritize by importance)
2. Separate government fees and consultant fees clearly
3. Government fees should be realistic per regulation
4. Consultant fees should reflect service complexity
5. Timeline should be realistic (consider bureaucracy)
6. Complexity score: 1-10 (10 = most complex)
7. Risk factors: Focus on compliance risks, location issues, scale challenges
8. Next steps: Practical, actionable items
9. ALWAYS include limitations disclaimer
10. OUTPUT MUST BE VALID JSON ONLY
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
                    'government_fee' => [
                        'min' => 0,
                        'max' => 0,
                        'note' => 'Gratis (biaya pemerintah)'
                    ],
                    'consultant_fee' => [
                        'min' => 1500000,
                        'max' => 2500000,
                        'note' => 'Biaya pendampingan konsultan'
                    ],
                    'total_cost_range' => 'Rp 1,5 - 2,5 Juta',
                    'description' => 'Izin dasar untuk memulai usaha di Indonesia, wajib untuk semua jenis usaha.'
                ],
                [
                    'code' => 'NPWP_BADAN',
                    'name' => 'NPWP Badan Usaha',
                    'priority' => 'critical',
                    'estimated_timeline' => '1-3 hari',
                    'government_fee' => [
                        'min' => 0,
                        'max' => 0,
                        'note' => 'Gratis (biaya pemerintah)'
                    ],
                    'consultant_fee' => [
                        'min' => 1000000,
                        'max' => 1500000,
                        'note' => 'Biaya pendampingan konsultan'
                    ],
                    'total_cost_range' => 'Rp 1 - 1,5 Juta',
                    'description' => 'Identitas wajib pajak untuk keperluan perpajakan perusahaan.'
                ],
                [
                    'code' => 'BUSINESS_LICENSE',
                    'name' => 'Izin Usaha Sesuai KBLI',
                    'priority' => 'high',
                    'estimated_timeline' => '7-14 hari',
                    'government_fee' => [
                        'min' => 500000,
                        'max' => 2000000,
                        'note' => 'Biaya resmi pemerintah'
                    ],
                    'consultant_fee' => [
                        'min' => 3000000,
                        'max' => 5000000,
                        'note' => 'Biaya pendampingan konsultan'
                    ],
                    'total_cost_range' => 'Rp 3,5 - 7 Juta',
                    'description' => 'Izin operasional sesuai dengan jenis usaha Anda.'
                ]
            ],
            'total_estimated_cost' => [
                'government_fees' => [
                    'min' => 500000,
                    'max' => 2000000
                ],
                'consultant_fees' => [
                    'min' => 5500000,
                    'max' => 9000000
                ],
                'grand_total' => [
                    'min' => 6000000,
                    'max' => 11000000
                ],
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
