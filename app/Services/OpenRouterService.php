<?php

namespace App\Services;

use App\Models\AiQueryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected string $primaryModel = 'anthropic/claude-3.5-sonnet';
    protected string $fallbackModel = 'google/gemini-pro-1.5';
    
    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
    }

    public function generatePermitRecommendations(
        string $kbliCode,
        string $kbliDescription,
        string $sector,
        ?string $businessScale = null,
        ?string $locationType = null,
        ?int $clientId = null
    ): ?array {
        $startTime = microtime(true);
        
        try {
            $prompt = $this->buildPrompt($kbliCode, $kbliDescription, $sector, $businessScale, $locationType);
            $promptHash = md5($prompt);
            
            $response = $this->callAI($prompt, $this->primaryModel);
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            
            if ($response['success']) {
                $parsedData = $this->parseResponse($response['content']);
                
                $this->logQuery([
                    'client_id' => $clientId,
                    'kbli_code' => $kbliCode,
                    'business_context' => ['scale' => $businessScale, 'location' => $locationType],
                    'prompt_text' => substr($prompt, 0, 5000),
                    'response_text' => substr($response['content'], 0, 10000),
                    'tokens_used' => $response['tokens_used'] ?? null,
                    'response_time_ms' => $responseTime,
                    'status' => 'success',
                    'ai_model' => $this->primaryModel,
                    'api_cost' => $response['cost'] ?? null,
                ]);
                
                return array_merge($parsedData, [
                    'ai_model' => $this->primaryModel,
                    'ai_prompt_hash' => $promptHash,
                    'confidence_score' => $this->calculateConfidence($parsedData),
                ]);
            }
            
            // Fallback to secondary model
            Log::warning('Primary AI failed, trying fallback', ['error' => $response['error']]);
            $fallbackResponse = $this->callAI($prompt, $this->fallbackModel);
            
            if ($fallbackResponse['success']) {
                $parsedData = $this->parseResponse($fallbackResponse['content']);
                $this->logQuery([
                    'client_id' => $clientId,
                    'kbli_code' => $kbliCode,
                    'business_context' => ['scale' => $businessScale, 'location' => $locationType],
                    'prompt_text' => substr($prompt, 0, 5000),
                    'response_text' => substr($fallbackResponse['content'], 0, 10000),
                    'tokens_used' => $fallbackResponse['tokens_used'] ?? null,
                    'response_time_ms' => $responseTime,
                    'status' => 'success',
                    'ai_model' => $this->fallbackModel,
                    'api_cost' => $fallbackResponse['cost'] ?? null,
                ]);
                
                return array_merge($parsedData, [
                    'ai_model' => $this->fallbackModel,
                    'ai_prompt_hash' => $promptHash,
                    'confidence_score' => $this->calculateConfidence($parsedData),
                ]);
            }
            
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'status' => 'error',
                'error_message' => $fallbackResponse['error'] ?? 'Unknown error',
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('AI generation failed', ['kbli_code' => $kbliCode, 'error' => $e->getMessage()]);
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function buildPrompt(string $kbliCode, string $description, string $sector, ?string $businessScale, ?string $locationType): string
    {
        $context = '';
        if ($businessScale) $context .= "\nSkala Usaha: {$businessScale}";
        if ($locationType) $context .= "\nTipe Lokasi: {$locationType}";

        return "Anda adalah konsultan perizinan senior Indonesia dengan 15+ tahun pengalaman. Analisis LENGKAP dan MENDETAIL seluruh perizinan.

INFORMASI USAHA:
Kode KBLI: {$kbliCode}
Deskripsi Kegiatan: {$description}
Sektor: {$sector}{$context}

INSTRUKSI PENTING:
1. **IDENTIFIKASI SEMUA IZIN** - Jangan lewatkan izin pendukung, izin operasional, dan izin teknis
2. **URUTAN DEPENDENCY** - Jelaskan izin mana yang harus didapat dulu (prerequisites)
3. **IZIN BERCABANG** - Untuk sektor tertentu (Real Estate, Konstruksi, Industri), identifikasi:
   - Izin lingkungan (UKL-UPL, AMDAL)
   - Izin teknis (PBG/IMB, Pertek BPN, PKKPR)
   - Izin operasional (SLF, Laik Fungsi)
   - Izin khusus sektor
4. **BEST PRACTICE** - Ikuti alur perizinan OSS 1.1 dan regulasi terbaru

⚠️ **ATURAN BIAYA PEMERINTAH (PENTING):**
- **estimated_cost_range** adalah HANYA biaya resmi ke PEMERINTAH (PNBP, retribusi daerah)
- NIB, NPWP, Sertifikat Standar = Rp 0 (memang gratis dari pemerintah)
- Izin teknis (IMB/PBG, SLF, dll) = ada biaya pemerintah sesuai perda
- Izin lingkungan (UKL-UPL, AMDAL) = ada biaya pemerintah
- Izin sektoral = bervariasi tergantung jenis

⚠️ **ESTIMASI BIAYA PEMERINTAH REALISTIS:**
- IMB/PBG: Rp 50,000 - 500,000 per m² (tergantung fungsi bangunan & daerah)
- SLF: Rp 1,000,000 - 5,000,000 (tergantung luas & kompleksitas)
- UKL-UPL: Rp 2,000,000 - 5,000,000
- AMDAL: Rp 10,000,000 - 50,000,000 (tergantung skala)
- Izin Usaha Perdagangan: Rp 250,000 - 1,000,000
- Izin Lokasi: Rp 500,000 - 2,000,000
- TDP: Rp 100,000 - 500,000
- SIUP: Rp 250,000 - 1,000,000

FORMAT OUTPUT (JSON):
{
  \"permits\": [
    {
      \"name\": \"Nama Izin Lengkap\",
      \"type\": \"mandatory|recommended|conditional\",
      \"category\": \"foundational|environmental|technical|operational|sectoral\",
      \"issuing_authority\": \"Instansi Penerbit\",
      \"estimated_cost_range\": {\"min\": 0, \"max\": 0},
      \"estimated_days\": 0,
      \"priority\": 1,
      \"description\": \"Penjelasan detail fungsi dan pentingnya izin ini\",
      \"legal_basis\": \"Undang-undang/Peraturan yang mendasari\",
      \"prerequisites\": [\"Izin yang harus dimiliki terlebih dahulu\"],
      \"triggers_next\": [\"Izin yang bisa diurus setelah ini\"],
      \"exemptions\": \"Kondisi pengecualian jika ada\",
      \"renewal_period\": \"Masa berlaku dan perpanjangan\",
      \"compliance_requirements\": [\"Persyaratan berkelanjutan setelah izin terbit\"]
    }
  ],
  \"documents\": [
    {
      \"name\": \"Nama Dokumen\",
      \"type\": \"identity|company|technical|financial|environmental|legal|other\",
      \"required_for_permits\": [\"List izin yang memerlukan dokumen ini\"],
      \"format\": \"PDF/JPG\",
      \"notes\": \"Catatan penting tentang dokumen\",
      \"validity_period\": \"Masa berlaku dokumen jika ada\",
      \"legalization_needed\": false
    }
  ],
  \"permit_flow\": {
    \"phases\": [
      {
        \"phase_name\": \"Fase 1: Persiapan Dasar\",
        \"permits_in_phase\": [\"NIB\", \"NPWP\"],
        \"estimated_days\": 7,
        \"notes\": \"Izin dasar yang harus dimiliki dulu\"
      }
    ],
    \"critical_dependencies\": [
      {
        \"permit\": \"IMB/PBG\",
        \"depends_on\": [\"UKL-UPL\", \"Pertek BPN\", \"Pengesahan Siteplan\"],
        \"reason\": \"Alasan dependency\"
      }
    ]
  },
  \"risk_assessment\": {
    \"level\": \"low|medium|high\",
    \"factors\": [\"Faktor risiko spesifik\"],
    \"mitigation\": [\"Langkah mitigasi\"],
    \"common_pitfalls\": [\"Kesalahan umum yang harus dihindari\"]
  },
  \"timeline\": {
    \"minimum_days\": 0,
    \"maximum_days\": 0,
    \"realistic_days\": 0,
    \"critical_path\": [\"Urutan izin di jalur kritis\"],
    \"parallel_tracks\": [\"Izin yang bisa diurus paralel\"]
  },
  \"cost_breakdown\": {
    \"government_fees\": {\"min\": 0, \"max\": 0},
    \"consulting_fees_estimate\": {\"min\": 2000000, \"max\": 50000000},
    \"document_preparation\": {\"min\": 500000, \"max\": 5000000},
    \"total_estimate\": {\"min\": 2500000, \"max\": 55000000}
  },
  \"additional_considerations\": [
    \"Pertimbangan tambahan khusus untuk usaha ini\"
  ],
  \"regional_variations\": \"Perbedaan perizinan antar daerah jika signifikan\"
}

