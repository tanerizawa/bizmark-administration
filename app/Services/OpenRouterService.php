<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected Client $client;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->model = config('services.openrouter.model', 'x-ai/grok-4-fast');
        
        if (!$this->apiKey) {
            throw new \Exception('OpenRouter API key not configured');
        }

        // Simplified client config following OpenRouter best practices
        $this->client = new Client([
            'timeout' => 120, // 2 minutes timeout for large documents
            'http_errors' => false, // Handle errors manually
        ]);
    }

    /**
     * Paraphrase document with project context
     */
    public function paraphraseDocument(string $templateText, array $projectContext): array
    {
        try {
            // Split template into chunks if too large (4000 chars per chunk)
            $chunks = $this->splitIntoChunks($templateText, 4000);
            
            $results = [];
            $totalInputTokens = 0;
            $totalOutputTokens = 0;

            foreach ($chunks as $index => $chunk) {
                $prompt = $this->buildParaphrasePrompt($chunk, $projectContext, $index + 1, count($chunks));
                
                $response = $this->sendChatRequest($prompt);
                
                $results[] = [
                    'chunk_index' => $index + 1,
                    'content' => $response['content'],
                    'input_tokens' => $response['input_tokens'],
                    'output_tokens' => $response['output_tokens'],
                ];

                $totalInputTokens += $response['input_tokens'];
                $totalOutputTokens += $response['output_tokens'];

                // Delay to avoid rate limiting (500ms)
                if ($index < count($chunks) - 1) {
                    usleep(500000);
                }
            }

            return [
                'success' => true,
                'chunks' => $results,
                'full_text' => $this->combineChunks($results),
                'total_input_tokens' => $totalInputTokens,
                'total_output_tokens' => $totalOutputTokens,
                'cost' => $this->calculateCost($totalInputTokens, $totalOutputTokens),
                'chunks_count' => count($chunks),
                'model' => $this->model,
            ];

        } catch (\Exception $e) {
            Log::error('OpenRouter paraphrase error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send chat completion request
     */
    protected function sendChatRequest(string $prompt): array
    {
        try {
            // Following OpenRouter best practices
            $response = $this->client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    // Optional but recommended for rankings
                    'HTTP-Referer' => config('app.url'),
                    'Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah asisten profesional yang membantu menyesuaikan dokumen template perizinan. Tugas Anda adalah memparafrasekan teks template dengan konteks proyek yang diberikan, sambil mempertahankan struktur dan format dokumen. Jangan menghilangkan informasi penting, hanya sesuaikan dengan data proyek.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.3, // Lower temperature for more consistent output
                    'max_tokens' => 4000,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            if ($statusCode !== 200) {
                Log::error('OpenRouter API error', [
                    'status' => $statusCode,
                    'body' => substr($body, 0, 500)
                ]);
                throw new \Exception("OpenRouter API returned HTTP $statusCode");
            }
            
            $data = json_decode($body, true);
            
            if ($data === null) {
                Log::error('Invalid JSON response from OpenRouter', ['body' => substr($body, 0, 500)]);
                throw new \Exception('Invalid JSON response from OpenRouter');
            }
            
            $content = '';
            if (isset($data['choices']) && is_array($data['choices']) && isset($data['choices'][0]['message']['content'])) {
                $content = (string) $data['choices'][0]['message']['content'];
            } elseif (isset($data['choices'][0]['text'])) { // some models respond with 'text'
                $content = (string) $data['choices'][0]['text'];
            }

            return [
                'content' => $content,
                'input_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'output_tokens' => $data['usage']['completion_tokens'] ?? 0,
            ];

        } catch (GuzzleException $e) {
            Log::error('OpenRouter API request failed: ' . $e->getMessage());
            throw new \Exception('Failed to communicate with OpenRouter API: ' . $e->getMessage());
        }
    }

    /**
     * Build paraphrase prompt with project context
     */
    protected function buildParaphrasePrompt(string $chunkText, array $context, int $chunkNum, int $totalChunks): string
    {
        $contextStr = "Konteks Proyek:\n";
        foreach ($context as $key => $value) {
            $contextStr .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
        }

        $prompt = "Parafrasekan teks dokumen template berikut dengan menyesuaikan informasi dari konteks proyek yang diberikan.\n\n";
        $prompt .= $contextStr . "\n";
        $prompt .= "Bagian {$chunkNum} dari {$totalChunks}:\n\n";
        $prompt .= "TEMPLATE:\n{$chunkText}\n\n";
        $prompt .= "INSTRUKSI:\n";
        $prompt .= "1. Ganti placeholder/variabel dengan data proyek yang sesuai\n";
        $prompt .= "2. Sesuaikan kata ganti (mis: 'pemohon', 'lokasi', 'luas') dengan data proyek\n";
        $prompt .= "3. Pertahankan struktur format (heading, list, numbering)\n";
        $prompt .= "4. Jangan menambah atau mengurangi informasi substantif\n";
        $prompt .= "5. Output hanya hasil parafrase, tanpa komentar tambahan\n\n";
        $prompt .= "HASIL PARAFRASE:";

        return $prompt;
    }

    /**
     * Split text into chunks
     */
    protected function splitIntoChunks(string $text, int $maxChars = 4000): array
    {
        if (mb_strlen($text) <= $maxChars) {
            return [$text];
        }

        $chunks = [];
        $paragraphs = preg_split('/\n\n+/', $text);
        $currentChunk = '';

        foreach ($paragraphs as $para) {
            if (mb_strlen($currentChunk . $para) > $maxChars && !empty($currentChunk)) {
                $chunks[] = $currentChunk;
                $currentChunk = $para;
            } else {
                $currentChunk .= ($currentChunk ? "\n\n" : '') . $para;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    /**
     * Combine chunks back into full text
     */
    protected function combineChunks(array $chunks): string
    {
        $combined = '';
        
        foreach ($chunks as $chunk) {
            $combined .= $chunk['content'] . "\n\n";
        }

        return trim($combined);
    }

    /**
     * Calculate cost based on token usage
     * Gemini Flash is free, but we track for monitoring
     */
    protected function calculateCost(int $inputTokens, int $outputTokens): float
    {
        // Free model, return 0
        return 0.00;
        
        // If using paid model, calculate based on pricing:
        // $inputCost = ($inputTokens / 1000000) * 0.075; // $0.075 per 1M tokens
        // $outputCost = ($outputTokens / 1000000) * 0.30; // $0.30 per 1M tokens
        // return $inputCost + $outputCost;
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = $this->sendChatRequest('Hello, test connection');
            
            return [
                'success' => true,
                'message' => 'OpenRouter connection successful',
                'model' => $this->model,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
