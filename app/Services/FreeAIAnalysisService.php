<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FreeAIAnalysisService
{
    private const CACHE_TTL = 12 * 60 * 60; // 12 hours
    private const OPENROUTER_API_URL = 'https://openrouter.ai/api/v1/chat/completions';
    private const REQUEST_TIMEOUT = 60; // seconds — keep under job timeout budget (2 models × 2 attempts × 60s = 240s < 300s job limit)

    /**
     * ═══ TIERED MODEL CONFIGURATION ═══
     *
     * FREE tier  (konsultasi-gratis, public landing page):
     *   Cost-optimized models — good quality at minimal cost per request.
     *   Primary: Gemini 2.5 Flash ($0.30/$2.50 per M tokens, 1M ctx, strong JSON reasoning)
     *   Fallback: DeepSeek V3.2 ($0.25/$0.40 per M tokens, 164K ctx, ultra-cheap)
     *
     * PREMIUM tier (client portal, authenticated users):
     *   Quality-optimized models — best reasoning accuracy for paying clients.
     *   Primary: Claude 3.5 Sonnet (~$3/$15 per M tokens, 200K ctx, top-tier regulatory reasoning)
     *   Fallback: Gemini 2.5 Flash ($0.30/$2.50 per M tokens, fallback still excellent)
     *
     * Pricing reference (OpenRouter, Feb 2026):
     *   Model                          | Input/M  | Output/M | Context
     *   google/gemini-2.5-flash         | $0.30    | $2.50    | 1,048,576
     *   deepseek/deepseek-v3.2          | $0.25    | $0.40    |   163,840
     *   anthropic/claude-3.5-sonnet     | $3.00    | $15.00   |   200,000
     *   x-ai/grok-4-fast               | $0.20    | $0.50    | 2,000,000
     */

    // ── Tier configurations ──
    private const TIER_FREE = 'free';
    private const TIER_PREMIUM = 'premium';

    private const TIER_CONFIG = [
        self::TIER_FREE => [
            'max_tokens'  => 3500,  // Sufficient for structured JSON output
            'temperature' => 0.25,  // Slightly higher — cost models benefit from it
        ],
        self::TIER_PREMIUM => [
            'max_tokens'  => 4500,  // Higher token budget for richer detail
            'temperature' => 0.15,  // Lower — premium models are more deterministic
        ],
    ];

    /**
     * Get primary + fallback model pair for a given tier
     * @return array{primary: string, fallback: string}
     */
    private function getModelsForTier(string $tier): array
    {
        if ($tier === self::TIER_PREMIUM) {
            return [
                'primary'  => config('services.openrouter.premium_primary_model', 'anthropic/claude-3.5-sonnet'),
                'fallback' => config('services.openrouter.premium_fallback_model', 'google/gemini-2.5-flash'),
            ];
        }

        // Free tier (default)
        return [
            'primary'  => config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash'),
            'fallback' => config('services.openrouter.free_fallback_model', 'deepseek/deepseek-v3.2'),
        ];
    }

    /**
     * Get tier-specific parameters (max_tokens, temperature)
     */
    private function getTierConfig(string $tier): array
    {
        return self::TIER_CONFIG[$tier] ?? self::TIER_CONFIG[self::TIER_FREE];
    }

    /**
     * Analyze business and recommend permits
     *
     * @param array  $formData  Business context data (KBLI, scale, location, etc.)
     * @param string $tier      'free' (landing page) or 'premium' (client portal)
     */
    public function analyze(array $formData, string $tier = self::TIER_FREE): array
    {
        $startTime = microtime(true);

        // Check cache first (similar inquiries) — cache key includes tier
        $cacheKey = $this->generateCacheKey($formData, $tier);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            Log::info('FreeAIAnalysisService: Using cached analysis', ['cache_key' => $cacheKey, 'tier' => $tier]);
            return array_merge($cached, [
                'cached' => true,
                'processing_time' => 0
            ]);
        }

        try {
            // Validate API key
            $apiKey = config('services.openrouter.api_key');
            if (empty($apiKey)) {
                Log::error('FreeAIAnalysisService: OpenRouter API key not configured');
                return $this->getFallbackAnalysis($formData);
            }

            // Get model pair and parameters for this tier
            $models = $this->getModelsForTier($tier);
            $tierConfig = $this->getTierConfig($tier);
            $primaryModel = $models['primary'];
            $fallbackModel = $models['fallback'];

            // Build prompt
            $systemPrompt = $this->getSystemPrompt();
            $userPrompt = $this->buildPrompt($formData);

            Log::info('FreeAIAnalysisService: Starting analysis', [
                'tier' => $tier,
                'primary_model' => $primaryModel,
                'fallback_model' => $fallbackModel,
                'max_tokens' => $tierConfig['max_tokens'],
            ]);

            // Try primary model
            $model = $primaryModel;
            $responseData = $this->callOpenRouterAPI($apiKey, $model, $systemPrompt, $userPrompt, $tierConfig);

            // Fallback to secondary model if primary fails
            if (!$responseData) {
                Log::warning('FreeAIAnalysisService: Primary model failed, trying fallback', [
                    'tier' => $tier,
                    'primary' => $primaryModel,
                    'fallback' => $fallbackModel,
                ]);
                $model = $fallbackModel;
                $responseData = $this->callOpenRouterAPI($apiKey, $model, $systemPrompt, $userPrompt, $tierConfig);
            }

            if (!$responseData) {
                throw new \Exception("Both {$tier} primary and fallback AI models failed");
            }

            // Parse response
            $content = $responseData['choices'][0]['message']['content'] ?? '';
            if (empty($content)) {
                throw new \Exception('Empty response from OpenRouter');
            }

            $analysis = $this->parseResponse($content);

            // Validate the analysis structure
            $analysis = $this->validateAndEnrichAnalysis($analysis, $formData);

            // Add metadata
            $analysis['ai_model_used'] = $model;
            $analysis['ai_tier'] = $tier;
            $analysis['ai_tokens_used'] = $responseData['usage']['total_tokens'] ?? 0;
            $analysis['ai_processing_time'] = (int) ((microtime(true) - $startTime) * 1000);
            $analysis['generated_at'] = now()->toIso8601String();
            $analysis['version'] = '3.1';
            $analysis['cached'] = false;

            // Cache result
            Cache::put($cacheKey, $analysis, self::CACHE_TTL);

            Log::info('FreeAIAnalysisService: Analysis completed', [
                'tier' => $tier,
                'model' => $model,
                'tokens' => $analysis['ai_tokens_used'],
                'time_ms' => $analysis['ai_processing_time'],
                'permits_count' => count($analysis['recommended_permits'] ?? []),
            ]);

            return $analysis;

        } catch (\Exception $e) {
            Log::error('FreeAIAnalysisService: Analysis failed', [
                'tier' => $tier,
                'error' => $e->getMessage(),
                'kbli_code' => $formData['kbli_code'] ?? 'N/A',
                'business_scale' => $formData['business_scale'] ?? 'N/A',
                'business_activity' => substr($formData['business_activity'] ?? '', 0, 100),
            ]);

            // Return context-aware fallback analysis — routed through validation for field completeness
            $fallback = $this->getFallbackAnalysis($formData);
            return $this->validateAndEnrichAnalysis($fallback, $formData);
        }
    }

    /**
     * Call OpenRouter API — extracted for model fallback support
     *
     * @param array $tierConfig  Tier-specific params: max_tokens, temperature
     */
    private function callOpenRouterAPI(string $apiKey, string $model, string $systemPrompt, string $userPrompt, array $tierConfig = []): ?array
    {
        try {
            // Build request payload with tier-specific parameters
            $payload = [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $tierConfig['temperature'] ?? 0.2,
                'max_tokens' => $tierConfig['max_tokens'] ?? 4000,
            ];

            // Only add response_format for models known to support structured JSON output
            if (str_contains($model, 'claude') || str_contains($model, 'gpt') || str_contains($model, 'gemini') || str_contains($model, 'deepseek')) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(self::REQUEST_TIMEOUT)->retry(1, 3000)->post(self::OPENROUTER_API_URL, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('FreeAIAnalysisService: API error', [
                'model' => $model,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 300),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::warning('FreeAIAnalysisService: API exception', [
                'model' => $model,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Analyze from KBLI parameters — used by OpenRouterService delegation
     * Transforms minimal KBLI params into the full analyze() format.
     */
    public function analyzeFromKbli(
        string $kbliCode,
        string $kbliDescription,
        string $sector,
        ?string $businessScale = null,
        ?string $locationType = null
    ): array {
        $result = $this->analyze([
            'kbli_code' => $kbliCode,
            'kbli_description' => $kbliDescription,
            'business_activity' => $kbliDescription,
            'business_scale' => $this->mapExternalScale($businessScale),
            'location_category' => $this->mapExternalLocation($locationType),
            'sector' => $sector,
        ], self::TIER_PREMIUM);

        // ── Portal context override ──
        // analyzeFromKbli() is ONLY called from the client portal (premium flow)
        // via OpenRouterService → KbliPermitCacheService → ServiceController.
        // Replace free-tier messaging ("silakan daftar ke portal") with portal-appropriate text.
        $result = $this->applyPortalContext($result);

        return $result;
    }

    /**
     * Override limitations, next_steps, and risk_factors for portal (premium) context.
     * Called ONLY from analyzeFromKbli() — ensures logged-in client portal users
     * never see "silakan daftar ke portal BizMark.ID" messaging.
     */
    private function applyPortalContext(array $result): array
    {
        // Determine if this was a fallback result (no real AI analysis)
        $isFallback = str_contains($result['ai_model_used'] ?? '', 'fallback');

        if ($isFallback) {
            $result['limitations'] = 'Ini adalah analisis estimasi otomatis berdasarkan regulasi 2026 (UU 6/2023, PP 5/2021). '
                . 'AI tidak dapat menganalisis saat ini, sehingga hasil berdasarkan template umum. '
                . 'Untuk analisis lebih akurat, gunakan fitur "Ajukan Permohonan / Konsultasi" di bawah '
                . 'agar konsultan bersertifikat BizMark dapat memberikan analisis detail sesuai kondisi spesifik usaha Anda.';
        } else {
            $result['limitations'] = 'Analisis ini dihasilkan oleh AI berdasarkan regulasi terkini 2026 (UU 6/2023, PP 5/2021) '
                . 'dan data KBLI yang tersedia. Meskipun sudah dioptimalkan untuk akurasi tinggi, persyaratan spesifik '
                . 'dapat bervariasi berdasarkan peraturan daerah (Perda) setempat dan kondisi lapangan. '
                . 'Gunakan fitur "Ajukan Permohonan / Konsultasi" untuk pendampingan konsultan bersertifikat.';
        }

        // Replace free-tier next_steps with portal-appropriate steps
        $portalNextSteps = [
            'Siapkan dokumen legalitas perusahaan (KTP, Akta, SK Kemenkumham)',
            'Periksa kesesuaian lokasi usaha dengan RTRW/RDTR setempat',
            'Daftar NIB melalui OSS RBA dengan pendampingan konsultan BizMark',
            'Urus dokumen lingkungan sesuai klasifikasi risiko usaha',
            'Klik "Ajukan Permohonan / Konsultasi" untuk memulai proses pendampingan',
            'Download ringkasan PDF sebagai referensi persiapan dokumen',
        ];

        // If AI provided next_steps, filter out free-tier messaging and merge
        if (!empty($result['next_steps'])) {
            $filtered = array_filter($result['next_steps'], function ($step) {
                return !str_contains(strtolower($step), 'daftar ke portal')
                    && !str_contains(strtolower($step), 'silakan daftar');
            });
            $result['next_steps'] = !empty($filtered) ? array_values($filtered) : $portalNextSteps;
        } else {
            $result['next_steps'] = $portalNextSteps;
        }

        // Filter risk_factors that reference free-tier
        if (!empty($result['risk_factors'])) {
            $result['risk_factors'] = array_values(array_filter($result['risk_factors'], function ($factor) {
                return !str_contains(strtolower($factor), 'daftar ke portal')
                    && !str_contains(strtolower($factor), 'silakan daftar');
            }));
        }

        return $result;
    }

    /**
     * Map external scale parameter to internal format
     */
    private function mapExternalScale(?string $scale): string
    {
        if (!$scale) return 'unknown';
        $map = [
            'mikro' => 'micro', 'micro' => 'micro',
            'kecil' => 'small', 'small' => 'small',
            'menengah' => 'medium', 'medium' => 'medium',
            'besar' => 'large', 'large' => 'large',
        ];
        return $map[strtolower($scale)] ?? $scale;
    }

    /**
     * Map external location parameter to internal format
     */
    private function mapExternalLocation(?string $location): string
    {
        if (!$location) return 'unknown';
        $map = [
            'kawasan_industri' => 'industrial', 'industrial' => 'industrial',
            'area_komersial' => 'commercial', 'commercial' => 'commercial',
            'area_residensial' => 'residential', 'residential' => 'residential',
            'pedesaan' => 'rural', 'rural' => 'rural',
        ];
        return $map[strtolower($location)] ?? $location;
    }

    /**
     * Generate cache key based on business characteristics
     * Includes tier, province and company_type for more specific caching
     */
    private function generateCacheKey(array $formData, string $tier = self::TIER_FREE): string
    {
        $key = implode('_', [
            $tier,
            $formData['kbli_code'] ?? 'no-kbli',
            $formData['business_scale'] ?? 'unknown',
            $formData['location_province'] ?? 'unknown',
            $formData['location_category'] ?? 'unknown',
            $formData['estimated_investment'] ?? 'unknown',
            $formData['company_type'] ?? 'unknown',
            substr(md5($formData['business_activity'] ?? ''), 0, 12),
        ]);

        return 'ai_analysis_v3_' . md5($key);
    }

    /**
     * Get system prompt for AI - Updated with current Indonesian regulations (2026)
     */
    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Anda adalah AI consultant ahli perizinan usaha di Indonesia. Tanggal referensi: Februari 2026.

═══ KERANGKA REGULASI BERLAKU (per 2026) ═══

1. UU CIPTA KERJA:
   - UU 6/2023 (Perppu 2/2022 yang disahkan menjadi UU — menggantikan UU 11/2020 yang dibatalkan MK)
   - Menjadi payung hukum omnibus perizinan berusaha di Indonesia

2. PERIZINAN BERBASIS RISIKO (OSS RBA):
   - PP 5/2021 tentang Penyelenggaraan Perizinan Berusaha Berbasis Risiko (masih berlaku 2026)
   - Perpres 10/2021 tentang Bidang Usaha Penanaman Modal
   - Klasifikasi risiko: Rendah → NIB saja; Menengah Rendah → NIB + Sertifikat Standar (self-declare);
     Menengah Tinggi → NIB + Sertifikat Standar (verifikasi); Tinggi → NIB + Izin
   - OSS RBA (oss.go.id) = single portal wajib untuk semua perizinan berusaha

3. PERIZINAN LINGKUNGAN:
   - PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup
   - Permen LHK 4/2021 tentang Daftar Usaha/Kegiatan Wajib AMDAL, UKL-UPL, atau SPPL
   - Permen LHK 6/2021 tentang Tata Cara & Persyaratan Pengelolaan Limbah B3
   - Persetujuan Lingkungan = AMDAL (risiko tinggi) atau UKL-UPL (risiko menengah) atau SPPL (risiko rendah)
   - AMDAL: wajib untuk usaha berdampak penting (Permen LHK 4/2021 Lampiran I)
   - UKL-UPL: usaha tidak berdampak penting tapi perlu pengelolaan lingkungan
   - SPPL: usaha risiko rendah (cukup surat pernyataan)

4. PERIZINAN BANGUNAN:
   - PP 16/2021 tentang Peraturan Pelaksanaan UU 28/2002 tentang Bangunan Gedung
   - PBG (Persetujuan Bangunan Gedung) menggantikan IMB — wajib untuk semua bangunan baru/renovasi
   - SLF (Sertifikat Laik Fungsi) — wajib setelah bangunan selesai, sebelum operasional
   - SIMBG (Sistem Informasi Manajemen Bangunan Gedung) = portal PBG dan SLF

5. TATA RUANG:
   - PP 21/2021 tentang Penyelenggaraan Penataan Ruang
   - KKPR / PKKPR (Kesesuaian Kegiatan Pemanfaatan Ruang) — menggantikan Izin Lokasi lama
   - Konfirmasi KKPR (risiko rendah) vs Persetujuan KKPR (risiko menengah-tinggi)
   - Diajukan via OSS RBA, diterbitkan oleh ATR/BPN

6. IZIN SEKTORAL (NSPK K/L):
   - Setiap kementerian/lembaga menerbitkan NSPK (Norma, Standar, Prosedur, Kriteria)
   - Izin sektoral terintegrasi melalui OSS RBA
   - Contoh: Izin Edar BPOM (makanan/obat), SIUJK → SBU Konstruksi, Izin Operasional RS/Klinik, dll.

7. IZIN YANG SUDAH TIDAK BERLAKU (JANGAN REKOMENDASIKAN):
   - ❌ SIUP (digantikan NIB + perizinan berbasis risiko)
   - ❌ TDP (digantikan NIB)
   - ❌ IUI (digantikan Sertifikat Standar / Izin melalui OSS)
   - ❌ IMB (digantikan PBG)
   - ❌ HO / Izin Gangguan (dicabut oleh Permendagri 19/2017)
   - ❌ Izin Lokasi lama (digantikan KKPR/PKKPR via OSS)

8. KBLI 2020:
   - Klasifikasi Baku Lapangan Usaha Indonesia (BPS, berlaku sejak 2020)
   - Kode 5 digit = level paling detail
   - Setiap KBLI memiliki tingkat risiko dan persyaratan izin tersendiri di OSS

═══ TUGAS ═══
Analisis kebutuhan perizinan usaha berdasarkan data yang diberikan.
Berikan rekomendasi AKURAT, SPESIFIK, dan SISTEMATIS.

═══ INSTRUKSI URUTAN IZIN (WAJIB DIIKUTI) ═══
Urutkan recommended_permits dari PERTAMA diurus hingga TERAKHIR, mengikuti alur dependency:

TAHAP 1 — LEGALITAS DASAR (foundational):
  1. NIB (Nomor Induk Berusaha) — selalu pertama
  2. NPWP Badan Usaha — setelah NIB
  3. Akta Pendirian & SK Kemenkumham — jika badan usaha baru

TAHAP 2 — TATA RUANG & LINGKUNGAN:
  4. KKPR/PKKPR (Kesesuaian Kegiatan Pemanfaatan Ruang) — jika butuh lahan/bangunan
  5. Persetujuan Lingkungan: SPPL / UKL-UPL / AMDAL — sesuai tingkat risiko
  6. Izin terkait lingkungan lanjutan: TPS-LB3, Izin Pengelolaan B3, dll.

TAHAP 3 — IZIN TEKNIS & BANGUNAN:
  7. PBG (Persetujuan Bangunan Gedung) — jika ada bangunan baru/renovasi
  8. SLF (Sertifikat Laik Fungsi) — setelah bangunan selesai sebelum operasional

TAHAP 4 — IZIN OPERASIONAL & STANDAR:
  9. Sertifikat Standar / Izin Usaha — sesuai klasifikasi risiko KBLI
  10. Izin operasional terkait (jika ada)

TAHAP 5 — IZIN SEKTORAL KHUSUS:
  11. Izin sektoral sesuai NSPK K/L (BPOM, Kemenkes, Kemen PUPR, dll.)
  12. Izin daerah tambahan (jika ada regulasi lokal)

═══ INSTRUKSI ANALISIS ═══
1. IDENTIFIKASI SEMUA IZIN — Jangan lewatkan izin pendukung, operasional, dan teknis
2. URUTAN DEPENDENCY — Setiap izin WAJIB punya prerequisites dan triggers_next (array kosong [] jika tidak ada)
3. KATEGORISASI — Setiap izin WAJIB memiliki category: foundational|environmental|technical|operational|sectoral
4. IZIN LENGKAP — Untuk sektor berisiko tinggi, identifikasi:
   - Izin lingkungan: AMDAL/UKL-UPL/SPPL
   - Izin tata ruang: KKPR/PKKPR
   - Izin teknis: PBG, SLF
   - Izin operasional: Sertifikat Standar
   - Izin sektoral: sesuai NSPK K/L
5. KONSISTEN — Nama izin di prerequisites/triggers_next HARUS SAMA PERSIS dengan name izin yang direferensikan

OUTPUT: JSON only, TANPA markdown wrapper, TANPA teks diluar JSON.

JSON Structure:
{
    "recommended_permits": [
        {
            "code": "KODE_IZIN",
            "name": "Nama Izin Lengkap",
            "priority": "critical|high|medium",
            "category": "foundational|environmental|technical|operational|sectoral",
            "estimated_timeline": "X-Y hari kerja",
            "government_fee": {
                "min": 0,
                "max": 500000,
                "note": "Penjelasan biaya"
            },
            "consultant_fee": {
                "min": 1500000,
                "max": 3000000,
                "note": "Biaya pendampingan BizMark"
            },
            "total_cost_range": "Rp X - Y Juta",
            "description": "Penjelasan 2-3 kalimat fungsi izin ini untuk usaha tersebut",
            "issuing_authority": "Instansi penerbit",
            "legal_basis": "Dasar hukum (UU/PP/Permen yang berlaku 2026)",
            "prerequisites": ["Nama izin yang HARUS dimiliki dulu — gunakan nama yang PERSIS SAMA"],
            "triggers_next": ["Nama izin yang BISA diurus setelah ini selesai"]
        }
    ],
    "risk_classification": "rendah|menengah_rendah|menengah_tinggi|tinggi",
    "kbli_suggestion": {
        "code": "XXXXX",
        "description": "Deskripsi KBLI",
        "confidence": "high|medium|low"
    },
    "total_estimated_cost": {
        "government_fees": { "min": 0, "max": 2000000 },
        "consultant_fees": { "min": 5000000, "max": 15000000 },
        "grand_total": { "min": 5000000, "max": 17000000 },
        "currency": "IDR"
    },
    "total_estimated_timeline": "X-Y hari kerja",
    "estimated_timeline": {
        "summary": "X-Y hari kerja total",
        "minimum_days": 14,
        "maximum_days": 60,
        "critical_path": [
            "Tahap 1: NIB + NPWP (1-3 hari)",
            "Tahap 2: KKPR/Persetujuan Lingkungan (14-30 hari)",
            "Tahap 3: PBG + SLF (14-28 hari)",
            "Tahap 4: Sertifikat Standar / Izin Sektoral (7-14 hari)"
        ]
    },
    "complexity_score": 7.5,
    "risk_factors": ["Faktor risiko spesifik 1"],
    "risk_assessment": {
        "level": "low|medium|high",
        "factors": ["Risiko kepatuhan 1"],
        "mitigation": ["Langkah mitigasi 1"],
        "common_pitfalls": ["Kesalahan umum yang perlu dihindari 1"]
    },
    "required_documents": ["Dokumen yang harus disiapkan 1"],
    "next_steps": ["Langkah konkrit 1"],
    "limitations": "Disclaimer analisis"
}

BIAYA GUIDELINES (per Februari 2026) — GUNAKAN DATA INI SEBAGAI ACUAN UTAMA:

1. **Biaya Pemerintah** (PNBP, retribusi daerah, biaya resmi):
   - NIB: Rp 0 (GRATIS via OSS RBA)
   - NPWP Badan: Rp 0 (GRATIS via online DJP)
   - Konfirmasi KKPR: Rp 0 (risiko rendah, otomatis via OSS)
   - Persetujuan KKPR/PKKPR: Rp 500rb - 2jt (risiko menengah-tinggi)
   - SPPL: Rp 0 (surat pernyataan, risiko rendah)
   - Sertifikat Standar: Rp 0 - 500rb
   - PBG (Persetujuan Bangunan Gedung): Rp 1jt - 2jt (retribusi, tergantung luas & zona)
   - SLF (Sertifikat Laik Fungsi): Rp 500rb - 2jt
   - Izin Lingkungan (UKL-UPL): Rp 1jt - 5jt
   - Izin Lingkungan (AMDAL): Rp 10jt - 100jt (tergantung skala proyek)
   - TPS Limbah B3: Rp 500rb - 1jt
   - Izin Pengelolaan Limbah B3: Rp 1jt - 5jt (PNBP KLHK)
   - Izin Pengangkutan Limbah B3: Rp 2jt - 10jt (KemenLHK)

2. **Biaya Konsultan BizMark** (jasa pendampingan profesional):
   - NIB + NPWP (Paket Dasar): Rp 1,5jt - 3jt
   - KKPR/PKKPR: Rp 3jt - 7jt
   - Sertifikat Standar: Rp 3jt - 7jt
   - PBG: Rp 5jt - 8jt (standar), Rp 8jt - 20jt (besar/kompleks)
   - SLF: Rp 3jt - 8jt
   - SPPL: Rp 1jt - 2jt
   - UKL-UPL: Rp 10jt - 25jt (termasuk penyusunan dokumen)
   - AMDAL: Rp 50jt - 150jt (studi, penyusunan, sidang komisi)
   - TPS Limbah B3: Rp 7jt - 12jt
   - Izin Pengelolaan Limbah B3 lengkap: Rp 15jt - 50jt
   - Izin Sektoral umum: Rp 3jt - 15jt

   Scale multipliers (kalikan pada biaya konsultan):
   * Mikro (<10 karyawan): 1.0x
   * Kecil (10-50): 1.3x
   * Menengah (50-100): 1.8x
   * Besar (>100): 2.5x

   Location multipliers:
   * DKI Jakarta, Surabaya, Bandung, Medan: 1.3x
   * Kota besar lain: 1.1x
   * Kabupaten/kota kecil: 1.0x
   * Pedesaan: 0.85x

3. **Total = Biaya Pemerintah + Biaya Konsultan**

CONTOH ALUR PERIZINAN 2026 (urutan wajib diikuti):
- Real Estate (68111): NIB → NPWP → PKKPR → UKL-UPL/AMDAL → PBG → SLF → Sertifikat Standar (7+ izin)
- Industri Limbah B3: NIB → NPWP → PKKPR → AMDAL → TPS-LB3 → Izin Pengelolaan B3 → PBG → SLF (8+ izin)
- Restoran sederhana: NIB → NPWP → SPPL → Sertifikat Standar → PBG (5 izin)
- Perdagangan online: NIB → NPWP → Sertifikat Standar (3 izin)
- Konstruksi: NIB → NPWP → SBU Konstruksi → PKKPR → UKL-UPL/AMDAL → PBG → SLF → Sertifikat Standar (8+ izin)

RULES KETAT:
1. Rekomendasikan 3-12 izin berdasarkan kompleksitas (3-5 untuk usaha sederhana, 6-12 untuk industri/konstruksi)
2. Pisahkan biaya pemerintah dan biaya konsultan untuk SETIAP izin
3. JANGAN rekomendasikan SIUP, TDP, IUI, IMB, HO, Izin Gangguan, Izin Lokasi lama
4. NIB SELALU pertama, NPWP SELALU kedua
5. KKPR/PKKPR SEBELUM AMDAL/UKL-UPL (tata ruang dulu, baru lingkungan)
6. PBG SEBELUM SLF (bangun dulu, baru laik fungsi)
7. Persetujuan lingkungan SEBELUM PBG (lingkungan dulu, baru bangun)
8. Sertifikat Standar/Izin SETELAH semua prasyarat teknis terpenuhi
9. Complexity score: 1-10
10. OUTPUT HARUS VALID JSON — tidak boleh ada teks diluar JSON
11. Dasar hukum HARUS mereferensikan regulasi yang MASIH BERLAKU di 2026
12. WAJIB isi prerequisites dan triggers_next (gunakan array kosong [] jika tidak ada)
13. WAJIB isi risk_assessment dengan factors, mitigation, dan common_pitfalls
14. WAJIB isi estimated_timeline dengan minimum_days, maximum_days, critical_path
PROMPT;
    }

    /**
     * Build user prompt from form data - enriched with all available context
     */
    private function buildPrompt(array $formData): string
    {
        $kbli = $formData['kbli_code'] ?? null;
        $kbliDesc = $formData['kbli_description'] ?? '';
        $businessActivity = $formData['business_activity'] ?? 'Tidak disebutkan';
        $scale = $this->translateScale($formData['business_scale'] ?? 'unknown');
        $province = $formData['location_province'] ?? 'Tidak disebutkan';
        $city = $formData['location_city'] ?? '';
        $locationCategory = $this->translateLocationCategory($formData['location_category'] ?? 'unknown');
        $investment = $this->translateInvestment($formData['estimated_investment'] ?? 'unknown');
        $companyType = $this->translateCompanyType($formData['company_type'] ?? '');
        $timeline = $this->translateTimeline($formData['timeline'] ?? '');
        $additionalNotes = $formData['additional_notes'] ?? '';

        $kbliLine = $kbli ? "- Kode KBLI: {$kbli}" . ($kbliDesc ? " ({$kbliDesc})" : '') : '- Kode KBLI: Belum ditentukan (tolong sarankan kode KBLI 5 digit yang paling sesuai)';
        
        $location = $city ? "{$city}, {$province}" : $province;

        $prompt = <<<PROMPT
Analisis kebutuhan perizinan untuk usaha berikut:

PROFIL USAHA:
- Aktivitas Bisnis: {$businessActivity}
{$kbliLine}
- Badan Usaha: {$companyType}
- Skala Usaha: {$scale}
- Lokasi: {$location} ({$locationCategory})
- Estimasi Investasi: {$investment}
PROMPT;

        if ($timeline) {
            $prompt .= "\n- Target Timeline: {$timeline}";
        }

        if ($additionalNotes) {
            $prompt .= "\n\nCATATAN TAMBAHAN:\n{$additionalNotes}";
        }

        $prompt .= "\n\nBerikan rekomendasi perizinan yang SPESIFIK dan AKURAT untuk jenis usaha ini dalam format JSON yang diminta. Pastikan biaya pemerintah dan biaya konsultan terpisah jelas.";

        return $prompt;
    }

    /**
     * Parse AI response (handle JSON, markdown-wrapped JSON, and edge cases)
     */
    private function parseResponse(string $content): array
    {
        // Clean up common issues
        $content = trim($content);
        
        // Try direct JSON parse
        $json = json_decode($content, true);
        if ($json !== null && is_array($json)) {
            return $json;
        }

        // Try extracting JSON from markdown code block
        if (preg_match('/```(?:json)?\s*(\{.+\})\s*```/s', $content, $matches)) {
            $json = json_decode($matches[1], true);
            if ($json !== null && is_array($json)) {
                return $json;
            }
        }

        // Try finding first { to last } as JSON
        $firstBrace = strpos($content, '{');
        $lastBrace = strrpos($content, '}');
        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $jsonStr = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
            $json = json_decode($jsonStr, true);
            if ($json !== null && is_array($json)) {
                return $json;
            }
        }

        // If all parsing fails, log the full content for debugging
        Log::warning('FreeAIAnalysisService: Could not parse AI response', [
            'content_length' => strlen($content),
            'content_preview' => substr($content, 0, 500),
        ]);

        throw new \Exception('Failed to parse AI response as JSON. Content length: ' . strlen($content));
    }

    /**
     * Validate and enrich the AI analysis to ensure complete data structure
     */
    private function validateAndEnrichAnalysis(array $analysis, array $formData): array
    {
        // Ensure recommended_permits exists and is an array
        if (!isset($analysis['recommended_permits']) || !is_array($analysis['recommended_permits'])) {
            $analysis['recommended_permits'] = [];
        }

        // Validate each permit has required fields
        foreach ($analysis['recommended_permits'] as &$permit) {
            $permit['code'] = $permit['code'] ?? 'UNKNOWN';
            $permit['name'] = $permit['name'] ?? 'Izin Tidak Teridentifikasi';
            $permit['priority'] = in_array($permit['priority'] ?? '', ['critical', 'high', 'medium', 'low']) 
                ? $permit['priority'] : 'medium';
            $permit['category'] = in_array($permit['category'] ?? '', ['foundational', 'environmental', 'technical', 'operational', 'sectoral'])
                ? $permit['category'] : 'operational';
            $permit['estimated_timeline'] = $permit['estimated_timeline'] ?? '7-14 hari kerja';
            $permit['description'] = $permit['description'] ?? '';
            $permit['prerequisites'] = $permit['prerequisites'] ?? [];
            $permit['triggers_next'] = $permit['triggers_next'] ?? [];
            
            // Map 'type' for backward compatibility with ConsultationPricingEngine
            if (!isset($permit['type'])) {
                $permit['type'] = match($permit['priority']) {
                    'critical' => 'mandatory',
                    'high' => 'mandatory',
                    'medium' => 'recommended',
                    default => 'conditional',
                };
            }
            
            // Ensure fee structures exist
            if (!isset($permit['government_fee']) || !is_array($permit['government_fee'])) {
                $permit['government_fee'] = ['min' => 0, 'max' => 0, 'note' => 'Estimasi'];
            }
            if (!isset($permit['consultant_fee']) || !is_array($permit['consultant_fee'])) {
                $permit['consultant_fee'] = ['min' => 1500000, 'max' => 3000000, 'note' => 'Estimasi'];
            }
            
            // Calculate total_cost_range if missing
            if (empty($permit['total_cost_range'])) {
                $totalMin = ($permit['government_fee']['min'] ?? 0) + ($permit['consultant_fee']['min'] ?? 0);
                $totalMax = ($permit['government_fee']['max'] ?? 0) + ($permit['consultant_fee']['max'] ?? 0);
                $permit['total_cost_range'] = $this->formatCostRange($totalMin, $totalMax);
            }
        }
        unset($permit); // break reference from foreach

        // === PERMIT ORDERING: Sort by category phase → priority → dependency chain ===
        $categoryOrder = [
            'foundational' => 0,
            'environmental' => 1,
            'technical' => 2,
            'operational' => 3,
            'sectoral' => 4,
        ];
        $priorityOrder = [
            'critical' => 0,
            'high' => 1,
            'medium' => 2,
            'low' => 3,
        ];

        usort($analysis['recommended_permits'], function ($a, $b) use ($categoryOrder, $priorityOrder) {
            $catA = $categoryOrder[$a['category'] ?? 'operational'] ?? 3;
            $catB = $categoryOrder[$b['category'] ?? 'operational'] ?? 3;
            if ($catA !== $catB) return $catA - $catB;

            $priA = $priorityOrder[$a['priority'] ?? 'medium'] ?? 2;
            $priB = $priorityOrder[$b['priority'] ?? 'medium'] ?? 2;
            if ($priA !== $priB) return $priA - $priB;

            return 0;
        });

        // Topological sort: ensure prerequisites appear before dependents
        $permitsByName = [];
        foreach ($analysis['recommended_permits'] as $idx => $p) {
            $permitsByName[$p['name']] = $idx;
            if (!empty($p['code'])) {
                $permitsByName[$p['code']] = $idx;
            }
        }

        $sorted = [];
        $visited = [];
        $permits = $analysis['recommended_permits'];

        $addPermit = function (int $idx) use (&$addPermit, &$sorted, &$visited, &$permits, &$permitsByName) {
            if (isset($visited[$idx])) return;
            $visited[$idx] = true;

            // Recursively add prerequisites first
            foreach ($permits[$idx]['prerequisites'] ?? [] as $prereqName) {
                foreach ($permitsByName as $name => $prereqIdx) {
                    if ($prereqIdx !== $idx && (
                        stripos($name, $prereqName) !== false ||
                        stripos($prereqName, $name) !== false
                    )) {
                        $addPermit($prereqIdx);
                        break;
                    }
                }
            }

            $sorted[] = $permits[$idx];
        };

        foreach ($permits as $idx => $p) {
            $addPermit($idx);
        }

        if (count($sorted) === count($permits)) {
            $analysis['recommended_permits'] = $sorted;
        }

        // Add ordering metadata (urutan) to each permit
        foreach ($analysis['recommended_permits'] as $idx => &$permit) {
            $permit['order'] = $idx + 1;
        }
        unset($permit);

        // Ensure total_estimated_cost exists with proper structure
        if (!isset($analysis['total_estimated_cost']) || !is_array($analysis['total_estimated_cost'])) {
            // Calculate from permits
            $govMin = $govMax = $conMin = $conMax = 0;
            foreach ($analysis['recommended_permits'] as $permit) {
                $govMin += $permit['government_fee']['min'] ?? 0;
                $govMax += $permit['government_fee']['max'] ?? 0;
                $conMin += $permit['consultant_fee']['min'] ?? 0;
                $conMax += $permit['consultant_fee']['max'] ?? 0;
            }
            $analysis['total_estimated_cost'] = [
                'government_fees' => ['min' => $govMin, 'max' => $govMax],
                'consultant_fees' => ['min' => $conMin, 'max' => $conMax],
                'grand_total' => ['min' => $govMin + $conMin, 'max' => $govMax + $conMax],
                'currency' => 'IDR',
            ];
        }

        // Ensure grand_total exists within total_estimated_cost
        if (!isset($analysis['total_estimated_cost']['grand_total'])) {
            $govMin = $analysis['total_estimated_cost']['government_fees']['min'] ?? 0;
            $govMax = $analysis['total_estimated_cost']['government_fees']['max'] ?? 0;
            $conMin = $analysis['total_estimated_cost']['consultant_fees']['min'] ?? 0;
            $conMax = $analysis['total_estimated_cost']['consultant_fees']['max'] ?? 0;
            $analysis['total_estimated_cost']['grand_total'] = [
                'min' => $govMin + $conMin,
                'max' => $govMax + $conMax,
            ];
        }

        // Set defaults for required fields
        $analysis['total_estimated_timeline'] = $analysis['total_estimated_timeline'] ?? '14-30 hari kerja';
        $analysis['complexity_score'] = is_numeric($analysis['complexity_score'] ?? null) 
            ? min(10, max(1, (float)$analysis['complexity_score'])) : 5.0;
        $analysis['risk_factors'] = $analysis['risk_factors'] ?? [];
        $analysis['next_steps'] = $analysis['next_steps'] ?? [];
        $analysis['required_documents'] = $analysis['required_documents'] ?? [];
        $analysis['risk_classification'] = $analysis['risk_classification'] ?? 'menengah_rendah';
        $analysis['limitations'] = $analysis['limitations'] ?? 'Analisis ini bersifat umum berdasarkan informasi yang diberikan. Untuk analisis detail dengan dokumen checklist lengkap, pendampingan konsultan bersertifikat, dan akses portal monitoring, silakan daftar ke portal BizMark.ID.';

        // Build risk_assessment for backward compatibility with ConsultationPricingEngine
        if (!isset($analysis['risk_assessment'])) {
            $riskLevel = match($analysis['risk_classification'] ?? 'menengah_rendah') {
                'rendah' => 'low',
                'menengah_rendah' => 'medium',
                'menengah_tinggi' => 'high',
                'tinggi' => 'high',
                default => 'medium',
            };
            $analysis['risk_assessment'] = [
                'level' => $riskLevel,
                'factors' => $analysis['risk_factors'],
                'mitigation' => [],
                'common_pitfalls' => [],
            ];
        }

        // Ensure risk_assessment has mitigation and common_pitfalls arrays
        if (empty($analysis['risk_assessment']['mitigation'])) {
            $analysis['risk_assessment']['mitigation'] = [
                'Konsultasikan dengan konsultan perizinan bersertifikat sebelum memulai proses',
                'Siapkan seluruh dokumen persyaratan secara lengkap sebelum pengajuan',
                'Pastikan kepatuhan terhadap peraturan daerah (Perda) setempat',
                'Monitor perubahan regulasi yang mungkin mempengaruhi proses perizinan',
            ];
        }
        if (empty($analysis['risk_assessment']['common_pitfalls'])) {
            $analysis['risk_assessment']['common_pitfalls'] = [
                'Dokumen tidak lengkap saat pengajuan sehingga terjadi penolakan/revisi',
                'Tidak memperhatikan urutan perolehan izin (dependency chain)',
                'Menggunakan format izin lama (SIUP/TDP/IMB) yang sudah tidak berlaku',
                'Tidak memperbarui NIB setelah ada perubahan data usaha',
            ];
        }

        // Build estimated_timeline object with enriched structure
        if (!isset($analysis['estimated_timeline']) || !is_array($analysis['estimated_timeline'])) {
            $analysis['estimated_timeline'] = [
                'summary' => $analysis['total_estimated_timeline'] ?? '14-30 hari kerja',
            ];
        }

        // Enrich timeline with minimum_days, maximum_days if missing
        if (!isset($analysis['estimated_timeline']['minimum_days']) || !isset($analysis['estimated_timeline']['maximum_days'])) {
            $totalMinDays = 0;
            $totalMaxDays = 0;
            foreach ($analysis['recommended_permits'] as $p) {
                $timeline = $p['estimated_timeline'] ?? '';
                if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int)$m[1];
                    $totalMaxDays += (int)$m[2];
                } elseif (preg_match('/(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int)$m[1];
                    $totalMaxDays += (int)$m[1];
                }
            }
            if ($totalMinDays > 0) {
                $analysis['estimated_timeline']['minimum_days'] = $totalMinDays;
                $analysis['estimated_timeline']['maximum_days'] = $totalMaxDays;
                $analysis['estimated_timeline']['summary'] = "{$totalMinDays}-{$totalMaxDays} hari kerja";
                $analysis['total_estimated_timeline'] = $analysis['estimated_timeline']['summary'];
            }
        }

        // Build critical_path from dependency-ordered permits if missing
        if (!isset($analysis['estimated_timeline']['critical_path'])) {
            $criticalPath = [];
            foreach ($analysis['recommended_permits'] as $p) {
                if (in_array($p['priority'] ?? '', ['critical', 'high'])) {
                    $criticalPath[] = $p['name'] . ' (' . ($p['estimated_timeline'] ?? '?') . ')';
                }
            }
            $analysis['estimated_timeline']['critical_path'] = $criticalPath;
        }

        return $analysis;
    }

    /**
     * Get fallback analysis if AI fails - contextually aware based on form data
     * Updated for 2026 regulatory framework (UU 6/2023, PP 5/2021, PP 22/2021, PP 16/2021, PP 21/2021)
     */
    private function getFallbackAnalysis(array $formData): array
    {
        $scale = $formData['business_scale'] ?? 'small';
        $investment = $formData['estimated_investment'] ?? 'under_100m';
        $locationCategory = $formData['location_category'] ?? 'commercial';
        $businessActivity = $formData['business_activity'] ?? '';
        
        // Determine scale multiplier for costs
        $scaleMultiplier = match($scale) {
            'micro' => 1.0,
            'small' => 1.3,
            'medium' => 1.8,
            'large' => 2.5,
            default => 1.0,
        };

        // Determine if environmental permits are likely needed
        $needsEnvironmental = $this->likelyNeedsEnvironmental($businessActivity, $investment);
        $needsPBG = $this->likelyNeedsPBG($businessActivity, $locationCategory);
        $needsAMDAL = $this->likelyNeedsAMDAL($businessActivity, $investment, $scale);
        $needsB3 = $this->likelyNeedsB3($businessActivity);

        // ====== TAHAP 1: LEGALITAS DASAR (Foundational) ======
        $permits = [
            [
                'code' => 'OSS_NIB',
                'name' => 'Nomor Induk Berusaha (NIB) via OSS RBA',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (pendaftaran online via OSS)'
                ],
                'consultant_fee' => [
                    'min' => (int)(1500000 * $scaleMultiplier),
                    'max' => (int)(3000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pendaftaran dan verifikasi data'
                ],
                'total_cost_range' => $this->formatCostRange(1500000 * $scaleMultiplier, 3000000 * $scaleMultiplier),
                'description' => 'Identitas tunggal pelaku usaha yang diterbitkan melalui OSS RBA. Wajib untuk semua jenis dan skala usaha di Indonesia sesuai UU Cipta Kerja.',
                'issuing_authority' => 'Kementerian Investasi/BKPM via OSS',
                'legal_basis' => 'UU 6/2023 (Cipta Kerja), PP 5/2021 tentang Perizinan Berusaha Berbasis Risiko',
                'prerequisites' => [],
                'triggers_next' => ['NPWP Badan Usaha', 'PKKPR/KKPR']
            ],
            [
                'code' => 'NPWP_BADAN',
                'name' => 'NPWP Badan Usaha',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (pendaftaran online via Coretax/e-Registration)'
                ],
                'consultant_fee' => [
                    'min' => (int)(1000000 * $scaleMultiplier),
                    'max' => (int)(1500000 * $scaleMultiplier),
                    'note' => 'Pendampingan registrasi dan pengaturan pajak'
                ],
                'total_cost_range' => $this->formatCostRange(1000000 * $scaleMultiplier, 1500000 * $scaleMultiplier),
                'description' => 'Nomor Pokok Wajib Pajak untuk badan usaha. Diperlukan untuk kewajiban perpajakan dan transaksi bisnis.',
                'issuing_authority' => 'Direktorat Jenderal Pajak (Coretax)',
                'legal_basis' => 'UU 6/2023, UU 7/2021 tentang Harmonisasi Peraturan Perpajakan',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['PKKPR/KKPR', 'Sertifikat Standar']
            ],
        ];

        // ====== TAHAP 2: TATA RUANG (Spatial Planning) ======
        // PKKPR for commercial/industrial locations or larger scale
        $needsPKKPR = in_array($locationCategory, ['industrial', 'commercial']) || in_array($scale, ['medium', 'large']);
        if ($needsPKKPR) {
            $permits[] = [
                'code' => 'PKKPR',
                'name' => 'Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang (PKKPR)',
                'priority' => 'critical',
                'category' => 'foundational',
                'type' => 'mandatory',
                'estimated_timeline' => '5-14 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 500000,
                    'note' => 'Biaya administrasi (gratis jika via OSS untuk risiko rendah-menengah)'
                ],
                'consultant_fee' => [
                    'min' => (int)(2000000 * $scaleMultiplier),
                    'max' => (int)(5000000 * $scaleMultiplier),
                    'note' => 'Pendampingan analisis kesesuaian tata ruang'
                ],
                'total_cost_range' => $this->formatCostRange(2000000 * $scaleMultiplier, 5500000 * $scaleMultiplier),
                'description' => 'Persetujuan kesesuaian lokasi usaha dengan Rencana Tata Ruang Wilayah (RTRW/RDTR). Pengganti Izin Lokasi lama. Wajib sebelum mengurus izin lingkungan dan bangunan.',
                'issuing_authority' => 'Pemerintah Daerah / ATR-BPN via OSS',
                'legal_basis' => 'PP 21/2021 tentang Penyelenggaraan Penataan Ruang, UU 6/2023',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['AMDAL/UKL-UPL/SPPL', 'PBG']
            ];
        }

        // ====== TAHAP 3: LINGKUNGAN HIDUP (Environmental) ======
        if ($needsAMDAL) {
            $permits[] = [
                'code' => 'AMDAL',
                'name' => 'Analisis Mengenai Dampak Lingkungan (AMDAL)',
                'priority' => 'critical',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '60-120 hari kerja',
                'government_fee' => [
                    'min' => 10000000,
                    'max' => (int)(100000000 * ($scale === 'large' ? 1.0 : 0.5)),
                    'note' => 'Biaya evaluasi, sidang komisi AMDAL, dan PNBP KLHK'
                ],
                'consultant_fee' => [
                    'min' => (int)(50000000 * $scaleMultiplier),
                    'max' => (int)(150000000 * $scaleMultiplier),
                    'note' => 'Studi AMDAL lengkap: ANDAL, RKL-RPL, sidang komisi penilai'
                ],
                'total_cost_range' => $this->formatCostRange(60000000 * $scaleMultiplier, 250000000 * $scaleMultiplier),
                'description' => 'Dokumen kajian dampak lingkungan wajib untuk usaha berisiko tinggi. Mencakup studi ANDAL, RKL-RPL, dan sidang Komisi Penilai AMDAL.',
                'issuing_authority' => 'Kementerian LHK / Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => $needsPKKPR ? ['Nomor Induk Berusaha (NIB)', 'PKKPR'] : ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['PBG', 'Sertifikat Standar']
            ];
        } elseif ($needsEnvironmental) {
            $permits[] = [
                'code' => 'UKL_UPL',
                'name' => 'Upaya Pengelolaan Lingkungan Hidup (UKL-UPL)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 5000000,
                    'note' => 'Biaya pemeriksaan dan evaluasi dokumen DLH'
                ],
                'consultant_fee' => [
                    'min' => (int)(10000000 * $scaleMultiplier),
                    'max' => (int)(25000000 * $scaleMultiplier),
                    'note' => 'Penyusunan dokumen UKL-UPL dan pendampingan'
                ],
                'total_cost_range' => $this->formatCostRange(11000000 * $scaleMultiplier, 30000000 * $scaleMultiplier),
                'description' => 'Dokumen pengelolaan lingkungan untuk usaha risiko menengah (dampak lingkungan sedang). Wajib sebelum operasional.',
                'issuing_authority' => 'Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => $needsPKKPR ? ['Nomor Induk Berusaha (NIB)', 'PKKPR'] : ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['Sertifikat Standar', 'PBG']
            ];
        } else {
            // Low-risk: SPPL (Surat Pernyataan Pengelolaan Lingkungan)
            $permits[] = [
                'code' => 'SPPL',
                'name' => 'Surat Pernyataan Pengelolaan Lingkungan (SPPL)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '1-3 hari kerja',
                'government_fee' => [
                    'min' => 0,
                    'max' => 0,
                    'note' => 'Gratis (terintegrasi dalam OSS untuk risiko rendah)'
                ],
                'consultant_fee' => [
                    'min' => (int)(500000 * $scaleMultiplier),
                    'max' => (int)(1500000 * $scaleMultiplier),
                    'note' => 'Pendampingan penyusunan SPPL'
                ],
                'total_cost_range' => $this->formatCostRange(500000 * $scaleMultiplier, 1500000 * $scaleMultiplier),
                'description' => 'Dokumen pernyataan pengelolaan lingkungan untuk usaha risiko rendah. Diterbitkan otomatis melalui OSS bersamaan dengan NIB.',
                'issuing_authority' => 'OSS / Dinas Lingkungan Hidup',
                'legal_basis' => 'UU 6/2023, PP 22/2021 tentang Perlindungan dan Pengelolaan Lingkungan Hidup',
                'prerequisites' => ['Nomor Induk Berusaha (NIB)'],
                'triggers_next' => ['Sertifikat Standar']
            ];
        }

        // Add B3 (hazardous waste) permits if needed
        if ($needsB3) {
            $permits[] = [
                'code' => 'TPS_LB3',
                'name' => 'Izin Tempat Penyimpanan Sementara Limbah B3 (TPS-LB3)',
                'priority' => 'high',
                'category' => 'environmental',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 500000,
                    'max' => 1000000,
                    'note' => 'Biaya verifikasi teknis fasilitas TPS'
                ],
                'consultant_fee' => [
                    'min' => (int)(7000000 * $scaleMultiplier),
                    'max' => (int)(12000000 * $scaleMultiplier),
                    'note' => 'Penyusunan SOP, desain TPS, verifikasi fasilitas'
                ],
                'total_cost_range' => $this->formatCostRange(7500000 * $scaleMultiplier, 13000000 * $scaleMultiplier),
                'description' => 'Izin penyimpanan sementara limbah Bahan Berbahaya dan Beracun (B3) sebelum diangkut ke fasilitas pengolahan.',
                'issuing_authority' => 'Dinas Lingkungan Hidup / KemenLHK',
                'legal_basis' => 'UU 6/2023, PP 22/2021, Permen LHK 6/2021',
                'prerequisites' => [$needsAMDAL ? 'AMDAL' : 'UKL-UPL'],
                'triggers_next' => ['Izin Pengelolaan Limbah B3']
            ];
            $permits[] = [
                'code' => 'IZIN_KELOLA_B3',
                'name' => 'Izin Pengelolaan Limbah B3',
                'priority' => 'high',
                'category' => 'sectoral',
                'type' => 'mandatory',
                'estimated_timeline' => '30-60 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 5000000,
                    'note' => 'PNBP KemenLHK untuk evaluasi pengelolaan B3'
                ],
                'consultant_fee' => [
                    'min' => (int)(15000000 * $scaleMultiplier),
                    'max' => (int)(50000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengurusan izin pengelolaan B3 lengkap'
                ],
                'total_cost_range' => $this->formatCostRange(16000000 * $scaleMultiplier, 55000000 * $scaleMultiplier),
                'description' => 'Izin untuk pengumpulan, penyimpanan, dan pengolahan limbah B3. Wajib untuk semua kegiatan yang menghasilkan atau mengelola limbah B3.',
                'issuing_authority' => 'Kementerian Lingkungan Hidup dan Kehutanan',
                'legal_basis' => 'UU 6/2023, PP 22/2021, Permen LHK 6/2021',
                'prerequisites' => ['TPS-LB3', $needsAMDAL ? 'AMDAL' : 'UKL-UPL'],
                'triggers_next' => []
            ];
        }

        // ====== TAHAP 4: TEKNIS & BANGUNAN ======
        if ($needsPBG) {
            $permits[] = [
                'code' => 'PBG',
                'name' => 'Persetujuan Bangunan Gedung (PBG)',
                'priority' => 'high',
                'category' => 'technical',
                'type' => 'mandatory',
                'estimated_timeline' => '14-28 hari kerja',
                'government_fee' => [
                    'min' => 1000000,
                    'max' => 2000000,
                    'note' => 'Retribusi PBG sesuai Perda (tergantung luas & zona)'
                ],
                'consultant_fee' => [
                    'min' => (int)(5000000 * $scaleMultiplier),
                    'max' => (int)(8000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengajuan PBG via SIMBG dan kelengkapan teknis'
                ],
                'total_cost_range' => $this->formatCostRange(6000000 * $scaleMultiplier, 10000000 * $scaleMultiplier),
                'description' => 'Persetujuan dari pemerintah daerah untuk mendirikan atau merenovasi bangunan. Pengganti IMB sesuai UU Cipta Kerja. Diajukan melalui SIMBG.',
                'issuing_authority' => 'Dinas PU / SIMBG',
                'legal_basis' => 'UU 6/2023, PP 16/2021 tentang Bangunan Gedung',
                'prerequisites' => $needsPKKPR
                    ? ['Nomor Induk Berusaha (NIB)', 'PKKPR', $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL')]
                    : ['Nomor Induk Berusaha (NIB)', $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL')],
                'triggers_next' => ['Sertifikat Laik Fungsi (SLF)']
            ];

            // SLF after PBG
            $permits[] = [
                'code' => 'SLF',
                'name' => 'Sertifikat Laik Fungsi (SLF)',
                'priority' => 'high',
                'category' => 'technical',
                'type' => 'mandatory',
                'estimated_timeline' => '14-30 hari kerja',
                'government_fee' => [
                    'min' => 500000,
                    'max' => 2000000,
                    'note' => 'Biaya pemeriksaan kelaikan fungsi bangunan'
                ],
                'consultant_fee' => [
                    'min' => (int)(3000000 * $scaleMultiplier),
                    'max' => (int)(7000000 * $scaleMultiplier),
                    'note' => 'Pendampingan pengujian kelaikan dan penerbitan SLF'
                ],
                'total_cost_range' => $this->formatCostRange(3500000 * $scaleMultiplier, 9000000 * $scaleMultiplier),
                'description' => 'Sertifikat yang menyatakan bangunan gedung telah laik fungsi untuk digunakan. Wajib dimiliki setelah PBG sebelum bangunan dioperasikan.',
                'issuing_authority' => 'Dinas PU / SIMBG',
                'legal_basis' => 'UU 6/2023, PP 16/2021 tentang Bangunan Gedung',
                'prerequisites' => ['Persetujuan Bangunan Gedung (PBG)'],
                'triggers_next' => ['Sertifikat Standar']
            ];
        }

        // ====== TAHAP 5: OPERASIONAL ======
        $permits[] = [
            'code' => 'SERTIFIKAT_STANDAR',
            'name' => 'Sertifikat Standar / Izin Usaha Berbasis Risiko',
            'priority' => 'high',
            'category' => 'operational',
            'type' => 'mandatory',
            'estimated_timeline' => '7-14 hari kerja',
            'government_fee' => [
                'min' => 0,
                'max' => 500000,
                'note' => 'Biaya verifikasi (jika diperlukan)'
            ],
            'consultant_fee' => [
                'min' => (int)(3000000 * $scaleMultiplier),
                'max' => (int)(7000000 * $scaleMultiplier),
                'note' => 'Penyusunan dokumen dan pendampingan perizinan'
            ],
            'total_cost_range' => $this->formatCostRange(3000000 * $scaleMultiplier, 7500000 * $scaleMultiplier),
            'description' => 'Izin operasional sesuai klasifikasi risiko usaha melalui OSS RBA. Untuk risiko menengah-tinggi, memerlukan verifikasi lapangan.',
            'issuing_authority' => 'Pemerintah Daerah / K/L Terkait via OSS',
            'legal_basis' => 'UU 6/2023, PP 5/2021, Perpres 10/2021',
            'prerequisites' => array_filter([
                'Nomor Induk Berusaha (NIB)',
                'NPWP Badan Usaha',
                $needsAMDAL ? 'AMDAL' : ($needsEnvironmental ? 'UKL-UPL' : 'SPPL'),
                $needsPBG ? 'Sertifikat Laik Fungsi (SLF)' : null,
            ]),
            'triggers_next' => []
        ];

        // Calculate totals
        $govMin = $govMax = $conMin = $conMax = 0;
        $totalMinDays = $totalMaxDays = 0;
        foreach ($permits as $permit) {
            $govMin += $permit['government_fee']['min'];
            $govMax += $permit['government_fee']['max'];
            $conMin += $permit['consultant_fee']['min'];
            $conMax += $permit['consultant_fee']['max'];
            // Parse timeline days
            if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $permit['estimated_timeline'], $m)) {
                $totalMinDays += (int)$m[1];
                $totalMaxDays += (int)$m[2];
            }
        }

        $complexity = count($permits) <= 3 ? 4.0 : (count($permits) <= 5 ? 6.0 : (count($permits) <= 7 ? 7.5 : 8.5));
        $riskClass = $needsAMDAL ? 'tinggi' : ($needsEnvironmental || $needsB3 ? 'menengah_tinggi' : 'menengah_rendah');
        $riskLevel = match($riskClass) {
            'rendah' => 'low',
            'menengah_rendah' => 'medium',
            'menengah_tinggi' => 'high',
            'tinggi' => 'high',
            default => 'medium',
        };

        $timelineSummary = $totalMinDays > 0
            ? "{$totalMinDays}-{$totalMaxDays} hari kerja"
            : ($needsAMDAL ? '90-180 hari kerja' : ($needsEnvironmental ? '30-60 hari kerja' : '14-30 hari kerja'));

        // Build critical path from critical/high priority permits
        $criticalPath = [];
        foreach ($permits as $p) {
            if (in_array($p['priority'], ['critical', 'high'])) {
                $criticalPath[] = $p['name'] . ' (' . $p['estimated_timeline'] . ')';
            }
        }

        return [
            'recommended_permits' => $permits,
            'risk_classification' => $riskClass,
            'kbli_suggestion' => [
                'code' => null,
                'description' => 'Tidak dapat menentukan KBLI secara otomatis. Konsultasi lebih lanjut diperlukan.',
                'confidence' => 'low',
            ],
            'total_estimated_cost' => [
                'government_fees' => ['min' => $govMin, 'max' => $govMax],
                'consultant_fees' => ['min' => $conMin, 'max' => $conMax],
                'grand_total' => ['min' => $govMin + $conMin, 'max' => $govMax + $conMax],
                'currency' => 'IDR'
            ],
            'total_estimated_timeline' => $timelineSummary,
            'estimated_timeline' => [
                'summary' => $timelineSummary,
                'minimum_days' => $totalMinDays ?: null,
                'maximum_days' => $totalMaxDays ?: null,
                'critical_path' => $criticalPath,
            ],
            'complexity_score' => $complexity,
            'risk_assessment' => [
                'level' => $riskLevel,
                'factors' => [
                    'Analisis ini menggunakan estimasi umum berdasarkan regulasi 2026 - konsultasi langsung diperlukan untuk akurasi',
                    'Persyaratan spesifik dapat bervariasi berdasarkan peraturan daerah (Perda) setempat',
                    'Timeline dapat berubah tergantung kelengkapan dokumen dan antrian instansi',
                ],
                'mitigation' => [
                    'Konsultasikan dengan konsultan perizinan bersertifikat sebelum memulai proses',
                    'Siapkan seluruh dokumen persyaratan secara lengkap sebelum pengajuan',
                    'Pastikan kepatuhan terhadap RTRW/RDTR daerah setempat',
                    'Monitor perubahan regulasi terkait UU Cipta Kerja dan turunannya',
                ],
                'common_pitfalls' => [
                    'Dokumen tidak lengkap saat pengajuan sehingga terjadi penolakan/revisi',
                    'Tidak memperhatikan urutan perolehan izin (dependency chain)',
                    'Menggunakan format izin lama (SIUP/TDP/IMB/HO) yang sudah tidak berlaku',
                    'Tidak mengurus PKKPR sebelum izin lingkungan dan PBG',
                    'Tidak memperbarui NIB setelah ada perubahan data usaha',
                ],
            ],
            'risk_factors' => [
                'Analisis ini menggunakan estimasi umum berdasarkan regulasi 2026 - konsultasi langsung diperlukan untuk akurasi',
                'Persyaratan spesifik dapat bervariasi berdasarkan peraturan daerah (Perda) setempat',
                'Timeline dapat berubah tergantung kelengkapan dokumen dan antrian instansi',
            ],
            'required_documents' => [
                'KTP Pengurus/Pemilik (e-KTP yang masih berlaku)',
                'Akta Pendirian dan perubahannya (jika badan usaha)',
                'SK Kemenkumham / AHU Online (untuk PT/CV)',
                'NPWP Pribadi Pengurus/Pemilik',
                'Bukti kepemilikan/sewa tempat usaha (SHM/SHGB/Sewa)',
                'Denah dan foto lokasi usaha',
                'Surat kuasa (jika diwakilkan)',
                'Peta lokasi dan koordinat GPS',
            ],
            'next_steps' => [
                'Siapkan dokumen legalitas perusahaan (KTP, Akta, SK Kemenkumham)',
                'Tentukan kode KBLI 5 digit yang sesuai dengan konsultan BizMark',
                'Periksa kesesuaian lokasi usaha dengan RTRW/RDTR setempat',
                'Daftar NIB melalui OSS RBA dengan pendampingan konsultan',
                'Urus dokumen lingkungan sesuai klasifikasi risiko usaha',
                'Daftar ke portal BizMark.ID untuk analisis detail dan pendampingan lengkap',
            ],
            'limitations' => 'Ini adalah analisis estimasi otomatis berdasarkan regulasi 2026 (UU 6/2023, PP 5/2021). AI tidak dapat menganalisis saat ini, sehingga hasil berdasarkan template umum. Untuk analisis detail, timeline breakdown, dan pendampingan konsultan bersertifikat, silakan daftar ke portal BizMark.ID.',
            'ai_model_used' => 'fallback-v3',
            'ai_tokens_used' => 0,
            'ai_processing_time' => 0,
            'generated_at' => now()->toIso8601String(),
            'version' => '3.0-fallback-2026',
            'cached' => false
        ];
    }

    /**
     * Check if business likely needs AMDAL (high-impact environmental assessment)
     * AMDAL required for: large-scale industry, B3/hazardous, mining, major construction, investment >2B
     */
    private function likelyNeedsAMDAL(string $activity, string $investment, string $scale): bool
    {
        // Large investment almost always needs AMDAL
        if ($investment === 'over_2b' && $scale === 'large') {
            return true;
        }

        $amdalKeywords = ['tambang', 'pertambangan', 'mining', 'smelter', 'kilang', 'refinery',
            'limbah b3', 'b3', 'hazardous', 'chemical', 'kimia berat', 'pupuk', 'pestisida',
            'petrokimia', 'pelabuhan', 'bandara', 'tol', 'bendungan', 'pltu', 'pltn',
            'nuklir', 'sawit besar', 'perkebunan besar', 'hutan'];

        foreach ($amdalKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if business likely deals with B3 (hazardous materials/waste)
     */
    private function likelyNeedsB3(string $activity): bool
    {
        $b3Keywords = ['limbah', 'b3', 'hazardous', 'toxic', 'beracun', 'berbahaya',
            'chemical', 'kimia', 'pestisida', 'pupuk kimia', 'electroplating',
            'galvanis', 'aki', 'baterai', 'oli bekas', 'smelter', 'pertambangan'];

        foreach ($b3Keywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if business likely needs environmental permits
     */
    private function likelyNeedsEnvironmental(string $activity, string $investment): bool
    {
        $envKeywords = ['pabrik', 'manufaktur', 'produksi', 'industri', 'pertambangan', 'mining',
            'konstruksi', 'pembangunan', 'chemical', 'kimia', 'limbah', 'pengolahan',
            'factory', 'manufacturing', 'tambang', 'sawit', 'kelapa sawit', 'perkebunan'];
        
        foreach ($envKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }
        
        // Large investments typically need environmental assessment
        return in_array($investment, ['500m_2b', 'over_2b']);
    }

    /**
     * Check if business likely needs PBG (building permit)
     */
    private function likelyNeedsPBG(string $activity, string $locationCategory): bool
    {
        $pbgKeywords = ['restoran', 'cafe', 'kafe', 'hotel', 'penginapan', 'gudang', 'warehouse',
            'pabrik', 'factory', 'toko', 'ruko', 'showroom', 'bengkel', 'workshop',
            'klinik', 'rumah sakit', 'hospital', 'gedung', 'building', 'mall'];
        
        foreach ($pbgKeywords as $keyword) {
            if (stripos($activity, $keyword) !== false) {
                return true;
            }
        }
        
        return in_array($locationCategory, ['industrial', 'commercial']);
    }

    /**
     * Format cost value with adaptive unit (rb/jt/M)
     */
    private function formatCost(float $value): string
    {
        if ($value >= 1000000000) {
            return 'Rp ' . number_format($value / 1000000000, 1, ',', '.') . 'M';
        } elseif ($value >= 1000000) {
            $formatted = number_format($value / 1000000, 1, ',', '.');
            $formatted = rtrim(rtrim($formatted, '0'), ',');
            return 'Rp ' . $formatted . ' Juta';
        } elseif ($value >= 1000) {
            return 'Rp ' . number_format($value / 1000, 0, ',', '.') . 'rb';
        } elseif ($value > 0) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        }
        return 'Rp 0';
    }

    /**
     * Format cost range with adaptive units
     */
    private function formatCostRange(float $min, float $max): string
    {
        if ($min == 0 && $max == 0) return 'Rp 0';
        if ($min == $max) return $this->formatCost($min);
        return $this->formatCost($min) . ' - ' . $this->formatCost($max);
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

    private function translateCompanyType(string $type): string
    {
        return match($type) {
            'PT' => 'PT (Perseroan Terbatas)',
            'CV' => 'CV (Commanditaire Vennootschap)',
            'Individual' => 'Perorangan',
            'Koperasi' => 'Koperasi',
            'Yayasan' => 'Yayasan',
            'Belum Terdaftar' => 'Belum Terdaftar / Baru Akan Mendirikan',
            default => 'Tidak disebutkan'
        };
    }

    private function translateTimeline(string $timeline): string
    {
        return match($timeline) {
            'urgent' => 'Urgent (< 1 bulan)',
            '1-3_months' => '1-3 bulan',
            '3-6_months' => '3-6 bulan',
            '6plus_months' => '> 6 bulan',
            'not_sure' => 'Belum pasti',
            default => ''
        };
    }
}
