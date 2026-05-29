<?php

namespace App\Services;

use App\Services\Analysis\CostFormatter;
use App\Services\Analysis\PermitFallbackAnalyzer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FreeAIAnalysisService
{
    public function __construct(
        private readonly PermitFallbackAnalyzer $fallbackAnalyzer = new PermitFallbackAnalyzer,
    ) {}

    private const CACHE_TTL = 12 * 60 * 60; // 12 hours

    private const OPENROUTER_API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    private const REQUEST_TIMEOUT = 60; // seconds — keep under job timeout budget (2 models × 2 attempts × 60s = 240s < 300s job limit)

    /**
     * ═══ TIERED MODEL CONFIGURATION ═══
     *
     * Semua model dikonfigurasi melalui config/services.php dan .env
     * Default: openrouter/free (gratis, tidak ada biaya per request)
     *
     * Environment variables:
     *   OPENROUTER_FREE_PRIMARY_MODEL, OPENROUTER_FREE_FALLBACK_MODEL
     *   OPENROUTER_PREMIUM_PRIMARY_MODEL, OPENROUTER_PREMIUM_FALLBACK_MODEL
     */

    // ── Tier configurations ──
    private const TIER_FREE = 'free';

    private const TIER_PREMIUM = 'premium';

    private const TIER_CONFIG = [
        self::TIER_FREE => [
            'max_tokens' => 3500,  // Sufficient for structured JSON output
            'temperature' => 0.25,  // Slightly higher — cost models benefit from it
        ],
        self::TIER_PREMIUM => [
            'max_tokens' => 6000,  // Increased for complete permit chain with prerequisites
            'temperature' => 0.15,  // Lower — premium models are more deterministic
        ],
    ];

    /**
     * Get primary + fallback model pair for a given tier
     *
     * @return array{primary: string, fallback: string}
     */
    private function getModelsForTier(string $tier): array
    {
        if ($tier === self::TIER_PREMIUM) {
            return [
                'primary' => config('services.openrouter.premium_primary_model', 'openrouter/free'),
                'fallback' => config('services.openrouter.premium_fallback_model', 'openrouter/free'),
            ];
        }

        // Free tier (default)
        return [
            'primary' => config('services.openrouter.free_primary_model', 'openrouter/free'),
            'fallback' => config('services.openrouter.free_fallback_model', 'openrouter/free'),
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
     * @param  array  $formData  Business context data (KBLI, scale, location, etc.)
     * @param  string  $tier  'free' (landing page) or 'premium' (client portal)
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
                'processing_time' => 0,
            ]);
        }

        try {
            // Validate API key
            $apiKey = config('services.openrouter.api_key');
            if (empty($apiKey)) {
                Log::error('FreeAIAnalysisService: OpenRouter API key not configured');

                return $this->fallbackAnalyzer->analyze($formData);
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
            if (! $responseData) {
                Log::warning('FreeAIAnalysisService: Primary model failed, trying fallback', [
                    'tier' => $tier,
                    'primary' => $primaryModel,
                    'fallback' => $fallbackModel,
                ]);
                $model = $fallbackModel;
                $responseData = $this->callOpenRouterAPI($apiKey, $model, $systemPrompt, $userPrompt, $tierConfig);
            }

            if (! $responseData) {
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
            $fallback = $this->fallbackAnalyzer->analyze($formData);

            return $this->validateAndEnrichAnalysis($fallback, $formData);
        }
    }

    /**
     * Call OpenRouter API — extracted for model fallback support
     *
     * @param  array  $tierConfig  Tier-specific params: max_tokens, temperature
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
                'Authorization' => 'Bearer '.$apiKey,
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
                .'AI tidak dapat menganalisis saat ini, sehingga hasil berdasarkan template umum. '
                .'Untuk analisis lebih akurat, gunakan fitur "Ajukan Permohonan / Konsultasi" di bawah '
                .'agar konsultan bersertifikat BizMark dapat memberikan analisis detail sesuai kondisi spesifik usaha Anda.';
        } else {
            $result['limitations'] = 'Analisis ini dihasilkan oleh AI berdasarkan regulasi terkini 2026 (UU 6/2023, PP 5/2021) '
                .'dan data KBLI yang tersedia. Meskipun sudah dioptimalkan untuk akurasi tinggi, persyaratan spesifik '
                .'dapat bervariasi berdasarkan peraturan daerah (Perda) setempat dan kondisi lapangan. '
                .'Gunakan fitur "Ajukan Permohonan / Konsultasi" untuk pendampingan konsultan bersertifikat.';
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
        if (! empty($result['next_steps'])) {
            $filtered = array_filter($result['next_steps'], function ($step) {
                return ! str_contains(strtolower($step), 'daftar ke portal')
                    && ! str_contains(strtolower($step), 'silakan daftar');
            });
            $result['next_steps'] = ! empty($filtered) ? array_values($filtered) : $portalNextSteps;
        } else {
            $result['next_steps'] = $portalNextSteps;
        }

        // Filter risk_factors that reference free-tier
        if (! empty($result['risk_factors'])) {
            $result['risk_factors'] = array_values(array_filter($result['risk_factors'], function ($factor) {
                return ! str_contains(strtolower($factor), 'daftar ke portal')
                    && ! str_contains(strtolower($factor), 'silakan daftar');
            }));
        }

        return $result;
    }

    /**
     * Map external scale parameter to internal format
     */
    private function mapExternalScale(?string $scale): string
    {
        if (! $scale) {
            return 'unknown';
        }
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
        if (! $location) {
            return 'unknown';
        }
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

        return 'ai_analysis_v3_'.md5($key);
    }

    /**
     * Get system prompt for AI - Updated with current Indonesian regulations (2026)
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
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
     → Jika lokasi belum memiliki RDTR: WAJIB Persetujuan KKPR via ATR/BPN (bukan hanya Konfirmasi)
  5. Andalalin (Analisis Dampak Lalu Lintas) — wajib untuk bangunan komersial/industri/publik
     yang bangkitkan/tarik perjalanan ≥ 100 kendaraan/jam atau luas > 500 m² (PP 96/2015 + Permenhub 75/2015)
     → Diterbitkan oleh Dinas Perhubungan Daerah; prasyarat PBG untuk bangunan komersial besar
  6. Persetujuan Lingkungan: SPPL / UKL-UPL / AMDAL — sesuai tingkat risiko
  7. Persetujuan Teknis (Pertek) PPLH — untuk usaha dengan baku mutu air limbah/emisi (PP 22/2021)
     → Wajib bagi usaha dengan kegiatan proses basah, pembakaran, atau produksi yang buang limbah cair/gas
  8. Rintek LB3 (Rincian Teknis Penyimpanan LB3) — dokumen teknis wajib sebelum TPS-LB3 (PP 22/2021 Ps.285)
     → Berlaku untuk usaha yang menghasilkan atau menangani Limbah B3
  9. TPS-LB3 / Izin Pengelolaan Limbah B3 — setelah Rintek LB3

TAHAP 3 — IZIN TEKNIS & BANGUNAN:
  10. Gambar Arsitektur / DED + Siteplan — dokumen teknis wajib sebelum PBG
      → Bukan izin tapi prerequisite dokumen; disusun oleh arsitek berlisensi IAI
  11. PBG (Persetujuan Bangunan Gedung) — jika ada bangunan baru/renovasi/perluasan (PP 16/2021)
      → Prasyarat: KKPR/PKKPR, Persetujuan Lingkungan, Andalalin (jika wajib), Gambar Arsitektur
  12. SLO Instalasi Listrik (Sertifikat Laik Operasi) — setelah bangunan selesai, sebelum SLF
      → Diterbitkan oleh Lembaga Inspeksi Teknik (LIT) terakreditasi (Permen ESDM 12/2021)
  13. SLF (Sertifikat Laik Fungsi) — setelah bangunan selesai dan SLO terbit, sebelum operasional

TAHAP 4 — IZIN OPERASIONAL & STANDAR:
  14. Sertifikat Standar / Izin Usaha — sesuai klasifikasi risiko KBLI
  15. SMK3 / P2K3 (Sistem Manajemen K3) — wajib untuk perusahaan >100 karyawan atau risiko tinggi
      → PP 50/2012 tentang SMK3; diterbitkan KEMNAKER; proses audit K3 internal + eksternal
  16. Uji Lab / Sertifikasi SNI — wajib untuk produk pangan (MD/ML BPOM), industri (SNI wajib),
      farmasi, kosmetik, alat kesehatan; sesuai NSPK K/L masing-masing

TAHAP 5 — IZIN SEKTORAL KHUSUS:
  17. Izin sektoral sesuai NSPK K/L (BPOM, Kemenkes, Kemen PUPR, Kemenhub, dll.)
  18. Izin daerah tambahan (Perda setempat, retribusi daerah, jika ada)

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
            "phase": 1,
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
            "triggers_next": ["Nama izin yang BISA diurus setelah ini selesai"],
            "documents_required": ["Dokumen spesifik yang wajib disiapkan untuk izin ini"]
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
- Real Estate (68111): NIB → NPWP → PKKPR → Andalalin → UKL-UPL/AMDAL → Gambar Arsitektur → PBG → SLO → SLF → Sertifikat Standar (10+ izin)
- Industri Limbah B3: NIB → NPWP → PKKPR → AMDAL → Pertek PPLH → Rintek LB3 → TPS-LB3 → Izin Pengelolaan B3 → Gambar Arsitektur → PBG → SLO → SLF → SMK3 (13+ izin)
- Restoran (560100): NIB → NPWP → SPPL → Sertifikat Standar → Gambar Arsitektur → PBG → SLF (7 izin)
- Perdagangan online: NIB → NPWP → Sertifikat Standar (3 izin)
- Konstruksi: NIB → NPWP → SBU Konstruksi → PKKPR → UKL-UPL/AMDAL → Andalalin → Gambar Arsitektur → PBG → SLO → SLF → SMK3 (11+ izin)
- Pabrik/Industri menengah: NIB → NPWP → PKKPR → UKL-UPL → Pertek PPLH → Andalalin → Gambar Arsitektur → PBG → SLO → SLF → Sertifikat Standar → SMK3 → SNI/Uji Lab (13+ izin)

RULES KETAT:
1. Rekomendasikan 3-15 izin berdasarkan kompleksitas (3-5 untuk usaha sederhana/online, 7-15 untuk industri/konstruksi/real estate)
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
15. WAJIB isi phase (integer 1-5) sesuai tahap masing-masing izin
16. WAJIB isi documents_required (array) untuk setiap izin — dokumen spesifik yang perlu disiapkan
17. Andalalin WAJIB direkomendasikan untuk: bangunan komersial > 500m², restoran, hotel, pabrik, rumah sakit, pusat perbelanjaan
18. SMK3 WAJIB untuk: perusahaan dengan >100 karyawan, atau sektor dengan risiko K3 tinggi (konstruksi, pertambangan, industri kimia, manufaktur)
19. Rintek LB3 + TPS-LB3 WAJIB untuk: industri yang menghasilkan limbah B3 (oli bekas, bahan kimia, farmasi, cat, baterai, dll.)
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

        $kbliLine = $kbli ? "- Kode KBLI: {$kbli}".($kbliDesc ? " ({$kbliDesc})" : '') : '- Kode KBLI: Belum ditentukan (tolong sarankan kode KBLI 5 digit yang paling sesuai)';

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

        throw new \Exception('Failed to parse AI response as JSON. Content length: '.strlen($content));
    }

    /**
     * Validate and enrich the AI analysis to ensure complete data structure
     */
    private function validateAndEnrichAnalysis(array $analysis, array $formData): array
    {
        // Ensure recommended_permits exists and is an array
        if (! isset($analysis['recommended_permits']) || ! is_array($analysis['recommended_permits'])) {
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
            if (! isset($permit['type'])) {
                $permit['type'] = match ($permit['priority']) {
                    'critical' => 'mandatory',
                    'high' => 'mandatory',
                    'medium' => 'recommended',
                    default => 'conditional',
                };
            }

            // Ensure fee structures exist
            if (! isset($permit['government_fee']) || ! is_array($permit['government_fee'])) {
                $permit['government_fee'] = ['min' => 0, 'max' => 0, 'note' => 'Estimasi'];
            }
            if (! isset($permit['consultant_fee']) || ! is_array($permit['consultant_fee'])) {
                $permit['consultant_fee'] = ['min' => 1500000, 'max' => 3000000, 'note' => 'Estimasi'];
            }

            // Calculate total_cost_range if missing
            if (empty($permit['total_cost_range'])) {
                $totalMin = ($permit['government_fee']['min'] ?? 0) + ($permit['consultant_fee']['min'] ?? 0);
                $totalMax = ($permit['government_fee']['max'] ?? 0) + ($permit['consultant_fee']['max'] ?? 0);
                $permit['total_cost_range'] = CostFormatter::range($totalMin, $totalMax);
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
            if ($catA !== $catB) {
                return $catA - $catB;
            }

            $priA = $priorityOrder[$a['priority'] ?? 'medium'] ?? 2;
            $priB = $priorityOrder[$b['priority'] ?? 'medium'] ?? 2;
            if ($priA !== $priB) {
                return $priA - $priB;
            }

            return 0;
        });

        // Topological sort: ensure prerequisites appear before dependents
        $permitsByName = [];
        foreach ($analysis['recommended_permits'] as $idx => $p) {
            $permitsByName[$p['name']] = $idx;
            if (! empty($p['code'])) {
                $permitsByName[$p['code']] = $idx;
            }
        }

        $sorted = [];
        $visited = [];
        $permits = $analysis['recommended_permits'];

        $addPermit = function (int $idx) use (&$addPermit, &$sorted, &$visited, &$permits, &$permitsByName) {
            if (isset($visited[$idx])) {
                return;
            }
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
        if (! isset($analysis['total_estimated_cost']) || ! is_array($analysis['total_estimated_cost'])) {
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
        if (! isset($analysis['total_estimated_cost']['grand_total'])) {
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
            ? min(10, max(1, (float) $analysis['complexity_score'])) : 5.0;
        $analysis['risk_factors'] = $analysis['risk_factors'] ?? [];
        $analysis['next_steps'] = $analysis['next_steps'] ?? [];
        $analysis['required_documents'] = $analysis['required_documents'] ?? [];
        $analysis['risk_classification'] = $analysis['risk_classification'] ?? 'menengah_rendah';
        $analysis['limitations'] = $analysis['limitations'] ?? 'Analisis ini bersifat umum berdasarkan informasi yang diberikan. Untuk analisis detail dengan dokumen checklist lengkap, pendampingan konsultan bersertifikat, dan akses portal monitoring, silakan daftar ke portal BizMark.ID.';

        // Build risk_assessment for backward compatibility with ConsultationPricingEngine
        if (! isset($analysis['risk_assessment'])) {
            $riskLevel = match ($analysis['risk_classification'] ?? 'menengah_rendah') {
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
        if (! isset($analysis['estimated_timeline']) || ! is_array($analysis['estimated_timeline'])) {
            $analysis['estimated_timeline'] = [
                'summary' => $analysis['total_estimated_timeline'] ?? '14-30 hari kerja',
            ];
        }

        // Enrich timeline with minimum_days, maximum_days if missing
        if (! isset($analysis['estimated_timeline']['minimum_days']) || ! isset($analysis['estimated_timeline']['maximum_days'])) {
            $totalMinDays = 0;
            $totalMaxDays = 0;
            foreach ($analysis['recommended_permits'] as $p) {
                $timeline = $p['estimated_timeline'] ?? '';
                if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int) $m[1];
                    $totalMaxDays += (int) $m[2];
                } elseif (preg_match('/(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int) $m[1];
                    $totalMaxDays += (int) $m[1];
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
        if (! isset($analysis['estimated_timeline']['critical_path'])) {
            $criticalPath = [];
            foreach ($analysis['recommended_permits'] as $p) {
                if (in_array($p['priority'] ?? '', ['critical', 'high'])) {
                    $criticalPath[] = $p['name'].' ('.($p['estimated_timeline'] ?? '?').')';
                }
            }
            $analysis['estimated_timeline']['critical_path'] = $criticalPath;
        }

        return $analysis;
    }

    /**
     * Translation helpers
     */
    private function translateScale(string $scale): string
    {
        return match ($scale) {
            'micro' => 'Mikro (< 10 karyawan)',
            'small' => 'Kecil (10-50 karyawan)',
            'medium' => 'Menengah (50-100 karyawan)',
            'large' => 'Besar (> 100 karyawan)',
            default => 'Tidak disebutkan'
        };
    }

    private function translateLocationCategory(string $category): string
    {
        return match ($category) {
            'industrial' => 'Kawasan Industri',
            'commercial' => 'Area Komersial',
            'residential' => 'Area Residensial',
            'rural' => 'Pedesaan',
            default => 'Tidak disebutkan'
        };
    }

    private function translateInvestment(string $investment): string
    {
        return match ($investment) {
            'under_100m' => '< Rp 100 juta',
            '100m_500m' => 'Rp 100 - 500 juta',
            '500m_2b' => 'Rp 500 juta - 2 miliar',
            'over_2b' => '> Rp 2 miliar',
            default => 'Tidak disebutkan'
        };
    }

    private function translateCompanyType(string $type): string
    {
        return match ($type) {
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
        return match ($timeline) {
            'urgent' => 'Urgent (< 1 bulan)',
            '1-3_months' => '1-3 bulan',
            '3-6_months' => '3-6 bulan',
            '6plus_months' => '> 6 bulan',
            'not_sure' => 'Belum pasti',
            default => ''
        };
    }
}
