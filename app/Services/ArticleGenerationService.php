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
        $title = $topic->title;

        // Ensure year in title for SEO freshness signal
        $year = date('Y');
        if (!preg_match('/20\d{2}/', $title)) {
            // Add year naturally
            if (mb_strlen($title) + strlen(" $year") <= 80) {
                $title .= " $year";
            }
        }

        return $title;
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
        $keywords = $topic->keywords ? implode(', ', $topic->keywords) : ($topic->language === 'en' ? 'business licensing' : 'perizinan usaha');
        
        // Enforce minimum word count for SEO (content score needs ≥800 for +5, ≥1500 for +8)
        $minWords = max($config->min_word_count, 800);
        $maxWords = max($config->max_word_count, $minWords + 500);
        
        // Enforce minimum heading counts for SEO (headings score needs ≥3 H2, ≥2 H3)
        $minHeadings = max($config->min_headings, 3);
        $maxHeadings = max($config->max_headings, $minHeadings + 2);
        
        // Language-specific context
        $languageContext = $this->getLanguageContext($topic->language, $topic->target_market);
        
        return "
{$languageContext['task_instruction']} \"{$topic->title}\" {$languageContext['for_site']}

**{$languageContext['business_context_title']}**
{$languageContext['company_info']}

**{$languageContext['article_category']}** {$topic->category}
**{$languageContext['main_keywords']}** {$keywords}
**{$languageContext['topic_description']}** {$topic->description}

**{$languageContext['content_requirements']}**
1. **{$languageContext['length']}** {$minWords}-{$maxWords} {$languageContext['words']}
2. **{$languageContext['structure']}**
   - {$languageContext['opening']} (2-3 {$languageContext['paragraphs']}: {$languageContext['pain_point']})
   - WAJIB minimal {$minHeadings} heading <h2> dan minimal 2 heading <h3> di bawah h2 yang relevan
   - {$languageContext['minimum']} {$config->min_paragraphs} {$languageContext['substantive_paragraphs']}
   - WAJIB minimal 1 bullet list (<ul><li>) ATAU numbered list (<ol><li>)
   - WAJIB gunakan <strong> untuk minimal 5 kata/frasa penting
   - {$languageContext['conclusion_cta']}
3. **{$languageContext['writing_style']}**
   - {$languageContext['professional_tone']}
   - {$languageContext['practical_actionable']}
   - {$languageContext['include_examples']}
   - {$languageContext['tone_description']}
   - {$languageContext['use_bullets']}
4. **{$languageContext['seo_practices']}**
   - {$languageContext['natural_keywords']}
   - {$languageContext['descriptive_subheadings']}
   - {$languageContext['internal_mentions']}
   - {$languageContext['meta_friendly']}

**{$languageContext['html_format']}**
{$languageContext['html_instructions']}

**{$languageContext['example_structure']}**
{$languageContext['structure_example']}

**{$languageContext['important']}**
{$languageContext['important_points']}

**SEO STRUCTURE CHECKLIST (WAJIB DIPENUHI):**
- Minimal {$minHeadings} heading <h2> (bukan <h1>)
- Minimal 2 heading <h3> di bawah <h2> yang relevan
- Minimal 1 <ul> atau <ol> list
- Minimal 5 penggunaan <strong> untuk kata kunci penting
- Minimal 7 paragraf <p>
- Panjang konten: {$minWords}-{$maxWords} kata

