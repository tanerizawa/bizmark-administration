<?php

namespace App\Services;

use App\Models\AiQueryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://openrouter.ai/api/v1';

    protected string $primaryModel = 'anthropic/claude-sonnet-4';

    protected string $fallbackModel = 'google/gemini-2.5-flash';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
    }

    /**
     * Generate permit recommendations — delegates to FreeAIAnalysisService (canonical system).
     * Transforms output to legacy format expected by ConsultationPricingEngine & KbliPermitCacheService.
     */
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
            /** @var FreeAIAnalysisService $analysisService */
            $analysisService = app(FreeAIAnalysisService::class);
            $analysis = $analysisService->analyzeFromKbli($kbliCode, $kbliDescription, $sector, $businessScale, $locationType);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log query for tracking
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'business_context' => ['scale' => $businessScale, 'location' => $locationType],
                'prompt_text' => "Delegated to FreeAIAnalysisService v3.0 (kbli={$kbliCode}, sector={$sector})",
                'response_text' => substr(json_encode($analysis['recommended_permits'] ?? []), 0, 10000),
                'tokens_used' => $analysis['ai_tokens_used'] ?? null,
                'response_time_ms' => $responseTime,
                'status' => 'success',
                'ai_model' => $analysis['ai_model_used'] ?? 'unknown',
                'api_cost' => null,
            ]);

            return [
                'recommended_permits' => $analysis['recommended_permits'] ?? [],
                'required_documents' => $analysis['required_documents'] ?? [],
                'risk_assessment' => $analysis['risk_assessment'] ?? null,
                'estimated_timeline' => $analysis['estimated_timeline'] ?? null,
                'additional_notes' => json_encode([
                    'risk_factors' => $analysis['risk_factors'] ?? [],
                    'next_steps' => $analysis['next_steps'] ?? [],
                    'limitations' => $analysis['limitations'] ?? '',
                ]),
                'ai_model' => $analysis['ai_model_used'] ?? 'unknown',
                'ai_prompt_hash' => md5($kbliCode.$sector.($businessScale ?? '').($locationType ?? '')),
                'confidence_score' => $this->calculateConfidence([
                    'recommended_permits' => $analysis['recommended_permits'] ?? [],
                    'required_documents' => $analysis['required_documents'] ?? [],
                    'risk_assessment' => $analysis['risk_assessment'] ?? null,
                    'estimated_timeline' => $analysis['estimated_timeline'] ?? null,
                ]),
            ];

        } catch (\Exception $e) {
            Log::error('AI generation failed (delegated)', ['kbli_code' => $kbliCode, 'error' => $e->getMessage()]);
            $this->logQuery([
                'client_id' => $clientId,
                'kbli_code' => $kbliCode,
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @deprecated Prompt logic moved to FreeAIAnalysisService (canonical system).
     * Kept as stub for reference. See FreeAIAnalysisService::getSystemPrompt().
     */
    protected function buildPrompt(string $kbliCode, string $description, string $sector, ?string $businessScale, ?string $locationType): string
    {
        // Legacy: this method is no longer called. All prompt logic lives in FreeAIAnalysisService.
        return 'DEPRECATED — see FreeAIAnalysisService';
    }

    protected function callAI(string $prompt, string $model): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(60)->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert in Indonesian business licensing. Always respond in Indonesian. Provide valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
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
            throw new \Exception('Invalid JSON: '.json_last_error_msg());
        }

        if (! isset($data['permits']) || ! is_array($data['permits'])) {
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
            if ($hasMandatory) {
                $score += 0.2;
            }
        }
        if (! empty($data['required_documents'])) {
            $score += 0.1;
        }
        if (! empty($data['risk_assessment'])) {
            $score += 0.1;
        }
        if (! empty($data['estimated_timeline'])) {
            $score += 0.1;
        }

        return min(1.0, $score);
    }

    protected function calculateCost(array $usage, string $model): ?float
    {
        if (empty($usage)) {
            return null;
        }

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

    /**
     * Generic chat method for flexible AI interactions.
     * Automatically falls back to secondary model on 400/404 errors.
     */
    public function chat(array $messages, array $options = []): array
    {
        $model = $options['model'] ?? $this->primaryModel;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4000;

        $modelsToTry = [$model];
        if ($model !== $this->fallbackModel) {
            $modelsToTry[] = $this->fallbackModel;
        }

        $lastResult = null;

        foreach ($modelsToTry as $currentModel) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->timeout(120)->post("{$this->baseUrl}/chat/completions", [
                    'model' => $currentModel,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'success' => true,
                        'content' => $data['choices'][0]['message']['content'] ?? '',
                        'tokens_used' => $data['usage']['total_tokens'] ?? null,
                        'prompt_tokens' => $data['usage']['prompt_tokens'] ?? null,
                        'completion_tokens' => $data['usage']['completion_tokens'] ?? null,
                        'cost' => $this->calculateCost($data['usage'] ?? [], $currentModel),
                        'model' => $currentModel,
                    ];
                }

                $status = $response->status();

                // Model deprecated/removed — try fallback
                if (in_array($status, [400, 404]) && $currentModel !== end($modelsToTry)) {
                    Log::warning('OpenRouter model unavailable, trying fallback', [
                        'failed_model' => $currentModel,
                        'status' => $status,
                        'fallback' => $this->fallbackModel,
                    ]);

                    continue;
                }

                Log::error('OpenRouter API error', [
                    'status' => $status,
                    'body' => $response->body(),
                    'model' => $currentModel,
                ]);

                $lastResult = [
                    'success' => false,
                    'error' => 'API request failed: '.$status,
                    'details' => $response->json(),
                ];
            } catch (\Exception $e) {
                Log::error('OpenRouter chat exception', [
                    'error' => $e->getMessage(),
                    'model' => $currentModel,
                ]);

                $lastResult = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $lastResult ?? ['success' => false, 'error' => 'All models failed'];
    }
}
