<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialCaptionService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate captions for all platforms
     */
    public function generateAll(Article $article): array
    {
        $result = $this->callAI($article);

        if (! $result) {
            return $this->generateFallback($article);
        }

        return $result;
    }

    /**
     * Generate caption for specific platform
     */
    public function generateFor(Article $article, string $platform): ?string
    {
        $captions = $this->generateAll($article);

        return $captions[$platform] ?? null;
    }

    /**
     * Call AI to generate platform-specific captions
     */
    protected function callAI(Article $article): ?array
    {
        $url = config('app.url').'/blog/'.$article->slug;
        $content = Str::limit(strip_tags($article->content), 1500);

        $prompt = <<<PROMPT
Buat caption media sosial untuk artikel berikut dalam Bahasa Indonesia.

Judul: {$article->title}
Kategori: {$article->category}
Ringkasan: {$content}
URL: {$url}

Buat caption untuk setiap platform dengan format JSON:
{
  "whatsapp": "Caption untuk WhatsApp (informal, max 300 chars, emoji, include link)",
  "instagram": "Caption untuk Instagram (engaging, max 2200 chars, banyak emoji, hashtags 15-20)",
  "twitter": "Caption untuk Twitter/X (max 260 chars, engaging, 2-3 hashtags, include link)",
  "linkedin": "Caption untuk LinkedIn (professional, max 700 chars, 3-5 hashtags)",
  "facebook": "Caption untuk Facebook (casual-professional, max 500 chars, 3-5 hashtags)",
  "telegram": "Caption untuk Telegram (informatif, max 400 chars, minimal formatting)"
}

Rules:
- Semua caption harus dalam Bahasa Indonesia
- Sertakan URL {$url} di setiap caption
- Gunakan hook yang menarik di awal
- Instagram: gunakan line breaks dan emoji banyak
- Twitter: harus di bawah 280 chars total
- LinkedIn: tone profesional, insight-driven
- Hashtags: kombinasi populer + niche (perizinan, lingkungan, bisnis)

Respond ONLY with valid JSON object.
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'system', 'content' => 'Kamu adalah social media specialist Indonesia. Output valid JSON only.'],
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => config('services.openrouter.default_model', 'openrouter/free'),
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        if (! $response['success']) {
            Log::warning('Social caption AI failed', ['article' => $article->id]);

            return null;
        }

        $content = $response['content'];
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);

        $parsed = json_decode(trim($content), true);
        if (! $parsed) {
            Log::warning('Social caption parse failed', ['content' => Str::limit($content, 200)]);

            return null;
        }

        Log::info('Social captions generated', [
            'article' => $article->id,
            'platforms' => array_keys($parsed),
            'cost' => $response['cost'] ?? 0,
        ]);

        return $parsed;
    }

    /**
     * Fallback captions without AI
     */
    protected function generateFallback(Article $article): array
    {
        $url = config('app.url').'/blog/'.$article->slug;
        $title = $article->title;
        $excerpt = $article->excerpt ?: Str::limit(strip_tags($article->content), 150);
        $tags = '#Perizinan #Bisnis #Bizmark #Lingkungan #Indonesia';

        return [
            'whatsapp' => "📄 *{$title}*\n\n{$excerpt}\n\n🔗 Baca selengkapnya: {$url}",
            'instagram' => "📄 {$title}\n\n{$excerpt}\n\n🔗 Link di bio atau kunjungi bizmark.id\n\n{$tags} #ConsultantLingkungan #IzinUsaha #AMDAL #OSS #PerizinanOnline",
            'twitter' => "📄 {$title}\n\n".Str::limit($excerpt, 150)."\n\n{$url}\n\n#Perizinan #Bisnis #Bizmark",
            'linkedin' => "📄 {$title}\n\n{$excerpt}\n\nBaca selengkapnya: {$url}\n\n{$tags}",
            'facebook' => "📄 {$title}\n\n{$excerpt}\n\n🔗 {$url}\n\n{$tags}",
            'telegram' => "📄 <b>{$title}</b>\n\n{$excerpt}\n\n🔗 {$url}",
        ];
    }

    /**
     * Get optimal posting schedule per platform (WIB timezone)
     */
    public function getOptimalSchedule(): array
    {
        return [
            'whatsapp' => ['08:00', '12:00', '19:00'],
            'instagram' => ['07:00', '12:00', '17:00', '21:00'],
            'twitter' => ['08:00', '12:30', '17:30'],
            'linkedin' => ['07:30', '10:00', '17:00'],
            'facebook' => ['09:00', '13:00', '16:00'],
            'telegram' => ['08:00', '12:00', '20:00'],
        ];
    }

    /**
     * Get next optimal time for a platform
     */
    public function getNextOptimalTime(string $platform): string
    {
        $schedule = $this->getOptimalSchedule()[$platform] ?? ['09:00'];
        $now = now()->timezone('Asia/Jakarta');

        foreach ($schedule as $time) {
            $target = $now->copy()->setTimeFromTimeString($time);
            if ($target->gt($now)) {
                return $target->toIso8601String();
            }
        }

        // Next day first slot
        return $now->addDay()->setTimeFromTimeString($schedule[0])->toIso8601String();
    }
}
