<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private const EMBEDDING_MODEL = 'text-embedding-ada-002';

    private const EMBEDDING_DIMS = 1536;

    private string $apiKey;

    private string $baseUrl;

    public function __construct()
    {
        // OpenRouter supports OpenAI-compatible embeddings endpoint
        $this->apiKey = config('services.openrouter.api_key') ?? '';
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Embed a single text string. Returns float[] of length 1536.
     * Returns empty array on failure.
     */
    public function embed(string $text): array
    {
        if (empty($this->apiKey)) {
            Log::warning('[Embedding] API key not configured');

            return [];
        }

        // Truncate to ~8000 chars to stay within token limits
        $text = mb_substr(trim($text), 0, 8000);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->withHeaders(['HTTP-Referer' => config('app.url')])
                ->post("{$this->baseUrl}/embeddings", [
                    'model' => self::EMBEDDING_MODEL,
                    'input' => $text,
                ]);

            if (! $response->successful()) {
                Log::warning('[Embedding] API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            }

            return $response->json('data.0.embedding', []);
        } catch (\Throwable $e) {
            Log::error('[Embedding] Exception: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Convert float array to pgvector literal string '[0.1,0.2,...]'.
     */
    public static function toVectorLiteral(array $embedding): string
    {
        return '['.implode(',', $embedding).']';
    }

    public static function dims(): int
    {
        return self::EMBEDDING_DIMS;
    }
}