{$languageContext['output_instruction']}
";
    }

    /**
     * Get language-specific context for prompts
     */
    protected function getLanguageContext(string $language, string $targetMarket): array
    {
        if ($language === 'en') {
            return [
                'task_instruction' => 'Task: Create a professional blog article about',
                'for_site' => 'for a business licensing consultancy website.',
                'business_context_title' => 'Business Context:',
                'company_info' => '- Company: PT. Cangah Pajaratan Mandiri (bizmark.id)
- Services: Business licensing consultant, environmental documents (UKL-UPL, AMDAL, SPPL), business analysis
- Target Readers: ' . ($targetMarket === 'pma' ? 'Foreign investors (PMA), international businesses, expat entrepreneurs' : 'Business owners, developers, industries') . '
- Expertise: 15+ years experience in licensing & environmental documentation',
                'article_category' => 'Article Category:',
                'main_keywords' => 'Main Keywords:',
                'topic_description' => 'Topic Description:',
                'content_requirements' => 'Content Requirements:',
                'length' => 'Length:',
                'words' => 'words',
                'structure' => 'Structure:',
                'opening' => 'Engaging opening',
                'paragraphs' => 'paragraphs',
                'pain_point' => 'reader pain point, relevance, promise',
                'sections' => 'sections with descriptive H2/H3 headings',
                'minimum' => 'Minimum',
                'substantive_paragraphs' => 'substantive paragraphs with concrete examples',
                'conclusion_cta' => 'Conclusion + natural CTA (not hard-selling)',
                'writing_style' => 'Writing Style:',
                'professional_tone' => 'Professional yet easy to understand (avoid excessive jargon)',
                'practical_actionable' => 'Practical and actionable (provide concrete steps when applicable)',
                'include_examples' => 'Include real examples/case studies when relevant',
                'tone_description' => 'Tone: Informative, helpful, trust-building',
                'use_bullets' => 'Use bullet points for checklists/lists',
                'seo_practices' => 'SEO Best Practices:',
                'natural_keywords' => 'Use keywords naturally (no keyword stuffing)',
                'descriptive_subheadings' => 'Descriptive and search-friendly subheadings',
                'internal_mentions' => 'Mention related topics internally (just mention, don\'t create links)',
                'meta_friendly' => 'Meta-friendly structure',
                'html_format' => 'HTML Output Format:',
                'html_instructions' => 'Use clean HTML structure:
- <h2> for main headings
- <h3> for sub-headings
- <p> for paragraphs
- <ul><li> for bullet points
- <ol><li> for numbered lists
- <strong> for important emphasis
- <em> for light emphasis',
                'example_structure' => 'Example Structure:',
                'structure_example' => '<h2>What is [Topic]?</h2>
<p>Basic concept explanation...</p>

<h2>Why [Topic] Matters?</h2>
<p>Benefits and importance...</p>
<ul>
<li>Benefit 1</li>
<li>Benefit 2</li>
</ul>

<h2>How to [Topic-Related Action]</h2>
<p>Step-by-step guide...</p>
<ol>
<li>Step 1</li>
<li>Step 2</li>
</ol>

<h2>Tips and Best Practices</h2>
<p>Practical advice...</p>

<h2>Conclusion</h2>
<p>Summary + natural CTA (example: \'Need professional assistance? Our team is ready to help.\')</p>',
                'important' => 'IMPORTANT:',
                'important_points' => '- Don\'t copy-paste from other sources, create original content
- Focus on value for readers, not promotion
- Don\'t be too promotional, maximum 1 service mention at the end
- Include company expertise naturally in content
- Use concrete data/facts when applicable (timeline, cost ranges, regulations)
- ' . ($targetMarket === 'pma' ? 'Address specific PMA/foreign investment concerns and regulations' : 'Focus on local Indonesian business context'),
                'output_instruction' => 'PROVIDE ONLY HTML CONTENT, DO NOT INCLUDE TITLE OR OTHER METADATA.',
            ];
        }

        // Indonesian (default)
        return [
            'task_instruction' => 'Tugas: Buat artikel blog profesional tentang',
            'for_site' => 'untuk situs konsultan perizinan.',
            'business_context_title' => 'Konteks Bisnis:',
            'company_info' => '- Perusahaan: PT. Cangah Pajaratan Mandiri (bizmark.id)
- Layanan: Konsultan perizinan, dokumen lingkungan (UKL-UPL, AMDAL, SPPL), analisis bisnis
- Target Pembaca: Pemilik usaha lokal, developer, industri yang memerlukan perizinan
- Expertise: 15+ tahun pengalaman perizinan & dokumen lingkungan',
            'article_category' => 'Kategori Artikel:',
            'main_keywords' => 'Kata Kunci Utama:',
            'topic_description' => 'Deskripsi Topik:',
            'content_requirements' => 'Requirements Konten:',
            'length' => 'Panjang:',
            'words' => 'kata',
            'structure' => 'Struktur:',
            'opening' => 'Pembukaan menarik',
            'paragraphs' => 'paragraf',
            'pain_point' => 'pain point pembaca, relevansi, promise',
            'sections' => 'section dengan heading H2/H3 yang descriptive',
            'minimum' => 'Minimal',
            'substantive_paragraphs' => 'paragraf substantif dengan contoh konkret',
            'conclusion_cta' => 'Kesimpulan + CTA natural (tidak hard-selling)',
            'writing_style' => 'Gaya Penulisan:',
            'professional_tone' => 'Profesional namun mudah dipahami (hindari jargon berlebihan)',
            'practical_actionable' => 'Praktis dan actionable (berikan langkah konkret jika applicable)',
            'include_examples' => 'Sertakan contoh nyata/studi kasus jika relevan',
            'tone_description' => 'Tone: Informatif, membantu, membangun kepercayaan',
            'use_bullets' => 'Gunakan bullet points untuk checklist/daftar',
            'seo_practices' => 'SEO Best Practices:',
            'natural_keywords' => 'Gunakan kata kunci secara natural (jangan keyword stuffing)',
            'descriptive_subheadings' => 'Subheading yang descriptive dan search-friendly',
            'internal_mentions' => 'Internal mention untuk topik terkait (tapi jangan buat link, cukup sebut topiknya)',
            'meta_friendly' => 'Meta-friendly structure',
            'html_format' => 'Format Output HTML:',
            'html_instructions' => 'Gunakan struktur HTML yang bersih:
- <h2> untuk heading utama
- <h3> untuk sub-heading
- <p> untuk paragraf
- <ul><li> untuk bullet points
- <ol><li> untuk numbered list
- <strong> untuk emphasis penting
- <em> untuk emphasis ringan',
            'example_structure' => 'Contoh Struktur:',
            'structure_example' => '<h2>Apa Itu [Topik]?</h2>
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
<p>Ringkasan + CTA natural (contoh: \'Butuh bantuan profesional? Tim kami siap membantu.\')</p>',
            'important' => 'PENTING:',
            'important_points' => '- Jangan copy-paste dari sumber lain, buat konten original
- Fokus pada value untuk pembaca, bukan promosi
- Jangan terlalu promotional, maksimal 1 mention jasa di akhir
- Sisipkan expertise perusahaan secara natural dalam konten
- Gunakan data/fakta konkret jika applicable (misal: timeline, biaya kisaran, regulasi)',
            'output_instruction' => 'HANYA BERIKAN HTML KONTEN, JANGAN SERTAKAN TITLE ATAU METADATA LAIN.',
        ];
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
     * Generate excerpt from content (SEO: ≥100 chars for excerpt_schema score)
     */
    protected function generateExcerpt(string $content): string
    {
        $text = strip_tags($content);
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // Target 120-200 chars for optimal SEO score
        $excerpt = Str::limit($text, 180, '');

        // Ensure minimum 100 chars
        if (mb_strlen($excerpt) < 100 && mb_strlen($text) >= 100) {
            $excerpt = Str::limit($text, 180, '');
        }

        // Append brief CTA
        $excerpt = trim($excerpt) . '. Selengkapnya di Bizmark.';

        return Str::limit($excerpt, 250, '');
    }

    /**
     * Generate meta title (SEO-optimized: year + Bizmark + power words, ≤55 chars)
     * Must differ from article title for +2 SEO score bonus
     */
    protected function generateMetaTitle(ArticleTopic $topic): string
    {
        $year = date('Y');
        $title = $topic->title;

        // Power words that boost CTR and SEO score
        $powerWords = ['Panduan', 'Tips', 'Lengkap', 'Terbaru', 'Update', 'Cara'];
        $hasPowerWord = false;
        foreach ($powerWords as $pw) {
            if (Str::contains($title, $pw, true)) {
                $hasPowerWord = true;
                break;
            }
        }

        // Build optimized meta title: "Topic Keyword Year | Bizmark"
        $suffix = " $year | Bizmark";
        $maxBase = 55 - mb_strlen($suffix); // Reserve space for suffix

        // Extract core topic (remove existing year/brand if present)
        $base = preg_replace('/\s*20\d{2}\s*/', ' ', $title);
        $base = preg_replace('/\s*\|?\s*[Bb]izmark\s*/', '', $base);
        $base = trim($base);

        // Add power word if missing and space allows
        if (!$hasPowerWord && mb_strlen($base) + 10 <= $maxBase) {
            $base = 'Panduan ' . $base;
        }

        // Truncate base to fit within limit
        if (mb_strlen($base) > $maxBase) {
            $base = Str::limit($base, $maxBase - 1, '');
            // Clean trailing partial word
            $base = preg_replace('/\s+\S*$/', '', $base);
        }

        $metaTitle = trim($base) . $suffix;

        return $metaTitle;
    }

    /**
     * Generate meta description (SEO-optimized: 130-155 chars, CTA, keywords)
     */
    protected function generateMetaDescription(ArticleTopic $topic): string
    {
        // Extract keywords from title for SEO relevance matching
        $titleWords = array_filter(
            explode(' ', strtolower($topic->title)),
            fn($w) => mb_strlen($w) > 3 && !in_array($w, ['yang', 'untuk', 'dari', 'dengan', 'dalam'])
        );
        $keyPhrase = implode(' ', array_slice($titleWords, 0, 3));

        // CTA phrases that boost meta_description score
        $ctaPhrases = [
            'Konsultasi gratis di Bizmark!',
            'Hubungi Bizmark untuk bantuan profesional!',
            'Pelajari selengkapnya di Bizmark.',
        ];
        $cta = $ctaPhrases[array_rand($ctaPhrases)];

        if ($topic->description && mb_strlen($topic->description) >= 60) {
            $base = $topic->description;
        } else {
            $base = "Panduan lengkap tentang {$keyPhrase}. Informasi terkini {$topic->title} dari konsultan perizinan profesional.";
        }

        // Trim base and append CTA to hit 130-155 chars
        $targetLen = 150 - mb_strlen($cta) - 2; // -2 for ". " separator
        if (mb_strlen($base) > $targetLen) {
            $base = Str::limit($base, $targetLen, '');
            $base = preg_replace('/\s+\S*$/', '', $base);
        }

        // Ensure clean sentence end before CTA
        $base = rtrim(trim($base), '.,;:!?');
        $description = $base . '. ' . $cta;

        // Final safety: cap at 160
        if (mb_strlen($description) > 160) {
            $description = Str::limit($description, 157, '...');
        }

        return $description;
    }

    /**
     * Generate meta keywords (SEO-optimized: 5-8 keywords with Bizmark)
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

        // Add common keywords + brand (required for SEO score)
        $keywords[] = 'perizinan usaha';
        $keywords[] = 'Bizmark';
        $keywords[] = 'Indonesia';

        // Remove duplicates and target 5-8 keywords
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 8);

        // Ensure minimum 5 keywords
        if (count($keywords) < 5) {
            $fillers = ['konsultan perizinan', 'izin usaha', 'dokumen lingkungan', 'OSS', 'NIB'];
            foreach ($fillers as $filler) {
                if (count($keywords) >= 5) break;
                if (!in_array($filler, $keywords)) {
                    $keywords[] = $filler;
                }
            }
        }

        return implode(', ', $keywords);
    }

    /**
     * Generate tags from topic (SEO: minimum 3 tags for excerpt_schema score)
     */
    protected function generateTags(ArticleTopic $topic): array
    {
        $tags = $topic->tags ?? [];

        // If no tags, generate from keywords
        if (empty($tags) && !empty($topic->keywords)) {
            $tags = array_slice($topic->keywords, 0, 5);
        }

        // Ensure minimum 3 tags for SEO score (excerpt_schema factor needs ≥2)
        if (count($tags) < 3) {
            $fillers = [$topic->category, 'Perizinan', 'Bizmark', 'Indonesia'];
            foreach ($fillers as $filler) {
                if ($filler && count($tags) < 3 && !in_array($filler, $tags)) {
                    $tags[] = $filler;
                }
            }
        }

        return array_values(array_unique($tags));
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