CONTOH UNTUK REAL ESTATE/PROPERTI (KBLI 68111):
- FOUNDATIONAL: NIB (Rp 0), NPWP Badan (Rp 0)
- ENVIRONMENTAL: UKL-UPL (Rp 2-5 juta) atau AMDAL (Rp 10-50 juta)
- TECHNICAL: 
  * Pertek BPN (Rp 500 ribu - 2 juta)
  * PKKPR (Rp 1-3 juta)
  * Pengesahan Siteplan (Rp 500 ribu - 1,5 juta)
  * IMB/PBG (Rp 50-500 ribu per m² × luas bangunan)
- OPERATIONAL:
  * Sertifikat Standar Real Estate (Rp 0 - via OSS)
  * SLF (Rp 1-5 juta)
- SECTORAL:
  * Izin Prinsip Pemanfaatan Ruang (Rp 1-3 juta)
  * Izin Lokasi (Rp 500 ribu - 2 juta)

ATURAN KETAT:
1. Minimal 8-12 izin untuk usaha yang kompleks (Real Estate, Industri, Konstruksi)
2. Minimal 5-7 izin untuk usaha menengah
3. Minimal 3-5 izin untuk usaha sederhana
4. WAJIB menyertakan prerequisites dan triggers_next
5. **Estimasi biaya PEMERINTAH harus REALISTIS** - jangan semua Rp 0
6. **estimated_cost_range = biaya ke PEMERINTAH saja** (bukan biaya konsultan)
7. Sertakan SEMUA izin teknis yang relevan
8. Gunakan kategori: foundational, environmental, technical, operational, sectoral
9. **PENTING**: Hanya NIB, NPWP, dan Sertifikat Standar yang benar-benar gratis (Rp 0)
10. **Izin lainnya hampir selalu ada biaya pemerintah** (PNBP/retribusi daerah)

