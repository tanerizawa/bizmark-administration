<?php

namespace App\Services;

use App\Models\BacklinkTarget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AiEmailGenerator
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected string $model;
    protected int $maxTokens = 800;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->model = config('services.openrouter.model', 'x-ai/grok-beta');
    }

    /**
     * Generate personalized email for backlink outreach
     */
    public function generatePersonalizedEmail(BacklinkTarget $target, string $type = 'initial'): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackEmail($target, $type);
        }

        try {
            // Analyze target website first
            $websiteContext = $this->analyzeWebsite($target);

            // Generate email using AI
            $prompt = $this->buildPrompt($target, $websiteContext, $type);
            $response = $this->callOpenRouter($prompt);

            return [
                'subject' => $response['subject'] ?? $this->generateSubject($target),
                'message' => $response['message'] ?? $this->getFallbackEmail($target, $type)['message'],
                'personalization_score' => $response['score'] ?? 50,
                'generated_by' => 'AI (OpenRouter: ' . $this->model . ')',
            ];
        } catch (\Exception $e) {
            Log::error('AI Email Generation Failed', [
                'target' => $target->id,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackEmail($target, $type);
        }
    }

    /**
     * Analyze website content to extract key topics
     */
    private function analyzeWebsite(BacklinkTarget $target): array
    {
        // Cache website analysis for 7 days
        $cacheKey = 'website_analysis_' . md5($target->website_url);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($target) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 BizmarkBot/1.0'])
                    ->get($target->website_url);

                if (!$response->successful()) {
                    return $this->getDefaultContext($target);
                }

                $html = $response->body();
                
                // Extract meta description
                preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/i', $html, $descMatch);
                $description = $descMatch[1] ?? '';

                // Extract title
                preg_match('/<title>(.*?)<\/title>/i', $html, $titleMatch);
                $title = $titleMatch[1] ?? $target->website_name;

                // Extract H1 headings
                preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $html, $h1Matches);
                $mainTopics = array_slice($h1Matches[1], 0, 3);

                return [
                    'title' => strip_tags($title),
                    'description' => strip_tags($description),
                    'main_topics' => array_map('strip_tags', $mainTopics),
                    'category' => $target->category,
                    'domain_authority' => $target->domain_authority,
                ];
            } catch (\Exception $e) {
                return $this->getDefaultContext($target);
            }
        });
    }

    /**
     * Build AI prompt for email generation
     */
    private function buildPrompt(BacklinkTarget $target, array $context, string $type): string
    {
        $bizmarkInfo = "Bizmark (bizmark.id) adalah platform perizinan usaha #1 di Indonesia yang membantu entrepreneur mengurus:
- Pendirian PT/CV/UD
- Perizinan usaha (NIB, NPWP, izin lokasi)
- Legalitas bisnis
- Konsultasi business compliance

Kami punya 1000+ artikel berkualitas tentang bisnis, perizinan, dan entrepreneurship yang cocok untuk website {$target->website_name}.";

        $targetContext = "Target Website: {$target->website_name}
URL: {$target->website_url}
Category: {$target->category}
Domain Authority: {$target->domain_authority}
Website Focus: " . implode(', ', $context['main_topics'] ?? []);

        $instruction = match($type) {
            'initial' => "Write a personalized cold outreach email for backlink partnership. Goals:
1. Introduce Bizmark professionally
2. Highlight mutual benefits (quality content for their audience)
3. Propose guest post or resource link collaboration
4. Keep it concise (150-200 words)
5. Professional but friendly tone
6. Include specific reference to their website content",

            'follow_up' => "Write a friendly follow-up email (2nd attempt). Goals:
1. Reference previous email
2. Add more value proposition
3. Include specific article idea that fits their site
4. Shorter than initial (100-150 words)
5. Gentle reminder without being pushy",

            'accepted' => "Write a thank you email after partnership acceptance. Goals:
1. Express gratitude
2. Outline next steps
3. Confirm article topic and deadline
4. Professional and excited tone",

            default => "Write a professional outreach email for backlink collaboration."
        };

        return "{$instruction}

{$bizmarkInfo}

{$targetContext}

Respond in JSON format with these exact keys:
{
  \"subject\": \"Email subject line (max 60 chars, compelling)\",
  \"message\": \"Email body (HTML formatted, use <p>, <br>, <strong> tags)\",
  \"score\": \"Personalization score 1-100 (how well it matches the target)\"
}

Important:
- Write in INDONESIAN LANGUAGE (Bahasa Indonesia)
- Use 'Anda' (formal) not 'kamu'
- Professional business tone
- NO placeholder like [Nama], use actual data
- Include contact person name if available: " . ($target->contact_name ?? 'Editor Team');
    }

    /**
     * Call OpenRouter API
     */
    private function callOpenRouter(string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'HTTP-Referer' => 'https://bizmark.id',
            'X-Title' => 'Bizmark Backlink Outreach',
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->baseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional business development specialist writing partnership outreach emails for Bizmark, an Indonesian business services platform. Write in fluent Indonesian (Bahasa Indonesia) with professional business tone. Always respond in valid JSON format.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenRouter API call failed: ' . $response->body());
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '{}';
        
        return json_decode($content, true) ?? [];
    }

    /**
     * Generate subject line
     */
    private function generateSubject(BacklinkTarget $target): string
    {
        $subjects = [
            "Kolaborasi Konten: {$target->website_name} x Bizmark.ID",
            "Partnership Opportunity: Quality Content untuk {$target->website_name}",
            "Guest Post Proposal untuk {$target->website_name}",
            "Konten Berkualitas untuk Pembaca {$target->website_name}",
        ];

        return $subjects[array_rand($subjects)];
    }

    /**
     * Get fallback email template if AI fails
     */
    private function getFallbackEmail(BacklinkTarget $target, string $type): array
    {
        $contactName = $target->contact_name ?? 'Tim Editor';
        
        $templates = [
            'initial' => [
                'subject' => "Kolaborasi Konten: {$target->website_name} x Bizmark.ID",
                'message' => "<p>Halo {$contactName},</p>

<p>Saya dari Bizmark.ID, platform perizinan usaha terkemuka di Indonesia. Kami tertarik untuk berkolaborasi dengan {$target->website_name} dalam hal konten.</p>

<p>Bizmark.ID memiliki 1000+ artikel berkualitas tentang bisnis, perizinan, dan entrepreneurship yang sangat relevan untuk audience Anda di kategori {$target->category}.</p>

<p><strong>Proposal:</strong></p>
<ul>
<li>Guest post dengan konten original & berkualitas</li>
<li>Resource link untuk artikel yang saling melengkapi</li>
<li>Mutual benefit: quality backlink untuk kedua pihak</li>
</ul>

<p>Apakah Anda terbuka untuk diskusi lebih lanjut?</p>

<p>Terima kasih,<br>
<strong>Bizmark Business Development Team</strong><br>
Email: partnerships@bizmark.id<br>
Website: https://bizmark.id</p>",
                'score' => 50,
            ],

            'follow_up' => [
                'subject' => "Re: Kolaborasi Konten {$target->website_name}",
                'message' => "<p>Halo {$contactName},</p>

<p>Saya follow-up email sebelumnya tentang kolaborasi konten antara {$target->website_name} dan Bizmark.ID.</p>

<p>Saya punya beberapa ide artikel yang sangat cocok untuk audience Anda, misalnya:<br>
<em>\"Panduan Lengkap Pendirian PT untuk {$target->category} Business\"</em></p>

<p>Apakah Anda tertarik untuk diskusi singkat?</p>

<p>Best regards,<br>
<strong>Bizmark Team</strong></p>",
                'score' => 50,
            ],
        ];

        return $templates[$type] ?? $templates['initial'];
    }

    /**
     * Get default context if website analysis fails
     */
    private function getDefaultContext(BacklinkTarget $target): array
    {
        return [
            'title' => $target->website_name,
            'description' => '',
            'main_topics' => [],
            'category' => $target->category,
            'domain_authority' => $target->domain_authority,
        ];
    }

    /**
     * Batch generate emails for multiple targets
     */
    public function batchGenerate(array $targets, string $type = 'initial'): array
    {
        $results = [];

        foreach ($targets as $target) {
            $results[$target->id] = $this->generatePersonalizedEmail($target, $type);
            
            // Rate limiting to avoid API quota
            if (count($results) % 10 == 0) {
                sleep(2);
            }
        }

        return $results;
    }
}
