<?php

namespace App\Services;

use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Services\OpenRouterService;
use Illuminate\Support\Str;

class ArticleGenerationService
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    /**
     * Generate complete article from topic
     */
    public function generateArticle(ArticleTopic $topic, AutoPostConfig $config): array
    {
        \Log::info('🤖 Starting article generation', [
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
            'model' => $config->ai_model,
            'category' => $topic->category,
            'target_words' => $config->min_word_count . '-' . $config->max_word_count,
        ]);

        $startTime = microtime(true);
        
        \Log::info('📝 Preparing content generation prompt', ['topic_id' => $topic->id]);

        try {
            // Generate content
            $content = $this->generateContent($topic, $config);
            
            // Generate meta fields
            $title = $this->generateTitle($topic);
            $excerpt = $this->generateExcerpt($content);
            $metaTitle = $this->generateMetaTitle($topic);
            $metaDescription = $this->generateMetaDescription($topic);
            $metaKeywords = $this->generateKeywords($topic);
            $tags = $this->generateTags($topic);
            
            // Calculate reading time
            $readingTime = $this->calculateReadingTime($content);
            
            $generationTime = round((microtime(true) - $startTime) * 1000);
            
            \Log::info('✅ Article generated successfully', [
                'topic_id' => $topic->id,
                'generation_time_ms' => $generationTime,
                'word_count' => str_word_count(strip_tags($content)),
                'reading_time' => $readingTime,
            ]);

            return [
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
                'tags' => $tags,
                'reading_time' => $readingTime,
                'category' => $topic->category,
                'status' => $config->auto_publish ? 'published' : 'draft',
                'published_at' => $config->auto_publish ? now() : null,
            ];

        } catch (\Exception $e) {
            \Log::error('❌ Article generation failed', [
                'topic_id' => $topic->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate article title (may refine the topic title)
     */
    protected function generateTitle(ArticleTopic $topic): string
    {
        // Use topic title as-is, it's already optimized
        return $topic->title;
    }

    /**
     * Generate article content with structured sections
     */
    protected function generateContent(ArticleTopic $topic, AutoPostConfig $config): string
    {
        $prompt = $this->buildContentPrompt($topic, $config);
        
        $messages = [
            [
                'role' => 'system',
                'content' => $this->getSystemPrompt()
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        
        $options = [
            'model' => $config->ai_model,
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ];
        
        \Log::info('🌐 Calling OpenRouter API', [
            'model' => $options['model'],
            'max_tokens' => $options['max_tokens'],
        ]);
        
        $response = $this->openRouter->chat($messages, $options);
        
        if (!$response['success']) {
            \Log::error('❌ OpenRouter API failed', [
                'error' => $response['error'] ?? 'Unknown error',
                'details' => $response['details'] ?? null,
            ]);
            throw new \Exception('OpenRouter API error: ' . ($response['error'] ?? 'Unknown error'));
        }
        
        \Log::info('✅ OpenRouter API response received', [
            'content_length' => strlen($response['content']),
            'tokens_used' => $response['tokens_used'] ?? 'N/A',
        ]);
        
        $markdown = $response['content'];
        
        return $this->formatContent($markdown);
    }

    /**
     * Build comprehensive prompt for content generation
     */
    protected function buildContentPrompt(ArticleTopic $topic, AutoPostConfig $config): string
    {
        $keywords = $topic->keywords ? implode(', ', $topic->keywords) : 'perizinan usaha';
        
        return "
Tugas: Buat artikel blog profesional tentang \"{$topic->title}\" untuk situs konsultan perizinan.

**Konteks Bisnis:**
- Perusahaan: PT. Cangah Pajaratan Mandiri (bizmark.id)
- Layanan: Konsultan perizinan, dokumen lingkungan (UKL-UPL, AMDAL, SPPL), analisis bisnis
- Target Pembaca: Pemilik usaha, developer, industri yang memerlukan perizinan
- Expertise: 15+ tahun pengalaman perizinan & dokumen lingkungan

**Kategori Artikel:** {$topic->category}
**Kata Kunci Utama:** {$keywords}
**Deskripsi Topik:** {$topic->description}

**Requirements Konten:**
1. **Panjang:** {$config->min_word_count}-{$config->max_word_count} kata
2. **Struktur:**
   - Pembukaan menarik (2-3 paragraf: pain point pembaca, relevansi, promise)
   - {$config->min_headings}-{$config->max_headings} section dengan heading H2/H3 yang descriptive
   - Minimal {$config->min_paragraphs} paragraf substantif dengan contoh konkret
   - Kesimpulan + CTA natural (tidak hard-selling)
3. **Gaya Penulisan:**
   - Profesional namun mudah dipahami (hindari jargon berlebihan)
   - Praktis dan actionable (berikan langkah konkret jika applicable)
   - Sertakan contoh nyata/studi kasus jika relevan
   - Tone: Informatif, membantu, membangun kepercayaan
   - Gunakan bullet points untuk checklist/daftar
4. **SEO Best Practices:**
   - Gunakan kata kunci secara natural (jangan keyword stuffing)
   - Subheading yang descriptive dan search-friendly
   - Internal mention untuk topik terkait (tapi jangan buat link, cukup sebut topiknya)
   - Meta-friendly structure

**Format Output HTML:**
Gunakan struktur HTML yang bersih:
- <h2> untuk heading utama
- <h3> untuk sub-heading
- <p> untuk paragraf
- <ul><li> untuk bullet points
- <ol><li> untuk numbered list
- <strong> untuk emphasis penting
- <em> untuk emphasis ringan

**Contoh Struktur:**
<h2>Apa Itu [Topik]?</h2>
<p>Penjelasan konsep dasar...</p>

<h2>Mengapa [Topik] Penting?</h2>
<p>Manfaat dan pentingnya...</p>
<ul>
<li>Benefit 1</li>
<li>Benefit 2</li>
</ul>

<h2>Cara [Action Terkait Topik]</h2>
<p>Panduan step-by-step...</p>
<ol>
<li>Langkah 1</li>
<li>Langkah 2</li>
</ol>

<h2>Tips dan Best Practices</h2>
<p>Saran praktis...</p>

<h2>Kesimpulan</h2>
<p>Ringkasan + CTA natural (contoh: 'Butuh bantuan profesional? Tim kami siap membantu.')</p>

**PENTING:**
- Jangan copy-paste dari sumber lain, buat konten original
- Fokus pada value untuk pembaca, bukan promosi
- Jangan terlalu promotional, maksimal 1 mention jasa di akhir
- Sisipkan expertise perusahaan secara natural dalam konten
- Gunakan data/fakta konkret jika applicable (misal: timeline, biaya kisaran, regulasi)

HANYA BERIKAN HTML KONTEN, JANGAN SERTAKAN TITLE ATAU METADATA LAIN.
";
    }

    /**
     * System prompt for article generation
     */
    protected function getSystemPrompt(): string
    {
        return "Anda adalah content writer ahli untuk perusahaan konsultan perizinan dan dokumen lingkungan di Indonesia. 
Anda memahami mendalam:
- Regulasi perizinan usaha Indonesia (OSS, NIB, IMB, SLF, dll)
- Dokumen lingkungan (UKL-UPL, AMDAL, SPPL)
- Proses birokrasi dan kebutuhan pemilik usaha
- SEO best practices untuk artikel informatif

Tulis artikel yang:
- Informatif dan educational (bukan promotional)
- Praktis dengan contoh konkret
- SEO-friendly dengan struktur yang baik
- Mudah dipahami oleh non-experts
- Membangun trust dan authority

Gunakan tone profesional namun approachable, seperti konsultan yang menjelaskan kepada klien.";
    }

    /**
     * Format AI markdown output to clean HTML
     */
    protected function formatContent(string $content): string
    {
        // Remove markdown code blocks if AI wrapped the output
        $content = preg_replace('/```html\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        
        // Trim whitespace
        $content = trim($content);
        
        // Ensure proper spacing between elements
        $content = str_replace('</h2><p>', '</h2>' . PHP_EOL . '<p>', $content);
        $content = str_replace('</h3><p>', '</h3>' . PHP_EOL . '<p>', $content);
        $content = str_replace('</p><h2>', '</p>' . PHP_EOL . '<h2>', $content);
        $content = str_replace('</p><h3>', '</p>' . PHP_EOL . '<h3>', $content);
        
        return $content;
    }

    /**
     * Generate excerpt from content
     */
    protected function generateExcerpt(string $content): string
    {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Get first 200 characters
        $excerpt = Str::limit($text, 200, '...');
        
        return $excerpt;
    }

    /**
     * Generate meta title (optimized for SEO)
     */
    protected function generateMetaTitle(ArticleTopic $topic): string
    {
        // Keep it under 60 characters for SEO
        $metaTitle = $topic->title;
        
        if (strlen($metaTitle) > 60) {
            $metaTitle = Str::limit($metaTitle, 57, '...');
        }
        
        return $metaTitle;
    }

    /**
     * Generate meta description
     */
    protected function generateMetaDescription(ArticleTopic $topic): string
    {
        // Use topic description if available, otherwise generate
        if ($topic->description) {
            $description = $topic->description;
        } else {
            $description = "Panduan lengkap tentang {$topic->title}. Informasi terkini dari konsultan perizinan profesional.";
        }
        
        // Keep it under 160 characters for SEO
        if (strlen($description) > 160) {
            $description = Str::limit($description, 157, '...');
        }
        
        return $description;
    }

    /**
     * Generate meta keywords
     */
    protected function generateKeywords(ArticleTopic $topic): string
    {
        $keywords = $topic->keywords ?? [];
        
        // Add category-specific keywords
        $categoryKeywords = [
            'tips' => ['panduan', 'cara', 'tips'],
            'regulation' => ['regulasi', 'peraturan', 'undang-undang'],
            'case-study' => ['studi kasus', 'pengalaman', 'success story'],
            'news' => ['berita', 'update', 'terbaru'],
            'general' => ['perizinan', 'konsultan', 'jasa'],
        ];
        
        if (isset($categoryKeywords[$topic->category])) {
            $keywords = array_merge($keywords, $categoryKeywords[$topic->category]);
        }
        
        // Add common keywords
        $keywords[] = 'perizinan usaha';
        $keywords[] = 'Indonesia';
        
        // Remove duplicates and limit to 10 keywords
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        
        return implode(', ', $keywords);
    }

    /**
     * Generate tags from topic
     */
    protected function generateTags(ArticleTopic $topic): array
    {
        $tags = $topic->tags ?? [];
        
        // If no tags, generate from keywords
        if (empty($tags) && !empty($topic->keywords)) {
            $tags = array_slice($topic->keywords, 0, 5);
        }
        
        return $tags;
    }

    /**
     * Calculate reading time (words per minute)
     */
    protected function calculateReadingTime(string $content): int
    {
        $text = strip_tags($content);
        $wordCount = str_word_count($text);
        
        // Average reading speed: 200 words per minute
        $readingTime = ceil($wordCount / 200);
        
        return max(1, $readingTime); // Minimum 1 minute
    }

    /**
     * Estimate AI cost for generation
     */
    public function estimateCost(AutoPostConfig $config): float
    {
        // Rough estimate based on Claude 3.5 Sonnet pricing
        // Input: ~2000 tokens × $3/M = $0.006
        // Output: ~2500 tokens × $15/M = $0.0375
        // Total: ~$0.044 per article
        
        return 0.044;
    }
}
