<?php

namespace App\Services;

use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use Illuminate\Support\Facades\Log;
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
        Log::info('🤖 Starting article generation', [
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
            'model' => $config->ai_model,
            'category' => $topic->category,
            'target_words' => $config->min_word_count.'-'.$config->max_word_count,
        ]);

        $startTime = microtime(true);

        Log::info('📝 Preparing content generation prompt', ['topic_id' => $topic->id]);

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

            Log::info('✅ Article generated successfully', [
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
            Log::error('❌ Article generation failed', [
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
        if (! preg_match('/20\d{2}/', $title)) {
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
                'content' => $this->getSystemPrompt(),
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        $options = [
            'model' => $config->ai_model,
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ];

        Log::info('🌐 Calling OpenRouter API', [
            'model' => $options['model'],
            'max_tokens' => $options['max_tokens'],
        ]);

        $response = $this->openRouter->chat($messages, $options);

        if (! $response['success']) {
            Log::error('❌ OpenRouter API failed', [
                'error' => $response['error'] ?? 'Unknown error',
                'details' => $response['details'] ?? null,
            ]);
            throw new \Exception('OpenRouter API error: '.($response['error'] ?? 'Unknown error'));
        }

        Log::info('✅ OpenRouter API response received', [
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

        // Template-specific structure instructions based on category
        $templateInstructions = $this->getTemplateInstructions($topic);

        return "
{$languageContext['task_instruction']} \"{$topic->title}\" {$languageContext['for_site']}

**{$languageContext['business_context_title']}**
{$languageContext['company_info']}

**{$languageContext['article_category']}** {$topic->category}
**{$languageContext['main_keywords']}** {$keywords}
**{$languageContext['topic_description']}** {$topic->description}

{$templateInstructions}

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
     * Get template-specific structure instructions based on article category/type.
     * Produces differentiated content structure for Pillar, Comparison, Case Study, FAQ, and generic articles.
     */
    protected function getTemplateInstructions(ArticleTopic $topic): string
    {
        $title = $topic->title;
        $isId = $topic->language === 'id';

        // Detect template type from category + title patterns
        $type = $this->detectTemplateType($topic);

        return match ($type) {
            'pillar' => $isId
                ? '**TEMPLATE: PILLAR PAGE (Panduan Lengkap)**
IKUTI STRUKTUR INI DENGAN KETAT:
1. <h2>Apa itu [Topik]?</h2> — Definisi, dasar hukum, siapa yang wajib
2. <h2>Mengapa [Topik] Penting?</h2> — 5 alasan, konsekuensi jika tidak memiliki
3. <h2>Persyaratan Dokumen</h2> — Checklist detail dalam <ul>, tips persiapan
4. <h2>Proses Pengurusan Step-by-Step</h2> — Numbered list <ol> dengan timeline dan biaya estimasi
5. <h2>FAQ [Topik]</h2> — Minimal 8 pertanyaan-jawaban, gunakan format <h3> per pertanyaan
6. <h2>Studi Kasus</h2> — 1-2 contoh nyata (boleh fiktif realistis)
7. <h2>Tips dari Ahli Bizmark</h2> — Expert insights, kesalahan umum
8. <h2>Konsultasi Gratis Bizmark</h2> — CTA: WhatsApp, form, telepon
Target: 2000-3000 kata. Ini adalah halaman pilar — harus sangat komprehensif dan menjadi referensi utama.'
                : '**TEMPLATE: PILLAR PAGE (Comprehensive Guide)**
FOLLOW THIS STRUCTURE STRICTLY:
1. <h2>What is [Topic]?</h2> — Definition, legal basis, who needs it
2. <h2>Why is [Topic] Important?</h2> — 5 reasons, consequences of non-compliance
3. <h2>Document Requirements</h2> — Detailed <ul> checklist, preparation tips
4. <h2>Step-by-Step Process</h2> — Numbered <ol> list with timeline and cost estimates
5. <h2>FAQ [Topic]</h2> — Minimum 8 Q&A items, use <h3> per question
6. <h2>Case Study</h2> — 1-2 real examples
7. <h2>Expert Tips from Bizmark</h2> — Common mistakes, pro insights
8. <h2>Free Consultation</h2> — CTA
Target: 2000-3000 words. This is a pillar page — must be extremely comprehensive.',

            'comparison' => $isId
                ? '**TEMPLATE: ARTIKEL PERBANDINGAN**
IKUTI STRUKTUR INI DENGAN KETAT:
1. <h2>Tabel Perbandingan Cepat</h2> — Buat tabel HTML <table> dengan kolom: Aspek | Opsi A | Opsi B
2. <h2>Apa itu [Opsi A]?</h2> — Penjelasan detail
3. <h2>Apa itu [Opsi B]?</h2> — Penjelasan detail
4. <h2>Perbedaan Utama</h2> — Bullet list <ul> dengan 5+ poin perbandingan
5. <h2>Kapan Memilih [Opsi A]</h2> — Kondisi, keuntungan, contoh kasus
6. <h2>Kapan Memilih [Opsi B]</h2> — Kondisi, keuntungan, contoh kasus
7. <h2>FAQ Perbandingan</h2> — 5+ pertanyaan, format <h3>
8. <h2>Konsultasi Gratis Bizmark</h2> — CTA
WAJIB: Sertakan minimal 1 tabel HTML <table> perbandingan side-by-side.'
                : '**TEMPLATE: COMPARISON ARTICLE**
FOLLOW THIS STRUCTURE STRICTLY:
1. <h2>Quick Comparison Table</h2> — HTML <table> with columns: Aspect | Option A | Option B
2. <h2>What is [Option A]?</h2>
3. <h2>What is [Option B]?</h2>
4. <h2>Key Differences</h2> — 5+ bullet points
5. <h2>When to Choose [Option A]</h2>
6. <h2>When to Choose [Option B]</h2>
7. <h2>FAQ</h2> — 5+ questions in <h3>
8. <h2>Free Consultation</h2> — CTA
REQUIRED: Include at least 1 HTML <table> for side-by-side comparison.',

            'case-study' => $isId
                ? '**TEMPLATE: STUDI KASUS**
IKUTI STRUKTUR INI DENGAN KETAT:
1. <h2>Latar Belakang</h2> — Profil klien/industri (boleh anonim), situasi awal
2. <h2>Tantangan yang Dihadapi</h2> — 3-5 masalah spesifik dalam <ul>
3. <h2>Solusi Bizmark</h2> — Langkah-langkah penyelesaian dalam <ol>, pendekatan strategis
4. <h2>Hasil & Timeline</h2> — Data kuantitatif: hari penyelesaian, penghematan biaya, metrik sukses
5. <h2>Pelajaran untuk Anda</h2> — 3-5 takeaways dalam <ul>
6. <h2>Hubungi Bizmark</h2> — CTA: konsultasi serupa
Gaya penulisan: Naratif, storytelling, gunakan data spesifik (boleh realistis fiktif).'
                : "**TEMPLATE: CASE STUDY**
FOLLOW THIS STRUCTURE STRICTLY:
1. <h2>Background</h2> — Client/industry profile, initial situation
2. <h2>Challenges Faced</h2> — 3-5 specific problems in <ul>
3. <h2>Bizmark's Solution</h2> — Step-by-step resolution in <ol>
4. <h2>Results & Timeline</h2> — Quantitative data: days to completion, cost savings, success metrics
5. <h2>Lessons for You</h2> — 3-5 takeaways in <ul>
6. <h2>Contact Bizmark</h2> — CTA
Style: Narrative, storytelling, use specific data.",

            'faq' => $isId
                ? '**TEMPLATE: KOMPILASI FAQ**
IKUTI STRUKTUR INI DENGAN KETAT:
1. <h2>Pertanyaan Umum</h2> — 5-8 Q&A umum, setiap pertanyaan dalam <h3>, jawaban dalam <p>
2. <h2>Pertanyaan Teknis</h2> — 5-8 Q&A teknis dengan detail prosedur
3. <h2>Pertanyaan Biaya & Waktu</h2> — 5-8 Q&A tentang estimasi biaya dan timeline
4. <h2>Masih Ada Pertanyaan?</h2> — CTA ke konsultasi Bizmark
PENTING: Gunakan format konsisten <h3>Pertanyaan?</h3><p>Jawaban lengkap.</p> untuk setiap item.
Minimal 15 pertanyaan total. Format ini optimal untuk Google Featured Snippets dan Schema FAQPage.'
                : '**TEMPLATE: FAQ COMPILATION**
FOLLOW THIS STRUCTURE STRICTLY:
1. <h2>General Questions</h2> — 5-8 Q&A, each question in <h3>, answer in <p>
2. <h2>Technical Questions</h2> — 5-8 technical Q&A with procedural detail
3. <h2>Cost & Timeline Questions</h2> — 5-8 Q&A about estimates
4. <h2>Still Have Questions?</h2> — CTA
IMPORTANT: Use consistent <h3>Question?</h3><p>Detailed answer.</p> format.
Minimum 15 questions total. This format is optimal for Google Featured Snippets and Schema FAQPage.',

            default => '', // Generic articles use the base prompt structure
        };
    }

    /**
     * Detect content template type from topic category and title patterns.
     */
    protected function detectTemplateType(ArticleTopic $topic): string
    {
        $category = $topic->category;
        $title = mb_strtolower($topic->title);

        // Explicit category matches
        if ($category === 'case-study') {
            return 'case-study';
        }

        // Title pattern detection
        if (preg_match('/panduan lengkap|complete guide|comprehensive|guide to|langkah.langkah|step.by.step/i', $title)) {
            return 'pillar';
        }

        if (preg_match('/\bvs\b|versus|perbandingan|perbedaan|dibanding|comparison|compare|mana yang/i', $title)) {
            return 'comparison';
        }

        if (preg_match('/studi kasus|case study|berhasil|sukses.*mendapat|kisah|pengalaman/i', $title)) {
            return 'case-study';
        }

        if (preg_match('/\bfaq\b|pertanyaan.*sering|tanya jawab|questions|yang perlu diketahui|hal.*harus.*tahu/i', $title)) {
            return 'faq';
        }

        // Category-based fallback for regulation → pillar (comprehensive by nature)
        if ($category === 'regulation' && mb_strlen($title) > 30) {
            return 'pillar';
        }

        return 'generic';
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
- Target Readers: '.($targetMarket === 'pma' ? 'Foreign investors (PMA), international businesses, expat entrepreneurs' : 'Business owners, developers, industries').'
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
- '.($targetMarket === 'pma' ? 'Address specific PMA/foreign investment concerns and regulations' : 'Focus on local Indonesian business context'),
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
        return 'Anda adalah content writer ahli untuk perusahaan konsultan perizinan dan dokumen lingkungan di Indonesia. 
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

Gunakan tone profesional namun approachable, seperti konsultan yang menjelaskan kepada klien.';
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
        $content = str_replace('</h2><p>', '</h2>'.PHP_EOL.'<p>', $content);
        $content = str_replace('</h3><p>', '</h3>'.PHP_EOL.'<p>', $content);
        $content = str_replace('</p><h2>', '</p>'.PHP_EOL.'<h2>', $content);
        $content = str_replace('</p><h3>', '</p>'.PHP_EOL.'<h3>', $content);

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
        $excerpt = trim($excerpt).'. Selengkapnya di Bizmark.';

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
        if (! $hasPowerWord && mb_strlen($base) + 10 <= $maxBase) {
            $base = 'Panduan '.$base;
        }

        // Truncate base to fit within limit
        if (mb_strlen($base) > $maxBase) {
            $base = Str::limit($base, $maxBase - 1, '');
            // Clean trailing partial word
            $base = preg_replace('/\s+\S*$/', '', $base);
        }

        $metaTitle = trim($base).$suffix;

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
            fn ($w) => mb_strlen($w) > 3 && ! in_array($w, ['yang', 'untuk', 'dari', 'dengan', 'dalam'])
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
        $description = $base.'. '.$cta;

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
                if (count($keywords) >= 5) {
                    break;
                }
                if (! in_array($filler, $keywords)) {
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
        if (empty($tags) && ! empty($topic->keywords)) {
            $tags = array_slice($topic->keywords, 0, 5);
        }

        // Ensure minimum 3 tags for SEO score (excerpt_schema factor needs ≥2)
        if (count($tags) < 3) {
            $fillers = [$topic->category, 'Perizinan', 'Bizmark', 'Indonesia'];
            foreach ($fillers as $filler) {
                if ($filler && count($tags) < 3 && ! in_array($filler, $tags)) {
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