Berikan HANYA output JSON valid tanpa markdown atau penjelasan tambahan.";
    }

    protected function callAI(string $prompt, string $model): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(60)->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert in Indonesian business licensing. Always respond in Indonesian. Provide valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'content' => $data['choices'][0]['message']['content'] ?? '',
                    'tokens_used' => $data['usage']['total_tokens'] ?? null,
                    'cost' => $this->calculateCost($data['usage'] ?? [], $model),
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function parseResponse(string $content): array
    {
        $content = preg_replace('/```json\s*/i', '', $content);
        $content = preg_replace('/```\s*$/i', '', $content);
        $content = trim($content);

        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON: ' . json_last_error_msg());
        }

        if (!isset($data['permits']) || !is_array($data['permits'])) {
            throw new \Exception('Missing or invalid permits field');
        }

        return [
            'recommended_permits' => $data['permits'],
            'required_documents' => $data['documents'] ?? [],
            'risk_assessment' => $data['risk_assessment'] ?? null,
            'estimated_timeline' => $data['timeline'] ?? null,
            'additional_notes' => isset($data['additional_considerations']) || isset($data['regional_variations'])
                ? json_encode([
                    'considerations' => $data['additional_considerations'] ?? [],
                    'regional_variations' => $data['regional_variations'] ?? null,
                ])
                : null,
        ];
    }

    protected function calculateConfidence(array $data): float
    {
        $score = 0.5;
        
        if (isset($data['recommended_permits'])) {
            $hasMandatory = collect($data['recommended_permits'])->where('type', 'mandatory')->isNotEmpty();
            if ($hasMandatory) $score += 0.2;
        }
        if (!empty($data['required_documents'])) $score += 0.1;
        if (!empty($data['risk_assessment'])) $score += 0.1;
        if (!empty($data['estimated_timeline'])) $score += 0.1;

        return min(1.0, $score);
    }

    protected function calculateCost(array $usage, string $model): ?float
    {
        if (empty($usage)) return null;

        $costs = [
            'anthropic/claude-3.5-sonnet' => ['input' => 3.0, 'output' => 15.0],
            'google/gemini-pro-1.5' => ['input' => 1.25, 'output' => 5.0],
        ];

        $modelCost = $costs[$model] ?? ['input' => 1.0, 'output' => 1.0];
        $inputCost = ($usage['prompt_tokens'] ?? 0) * $modelCost['input'] / 1000000;
        $outputCost = ($usage['completion_tokens'] ?? 0) * $modelCost['output'] / 1000000;

        return $inputCost + $outputCost;
    }

    protected function logQuery(array $data): void
    {
        try {
            AiQueryLog::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to log AI query', ['error' => $e->getMessage()]);
        }
    }
}
