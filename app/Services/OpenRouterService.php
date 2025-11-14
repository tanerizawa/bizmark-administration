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

        return "Anda adalah ahli perizinan usaha Indonesia dengan spesialisasi dalam mencocokkan kode KBLI dengan persyaratan perizinan.

INFORMASI USAHA:
Kode KBLI: {$kbliCode}
Deskripsi Kegiatan: {$description}
Sektor: {$sector}{$context}

TUGAS: Analisis kebutuhan perizinan secara komprehensif dan berikan rekomendasi dalam format JSON.

FORMAT OUTPUT (JSON):
{
  \"permits\": [
    {
      \"name\": \"Nama Izin\",
      \"type\": \"mandatory|recommended|optional\",
      \"issuing_authority\": \"Instansi Penerbit\",
      \"estimated_cost_range\": {\"min\": 0, \"max\": 0},
      \"estimated_days\": 0,
      \"priority\": 1,
      \"description\": \"Penjelasan singkat\",
      \"legal_basis\": \"Dasar hukum\",
      \"prerequisites\": []
    }
  ],
  \"documents\": [
    {
      \"name\": \"Nama Dokumen\",
      \"type\": \"identity|company|technical|financial|other\",
      \"required_for_permits\": [],
      \"format\": \"PDF/JPG\",
      \"notes\": \"Catatan\"
    }
  ],
  \"risk_assessment\": {
    \"level\": \"low|medium|high\",
    \"factors\": [],
    \"mitigation\": []
  },
  \"timeline\": {
    \"minimum_days\": 0,
    \"maximum_days\": 0,
    \"critical_path\": []
  },
  \"additional_considerations\": [],
  \"regional_variations\": \"\"
}

ATURAN:
1. Fokus pada regulasi Indonesia (OSS 1.1, BKPM, kementerian sektoral)
2. Prioritaskan izin MANDATORY terlebih dahulu
3. Berikan estimasi biaya dan waktu REALISTIS
4. NIB adalah izin dasar untuk hampir semua usaha
5. Estimasi biaya dalam Rupiah

Berikan HANYA output JSON tanpa penjelasan tambahan.";
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
