<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Models\AutoPostSchedule;
use App\Models\AutoPostLog;
use App\Jobs\GenerateAutoPostArticle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ArticleAutoPostService
{
    protected ArticleGenerationService $generationService;
    protected InternalLinkService $linkService;
    protected TopicSimilarityService $similarityService;
    protected ArticleQualityService $qualityService;
    protected PexelsService $pexelsService;
    protected SeoScoringService $seoScorer;
    protected SeoFixService $seoFixer;

    public function __construct(
        ArticleGenerationService $generationService,
        InternalLinkService $linkService,
        TopicSimilarityService $similarityService,
        ArticleQualityService $qualityService,
        PexelsService $pexelsService,
        SeoScoringService $seoScorer,
        SeoFixService $seoFixer
    ) {
        $this->generationService = $generationService;
        $this->linkService = $linkService;
        $this->similarityService = $similarityService;
        $this->qualityService = $qualityService;
        $this->pexelsService = $pexelsService;
        $this->seoScorer = $seoScorer;
        $this->seoFixer = $seoFixer;
    }

    /**
     * Main workflow for generating and posting article
     */
    public function executeScheduledPost(AutoPostSchedule $schedule): Article
    {
        $startTime = microtime(true);
        
        // Mark as processing (don't increment attempts — already done by the queue job)
        $schedule->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
        
        \Log::info('🚀 Starting scheduled article generation', [
            'schedule_id' => $schedule->id,
            'topic_id' => $schedule->topic_id,
            'topic_title' => $schedule->topic->title ?? 'N/A',
            'attempt' => $schedule->attempts,
            'timestamp' => now()->toDateTimeString(),
        ]);

        AutoPostLog::create([
            'schedule_id' => $schedule->id,
            'topic_id' => $schedule->topic_id,
            'level' => 'info',
            'event' => 'generation_started',
            'message' => '🚀 Starting article generation for: ' . ($schedule->topic->title ?? 'Unknown Topic') . ' (Attempt #' . $schedule->attempts . ')',
            'context' => ['timestamp' => now()->toDateTimeString()],
        ]);

        try {
            // 1. Load configuration
            \Log::info('⚙️  Loading configuration', ['schedule_id' => $schedule->id]);
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'level' => 'info',
                'event' => 'config_loaded',
                'message' => '⚙️  Loading auto-post configuration',
            ]);
            
            $config = AutoPostConfig::current();
            
            if (!$config->is_enabled) {
                throw new \Exception('Auto-posting is disabled in configuration');
            }
            
            // Set timeout based on config
            set_time_limit($config->timeout_seconds ?? 120);
            
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'level' => 'success',
                'event' => 'config_validated',
                'message' => '✅ Configuration validated and ready',
                'context' => ['model' => $config->ai_model, 'auto_publish' => $config->auto_publish, 'timeout' => $config->timeout_seconds],
            ]);
            
            // 2. Load topic
            $topic = $schedule->topic;
            
            if (!$topic) {
                throw new \Exception("Topic not found for schedule #{$schedule->id}");
            }
            
            \Log::info('📋 Topic loaded', ['topic_id' => $topic->id, 'title' => $topic->title]);
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'level' => 'info',
                'event' => 'topic_loaded',
                'message' => '📋 Topic loaded: ' . $topic->title,
                'context' => ['category' => $topic->category, 'keywords' => $topic->keywords],
            ]);
            
            // 3. Check for duplicates
            \Log::info('🔍 Checking for duplicate topics', ['topic_id' => $topic->id]);
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'level' => 'info',
                'event' => 'duplicate_check_started',
                'message' => '🔍 Checking for duplicate content',
            ]);
            
            if ($this->similarityService->isDuplicate($topic, $config->duplicate_threshold)) {
                throw new \Exception("Topic is too similar to recent articles (duplicate detected)");
            }
            
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'level' => 'success',
                'event' => 'duplicate_check_passed',
                'message' => '✅ No duplicate content detected',
            ]);
            
            // 4. Mark topic as processing
            $topic->markAsProcessing();
            
            // 5. Generate content via AI
            \Log::info('🤖 Starting AI generation', ['topic_id' => $topic->id, 'model' => $config->ai_model]);
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'level' => 'info',
                'event' => 'ai_generation_started',
                'message' => '🤖 Generating article content with AI',
                'context' => ['model' => $config->ai_model, 'temperature' => 0.7],
            ]);
            
            $articleData = $this->generationService->generateArticle($topic, $config);
            
            $wordCount = str_word_count(strip_tags($articleData['content']));
            \Log::info('✅ AI generation completed', ['word_count' => $wordCount, 'reading_time' => $articleData['reading_time']]);
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'article_id' => null,
                'level' => 'success',
                'event' => 'ai_generation_completed',
                'message' => '✅ AI content generation completed successfully',
                'context' => [
                    'word_count' => $wordCount,
                    'reading_time' => $articleData['reading_time'],
                    'sections' => substr_count($articleData['content'], '<h2>') + substr_count($articleData['content'], '<h3>'),
                ],
                'word_count' => $wordCount,
                'reading_time' => $articleData['reading_time'],
            ]);
            
            // 6. Validate quality
            $qualityCheck = $this->qualityService->validateQuality($articleData, $config);
            
            if (!$qualityCheck['valid']) {
                // Only log failure if quality is critically low
                if ($qualityCheck['quality_score'] < 50) {
                    AutoPostLog::logError('quality_check_failed', 'Article quality too low - rejected', [
                        'topic_id' => $topic->id,
                        'context' => [
                            'issues' => $qualityCheck['issues'],
                            'quality_score' => $qualityCheck['quality_score'],
                        ],
                    ]);
                    throw new \Exception('Article quality too low: ' . implode(', ', $qualityCheck['issues']));
                }
                
                // Log warning for marginal quality but continue
                AutoPostLog::logWarning('quality_check_warning', 'Article quality marginal but acceptable', [
                    'topic_id' => $topic->id,
                    'context' => [
                        'issues' => $qualityCheck['issues'],
                        'quality_score' => $qualityCheck['quality_score'],
                    ],
                ]);
            }
            
            AutoPostLog::logSuccess('quality_check_passed', 'Article quality validation passed', [
                'topic_id' => $topic->id,
                'context' => [
                    'quality_score' => $qualityCheck['quality_score'],
                    'warnings' => count($qualityCheck['warnings'] ?? []),
                ],
            ]);
            
            // 7. Add internal links + "Baca juga" section (SEO: +7 for ≥5 links, +3 for "Baca juga")
            AutoPostLog::logInfo('internal_links_processing', 'Adding internal links', [
                'topic_id' => $topic->id,
                'context' => ['target_count' => max($config->internal_links_count, 5)],
            ]);
            
            // Use at least 5 internal links for maximum SEO score
            $targetLinkCount = max($config->internal_links_count, 5);
            $articleData['content'] = $this->linkService->addInternalLinks(
                $articleData['content'],
                $topic,
                $targetLinkCount
            );

            // 7.1 Inject pSEO cross-links (article → city×service pages)
            $articleData['content'] = $this->linkService->injectPseoLinks(
                $articleData['content'],
                null,
                2
            );
            
            // Ensure "Baca juga" / "Artikel Terkait" section exists for +3 SEO bonus
            $articleData['content'] = $this->ensureBacaJugaSection($articleData['content'], $topic);
            
            $linkStats = $this->linkService->validateLinks($articleData['content']);
            
            AutoPostLog::logSuccess('internal_links_added', 'Internal links added successfully', [
                'topic_id' => $topic->id,
                'internal_links' => $linkStats['internal_links'],
            ]);
            
            // 7.5. Fetch featured image from Pexels
            if ($config->include_featured_image) {
                $articleData['featured_image'] = $this->fetchFeaturedImage($topic, $schedule);
            }
            
            // 8. Create article
            $article = $this->createArticle($articleData, $topic);
            
            \Log::info('📝 Article created in database', [
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => $article->status,
            ]);
            
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'topic_id' => $topic->id,
                'level' => 'success',
                'event' => 'article_created',
                'message' => '📝 Article created in database: ' . $article->title,
                'context' => [
                    'article_id' => $article->id,
                    'status' => $article->status,
                    'slug' => $article->slug,
                ],
                'word_count' => str_word_count(strip_tags($article->content)),
                'reading_time' => $article->reading_time,
            ]);
            
            // 8.5. SEO Compliance: Score and auto-fix before publishing
            $seoResult = $this->applySeoCompliance($article, $schedule);
            
            // 9. Mark topic as published
            $topic->markAsPublished($article->id);
            
            // 10. Calculate generation time and cost
            $generationTime = (int) round(microtime(true) - $startTime);
            $estimatedCost = $this->generationService->estimateCost($config);
            
            // 11. Final success log
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'topic_id' => $topic->id,
                'level' => 'success',
                'event' => 'article_published',
                'message' => '🚀 Article auto-posted successfully: ' . $article->title . ' (SEO: ' . ($seoResult['final_score'] ?? '?') . '/100)',
                'context' => [
                    'article_id' => $article->id,
                    'url' => url('/blog/' . $article->slug),
                    'quality_score' => $qualityCheck['quality_score'],
                    'seo_score' => $seoResult['final_score'] ?? null,
                    'seo_grade' => $seoResult['grade'] ?? null,
                    'seo_fixes_applied' => $seoResult['fixes_count'] ?? 0,
                    'ai_cost' => $estimatedCost,
                ],
                'word_count' => $qualityCheck['metrics']['word_count'],
                'reading_time' => $article->reading_time,
                'internal_links' => $linkStats['internal_links'],
                'ai_cost' => $estimatedCost,
            ]);
            
            \Log::info('✅ Article auto-posted successfully', [
                'article_id' => $article->id,
                'title' => $article->title,
                'generation_time' => $generationTime . 's',
                'quality_score' => $qualityCheck['quality_score'],
            ]);
            
            return $article;
            
        } catch (\Exception $e) {
            $generationTime = (int) round(microtime(true) - $startTime);

            $errorMessage = $e->getMessage();
            $errorLower = Str::lower($errorMessage);
            $isDuplicateFailure = Str::contains($errorLower, 'duplicate detected');
            $isNonRetryableFailure = $isDuplicateFailure || Str::contains($errorLower, 'topic not found');
            
            AutoPostLog::logError('article_generation_failed', $e->getMessage(), [
                'schedule_id' => $schedule->id,
                'topic_id' => $schedule->topic_id,
            ]);
            
            // Duplicate/topic-not-found failures are deterministic and should not consume retries.
            $shouldRetry = !$isNonRetryableFailure
                && $schedule->attempts < ($config->retry_attempts ?? 3);

            // For duplicate topics, auto-assign replacement topic to keep pipeline moving.
            if ($isDuplicateFailure && isset($topic)) {
                $replacementTopic = ArticleTopic::available()
                    ->where('id', '!=', $topic->id)
                    ->orderBy('priority', 'desc')
                    ->first();

                if ($replacementTopic) {
                    $schedule->update([
                        'topic_id' => $replacementTopic->id,
                        'status' => 'pending',
                        'started_at' => null,
                        'completed_at' => null,
                        'error_message' => 'Auto-reassigned due to duplicate topic: ' . Str::limit($topic->title, 120),
                        'generation_time_seconds' => $generationTime,
                    ]);

                    // Keep old topic marked failed to avoid repeated selection.
                    $topic->markAsFailed();
                    // Reserve replacement topic for this schedule slot.
                    $replacementTopic->markAsScheduled($schedule->scheduled_at);

                    AutoPostLog::logWarning('schedule_topic_reassigned', 'Schedule reassigned to replacement topic after duplicate detection', [
                        'schedule_id' => $schedule->id,
                        'topic_id' => $replacementTopic->id,
                        'context' => [
                            'old_topic_id' => $topic->id,
                            'old_topic_title' => $topic->title,
                            'new_topic_title' => $replacementTopic->title,
                        ],
                    ]);

                    // Re-dispatch immediately so due schedules can still complete without manual intervention.
                    GenerateAutoPostArticle::dispatch($schedule->fresh())->onQueue('default');

                    \Log::warning('🔁 Schedule auto-reassigned after duplicate detection', [
                        'schedule_id' => $schedule->id,
                        'old_topic_id' => $topic->id,
                        'new_topic_id' => $replacementTopic->id,
                    ]);

                    // Stop here: this failure has been handled by reassignment.
                    throw new \RuntimeException('Duplicate topic handled by automatic reassignment');
                }
            }
            
            $schedule->update([
                'status' => $shouldRetry ? 'pending' : 'failed',
                'error_message' => substr($errorMessage, 0, 500),
                'generation_time_seconds' => $generationTime,
                'completed_at' => now(),
            ]);
            
            // Mark topic as failed only if max retries exceeded
            if (isset($topic)) {
                if (!$shouldRetry) {
                    $topic->markAsFailed();
                } else {
                    // Clear scheduling for retry
                    $topic->clearScheduling();
                }
            }
            
            \Log::error('❌ Article generation failed', [
                'schedule_id' => $schedule->id,
                'attempt' => $schedule->attempts,
                'will_retry' => $shouldRetry,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Fetch a relevant featured image from Pexels based on topic context
     */
    protected function fetchFeaturedImage(ArticleTopic $topic, AutoPostSchedule $schedule): ?string
    {
        AutoPostLog::logInfo('featured_image_search', '🖼️ Searching for featured image on Pexels', [
            'schedule_id' => $schedule->id,
            'topic_id' => $topic->id,
        ]);

        // Build search queries from topic context, ordered by specificity
        $queries = $this->buildImageSearchQueries($topic);

        $bestPhoto = null;
        $bestScore = -1;
        $bestQuery = null;

        foreach ($queries as $query) {
            try {
                $results = $this->pexelsService->searchPhotos($query, 10, 1, [
                    'orientation' => 'landscape',
                    'size' => 'large',
                    'locale' => 'id-ID',
                ]);

                if (!empty($results['photos'])) {
                    foreach ($results['photos'] as $photo) {
                        $score = $this->scorePhotoCandidate($photo, $query, $topic);
                        if ($score > $bestScore) {
                            $bestScore = $score;
                            $bestPhoto = $photo;
                            $bestQuery = $query;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Pexels search failed for query', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Last-resort fallback to curated image if all query results are too weak.
        if (!$bestPhoto) {
            try {
                $curated = $this->pexelsService->getCuratedPhotos(10, 1);
                foreach ($curated['photos'] ?? [] as $photo) {
                    $score = $this->scorePhotoCandidate($photo, '', $topic);
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestPhoto = $photo;
                        $bestQuery = 'curated_fallback';
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Pexels curated fallback failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($bestPhoto) {
            $imageUrl = $bestPhoto['src']['large2x'] ?? $bestPhoto['src']['large'] ?? $bestPhoto['src']['original'];
            $path = $this->pexelsService->downloadAndSavePhoto(
                $imageUrl,
                $bestPhoto['photographer'] ?? 'Unknown',
                $bestPhoto['id']
            );

            if ($path) {
                AutoPostLog::logSuccess('featured_image_found', '🖼️ Featured image downloaded from Pexels', [
                    'schedule_id' => $schedule->id,
                    'topic_id' => $topic->id,
                    'context' => [
                        'query' => $bestQuery,
                        'photo_id' => $bestPhoto['id'],
                        'photographer' => $bestPhoto['photographer'] ?? 'Unknown',
                        'path' => $path,
                        'relevance_score' => $bestScore,
                    ],
                ]);

                return $path;
            }
        }

        AutoPostLog::logWarning('featured_image_not_found', '⚠️ No suitable featured image found', [
            'schedule_id' => $schedule->id,
            'topic_id' => $topic->id,
        ]);

        return null;
    }

    /**
     * Build prioritized search queries for Pexels from topic context
     */
    protected function buildImageSearchQueries(ArticleTopic $topic): array
    {
        $queries = [];

        $title = trim((string) $topic->title);
        $keywords = collect($topic->keywords ?? [])->filter()->map(fn ($k) => trim((string) $k))->values();

        // 1) Prioritize first two meaningful keywords
        if ($keywords->isNotEmpty()) {
            $queries[] = $keywords->take(2)->implode(' ');
            $queries[] = $keywords->take(3)->implode(' ');
        }

        // 2) Main title + Indonesia business context
        if ($title !== '') {
            $queries[] = $title;
            $queries[] = $title . ' Indonesia bisnis';
        }

        // 3) Simplified title without year/noise words
        $cleanTitle = preg_replace('/\b\d{4}\b/', '', $topic->title);
        $cleanTitle = preg_replace('/\b\d+\b/', '', $cleanTitle);
        $cleanTitle = preg_replace('/[^\w\s]/u', '', $cleanTitle);
        $stopWords = ['dan', 'yang', 'untuk', 'dengan', 'dalam', 'cara', 'panduan', 'lengkap'];
        $words = array_values(array_filter(explode(' ', Str::lower(trim($cleanTitle))), function ($w) use ($stopWords) {
            return strlen($w) > 2 && !in_array($w, $stopWords, true);
        }));
        if (count($words) > 4) {
            $words = array_slice($words, 0, 4);
        }
        if (!empty($words)) {
            $queries[] = implode(' ', $words);
        }

        // 4) Category-based fallback queries for better semantic match
        $categoryMap = [
            'tips' => ['konsultasi bisnis kantor', 'dokumen perizinan usaha indonesia'],
            'regulation' => ['regulasi pemerintah dokumen legal', 'izin usaha dokumen resmi'],
            'general' => ['bisnis indonesia kantor profesional', 'dokumen usaha indonesia'],
            'case-study' => ['tim bisnis meeting sukses', 'analisis bisnis indonesia'],
            'news' => ['berita bisnis indonesia', 'perkembangan usaha indonesia'],
        ];

        foreach (($categoryMap[$topic->category] ?? ['dokumen perizinan bisnis indonesia']) as $fallbackQuery) {
            $queries[] = $fallbackQuery;
        }

        // 5) Hard fallback
        $queries[] = 'perizinan usaha indonesia';
        $queries[] = 'business office document';

        return array_unique(array_filter($queries));
    }

    /**
     * Score candidate photos by text relevance and visual suitability.
     */
    protected function scorePhotoCandidate(array $photo, string $query, ArticleTopic $topic): int
    {
        $score = 0;

        $queryTokens = $this->extractSearchTokens($query);
        $topicTokens = $this->extractSearchTokens($topic->title . ' ' . implode(' ', $topic->keywords ?? []));
        $categoryHints = $this->extractSearchTokens(implode(' ', $this->getCategoryImageHints($topic->category)));

        $alt = Str::lower((string) ($photo['alt'] ?? ''));
        $url = Str::lower((string) ($photo['url'] ?? ''));
        $photographer = Str::lower((string) ($photo['photographer'] ?? ''));
        $haystack = $alt . ' ' . $url . ' ' . $photographer;

        foreach ($queryTokens as $token) {
            if (Str::contains($haystack, $token)) {
                $score += 8;
            }
        }

        foreach ($topicTokens as $token) {
            if (Str::contains($haystack, $token)) {
                $score += 4;
            }
        }

        foreach ($categoryHints as $hint) {
            if (Str::contains($haystack, $hint)) {
                $score += 3;
            }
        }

        // Prefer landscape-like ratio and larger image width when available.
        $width = (int) ($photo['width'] ?? 0);
        $height = (int) ($photo['height'] ?? 1);
        if ($width > 0 && $height > 0) {
            $ratio = $width / max(1, $height);
            if ($ratio >= 1.3 && $ratio <= 2.2) {
                $score += 6;
            }
            if ($width >= 1600) {
                $score += 4;
            }
        }

        return $score;
    }

    protected function extractSearchTokens(string $text): array
    {
        $normalized = Str::lower(preg_replace('/[^\pL\pN\s]/u', ' ', $text));
        $parts = preg_split('/\s+/', $normalized) ?: [];
        $stop = ['dan', 'yang', 'untuk', 'dengan', 'dalam', 'atau', 'the', 'for', 'from'];

        return array_values(array_unique(array_filter($parts, function ($token) use ($stop) {
            return mb_strlen($token) > 2 && !in_array($token, $stop, true);
        })));
    }

    protected function getCategoryImageHints(?string $category): array
    {
        $hints = [
            'tips' => ['business', 'office', 'document', 'consulting', 'planning'],
            'regulation' => ['legal', 'government', 'regulation', 'law', 'document'],
            'general' => ['business', 'office', 'meeting', 'document'],
            'case-study' => ['success', 'teamwork', 'strategy', 'meeting'],
            'news' => ['news', 'media', 'business', 'analysis'],
        ];

        return $hints[$category] ?? ['business', 'office', 'document'];
    }

    /**
     * Create article in database with proper duplicate handling
     */
    protected function createArticle(array $articleData, ArticleTopic $topic): Article
    {
        // Get the first admin user as author (or use a specific bot user)
        $author = \App\Models\User::where('role_id', 1)->first();
        
        if (!$author) {
            $author = \App\Models\User::first();
        }
        
        // Check if article with same title already exists (INCLUDING soft deleted)
        $existingArticle = Article::withTrashed()->where('title', $articleData['title'])->first();
        
        if ($existingArticle) {
            \Log::warning('⚠️ Article with same title already exists', [
                'existing_id' => $existingArticle->id,
                'title' => $articleData['title'],
                'is_deleted' => $existingArticle->trashed(),
            ]);
            
            // If soft deleted, restore it and update
            if ($existingArticle->trashed()) {
                $existingArticle->restore();
                $existingArticle->update([
                    'content' => $articleData['content'],
                    'excerpt' => $articleData['excerpt'],
                    'status' => $articleData['status'],
                    'published_at' => $articleData['published_at'],
                    'meta_title' => $articleData['meta_title'],
                    'meta_description' => $articleData['meta_description'],
                    'source_type' => 'auto-generated',
                ]);
                \Log::info('✅ Restored and updated soft-deleted article', [
                    'article_id' => $existingArticle->id,
                ]);
            } elseif ($existingArticle->status === 'draft') {
                // Update existing draft
                $existingArticle->update([
                    'content' => $articleData['content'],
                    'excerpt' => $articleData['excerpt'],
                    'status' => $articleData['status'],
                    'published_at' => $articleData['published_at'],
                    'source_type' => 'auto-generated',
                ]);
            }
            
            return $existingArticle;
        }
        
        // Generate unique slug BEFORE create to avoid constraint violation
        $slug = Article::generateUniqueSlug($articleData['title']);
        
        // Use database transaction to ensure atomicity
        return \DB::transaction(function () use ($articleData, $topic, $author, $slug) {
            return Article::create([
                'title' => $articleData['title'],
                'slug' => $slug, // Explicitly set unique slug
                'content' => $articleData['content'],
                'excerpt' => $articleData['excerpt'],
                'category' => $articleData['category'],
                'language' => $topic->language ?? 'id',
                'tags' => $articleData['tags'],
                'status' => $articleData['status'],
                'published_at' => $articleData['published_at'],
                'author_id' => $author->id,
                'source_type' => 'auto-generated',
                'topic_cluster_id' => $topic->topic_cluster_id,
                'meta_title' => $articleData['meta_title'],
                'meta_description' => $articleData['meta_description'],
                'meta_keywords' => $articleData['meta_keywords'],
                'reading_time' => $articleData['reading_time'],
                'featured_image' => $articleData['featured_image'] ?? null,
                'is_featured' => false,
            ]);
        });
    }

    /**
     * SEO Compliance: Score the article and auto-fix if below target
     * This saves OpenRouter API costs by fixing issues immediately 
     * instead of requiring separate SeoFixService runs later.
     */
    protected function applySeoCompliance(Article $article, AutoPostSchedule $schedule): array
    {
        $targetScore = 80;

        try {
            // Score the article with our 10-factor SEO scoring algorithm
            $initialScore = $this->seoScorer->scoreArticle($article);

            \Log::info('📊 SEO Score check', [
                'article_id' => $article->id,
                'score' => $initialScore->total_score,
                'grade' => $initialScore->grade,
                'issues' => count($initialScore->recommendations ?? []),
            ]);

            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'level' => 'info',
                'event' => 'seo_score_initial',
                'message' => "📊 SEO Score: {$initialScore->total_score}/100 (Grade {$initialScore->grade})",
                'context' => [
                    'score' => $initialScore->total_score,
                    'grade' => $initialScore->grade,
                    'recommendations' => count($initialScore->recommendations ?? []),
                    'factors' => $initialScore->factors,
                ],
            ]);

            // If score is already good, no fix needed — API cost saved!
            if ($initialScore->total_score >= $targetScore) {
                \Log::info('✅ SEO Score already meets target, no fix needed', [
                    'score' => $initialScore->total_score,
                ]);
                return [
                    'initial_score' => $initialScore->total_score,
                    'final_score' => $initialScore->total_score,
                    'grade' => $initialScore->grade,
                    'fixes_count' => 0,
                    'api_saved' => true,
                ];
            }

            // Score below target: apply rule-based fixes only (no extra API call)
            // This uses the same logic as SeoFixService but skips the AI API calls
            $fixResult = $this->applyRuleBasedSeoFixes($article, $initialScore);

            // Re-score after fixes
            $article->refresh();
            $finalScore = $this->seoScorer->scoreArticle($article);

            $scoreChange = $finalScore->total_score - $initialScore->total_score;

            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'level' => $finalScore->total_score >= $targetScore ? 'success' : 'warning',
                'event' => 'seo_compliance_result',
                'message' => "🔧 SEO Fix: {$initialScore->total_score} → {$finalScore->total_score} (+" . max(0, $scoreChange) . ") Grade {$finalScore->grade}",
                'context' => [
                    'initial_score' => $initialScore->total_score,
                    'final_score' => $finalScore->total_score,
                    'score_change' => $scoreChange,
                    'grade' => $finalScore->grade,
                    'fixes' => $fixResult['fixes'],
                    'remaining_issues' => count($finalScore->recommendations ?? []),
                ],
            ]);

            \Log::info('🔧 SEO Compliance applied', [
                'article_id' => $article->id,
                'score' => $initialScore->total_score . ' → ' . $finalScore->total_score,
                'fixes' => count($fixResult['fixes']),
            ]);

            return [
                'initial_score' => $initialScore->total_score,
                'final_score' => $finalScore->total_score,
                'grade' => $finalScore->grade,
                'fixes_count' => count($fixResult['fixes']),
                'fixes' => $fixResult['fixes'],
                'api_saved' => true,
            ];

        } catch (\Exception $e) {
            \Log::warning('⚠️ SEO compliance check failed (non-fatal)', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'initial_score' => 0,
                'final_score' => 0,
                'grade' => '?',
                'fixes_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Apply rule-based SEO fixes (no extra AI API calls) to save costs.
     * Fixes: meta_title (year, Bizmark, length), meta_description (CTA), 
     * tags, excerpt, reading_time, slug — all deterministic.
     */
    protected function applyRuleBasedSeoFixes(Article $article, $seoScore): array
    {
        $fixes = [];
        $changed = false;
        $factors = $seoScore->factors ?? [];

        // Fix meta_title: ensure year + Bizmark + ≤55 chars
        $titleScore = $factors['title']['score'] ?? 0;
        if ($titleScore < 15 && $article->meta_title) {
            $metaTitle = $article->meta_title;
            $year = date('Y');

            // Add year if missing
            if (!preg_match('/20\d{2}/', $metaTitle)) {
                if (mb_strlen($metaTitle) + strlen(" $year") <= 55) {
                    $metaTitle .= " $year";
                } elseif (mb_strlen($metaTitle) > 45) {
                    $metaTitle = Str::limit($metaTitle, 45, '') . " $year";
                }
            }

            // Add Bizmark if missing
            if (!Str::contains($metaTitle, ['Bizmark', 'bizmark'], true)) {
                if (mb_strlen($metaTitle) + 10 <= 55) {
                    $metaTitle .= ' | Bizmark';
                } elseif (mb_strlen($metaTitle) > 40) {
                    $metaTitle = Str::limit($metaTitle, 40, '') . ' | Bizmark';
                }
            }

            if ($metaTitle !== $article->meta_title) {
                $article->meta_title = $metaTitle;
                $fixes[] = '⚙️ Meta title optimized for SEO';
                $changed = true;
            }
        }

        // Ensure meta_title differs from title (for +2 bonus)
        if ($article->meta_title === $article->title && $article->meta_title) {
            $year = date('Y');
            $suffix = " $year | Bizmark";
            $base = preg_replace('/\s*20\d{2}\s*/', ' ', $article->title);
            $base = preg_replace('/\s*\|?\s*[Bb]izmark\s*/', '', $base);
            $base = trim(Str::limit(trim($base), 55 - mb_strlen($suffix), ''));
            $article->meta_title = $base . $suffix;
            $fixes[] = '⚙️ Meta title differentiated from title';
            $changed = true;
        }

        // Fix meta_description: ensure CTA
        $descScore = $factors['meta_description']['score'] ?? 0;
        if ($descScore < 12 && $article->meta_description) {
            $desc = $article->meta_description;
            $hasCta = preg_match('/hubungi|konsultasi|pelajari|baca|dapatkan|gratis/i', $desc);
            if (!$hasCta) {
                $cta = ' Konsultasi gratis di Bizmark!';
                if (mb_strlen($desc) + mb_strlen($cta) <= 160) {
                    $article->meta_description = trim($desc) . $cta;
                } else {
                    $article->meta_description = Str::limit($desc, 160 - mb_strlen($cta), '') . $cta;
                }
                $fixes[] = '⚙️ CTA added to meta description';
                $changed = true;
            }
        }

        // Ensure meta_keywords has Bizmark
        if ($article->meta_keywords && !Str::contains($article->meta_keywords, 'Bizmark', true)) {
            $article->meta_keywords .= ', Bizmark';
            $fixes[] = '⚙️ Bizmark added to meta keywords';
            $changed = true;
        }

        // Fix tags: ensure ≥2
        $tags = $article->tags ?? [];
        if (count($tags) < 2) {
            $fillers = [$article->category, 'Perizinan', 'Bizmark'];
            foreach ($fillers as $filler) {
                if ($filler && count($tags) < 3 && !in_array($filler, $tags)) {
                    $tags[] = $filler;
                }
            }
            $article->tags = array_values(array_unique($tags));
            $fixes[] = '⚙️ Tags added for SEO';
            $changed = true;
        }

        // Fix reading_time if missing
        if (!$article->reading_time && $article->content) {
            $article->reading_time = max(1, ceil(str_word_count(strip_tags($article->content)) / 200));
            $fixes[] = '⚙️ Reading time calculated';
            $changed = true;
        }

        // Fix excerpt if too short
        if (!$article->excerpt || mb_strlen($article->excerpt) < 50) {
            $text = strip_tags($article->content ?? '');
            $article->excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', $text)), 180, '') . '. Selengkapnya di Bizmark.';
            $fixes[] = '⚙️ Excerpt generated for SEO';
            $changed = true;
        }

        if ($changed) {
            $article->updated_at = now();
            $article->saveQuietly();
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }

    /**
     * Ensure content has "Baca juga" / "Artikel Terkait" section for SEO scoring bonus (+3)
     */
    protected function ensureBacaJugaSection(string $content, ArticleTopic $topic): string
    {
        // Check if already has the section (from InternalLinkService or prior processing)
        if (Str::contains($content, ['Baca juga', 'Baca Juga', 'Artikel Terkait', 'artikel terkait'])) {
            return $content;
        }

        $baseUrl = rtrim(config('app.url'), '/');

        // Find related published articles
        $relatedArticles = Article::where('status', 'published')
            ->select('id', 'title', 'slug');

        if ($topic->category) {
            $related = (clone $relatedArticles)
                ->where('category', $topic->category)
                ->inRandomOrder()
                ->limit(5)
                ->get();
        } else {
            $related = collect();
        }

        if ($related->isEmpty()) {
            $related = $relatedArticles->inRandomOrder()->limit(5)->get();
        }

        if ($related->isEmpty()) {
            return $content;
        }

        // Build "Artikel Terkait" section in HTML (auto-post always generates HTML)
        $section = '<hr><h2>Artikel Terkait</h2>';
        $section .= '<p>Baca juga artikel lainnya di Bizmark:</p><ul>';
        foreach ($related as $a) {
            $url = htmlspecialchars("{$baseUrl}/blog/{$a->slug}", ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($a->title, ENT_QUOTES, 'UTF-8');
            $section .= "<li><a href=\"{$url}\">{$title}</a></li>";
        }
        $section .= '</ul>';

        return $content . "\n\n" . $section;
    }

    /**
     * Schedule next batch of posts for a given date
     */
    public function scheduleNextBatch(Carbon $date): array
    {
        \Log::info('📅 Scheduling next batch of posts', [
            'date' => $date->toDateString(),
        ]);

        $config = AutoPostConfig::current();
        
        if (!$config->is_enabled) {
            \Log::warning('⚠️  Auto-posting is disabled, skipping scheduling');
            return [];
        }
        
        $schedules = [];
        $postTimes = $config->getPostTimesForDate($date);
        
        foreach ($postTimes as $time) {
            // Check if already scheduled
            $existing = AutoPostSchedule::where('scheduled_at', $time)->first();
            
            if ($existing) {
                \Log::debug('⏭️  Schedule already exists for time', [
                    'time' => $time->toDateTimeString(),
                ]);
                continue;
            }
            
            // Get next available topic
            $topic = $this->getNextTopic();
            
            if (!$topic) {
                \Log::warning('⚠️  No available topics for scheduling', [
                    'time' => $time->toDateTimeString(),
                ]);
                continue;
            }
            
            // Create schedule
            $schedule = AutoPostSchedule::create([
                'topic_id' => $topic->id,
                'scheduled_at' => $time,
                'status' => 'pending',
            ]);
            
            // Update topic scheduled_for
            $topic->update(['scheduled_for' => $time]);
            
            $schedules[] = $schedule;
            
            \Log::info('✅ Scheduled post', [
                'schedule_id' => $schedule->id,
                'topic' => $topic->title,
                'time' => $time->toDateTimeString(),
            ]);
            
            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
                'level' => 'info',
                'event' => 'post_scheduled',
                'message' => 'Post scheduled for publication',
                'context' => [
                    'scheduled_at' => $time->toDateTimeString(),
                ],
            ]);
        }
        
        \Log::info('📊 Batch scheduling complete', [
            'date' => $date->toDateString(),
            'scheduled_count' => count($schedules),
        ]);
        
        return $schedules;
    }

    /**
     * Schedule a rolling batch starting from a date.
     * If slots are full on a day, remaining posts are moved to the next day.
     */
    public function scheduleRollingBatch(Carbon $startDate, int $targetCount, int $maxOverflowDays = 7): array
    {
        $config = AutoPostConfig::current();

        if (!$config->is_enabled || $targetCount < 1) {
            return [
                'requested' => $targetCount,
                'scheduled_count' => 0,
                'remaining' => max(0, $targetCount),
                'scheduled' => [],
                'days_used' => 0,
            ];
        }

        $postTimes = collect($config->post_times ?? [])->filter()->unique()->sort()->values();
        if ($postTimes->isEmpty()) {
            \Log::warning('⚠️ No post times configured for rolling batch scheduling');
            return [
                'requested' => $targetCount,
                'scheduled_count' => 0,
                'remaining' => $targetCount,
                'scheduled' => [],
                'days_used' => 0,
            ];
        }

        $remaining = $targetCount;
        $scheduled = [];
        $cursor = $startDate->copy()->startOfDay();
        $daysUsed = 0;

        for ($dayOffset = 0; $dayOffset <= $maxOverflowDays && $remaining > 0; $dayOffset++) {
            $dateScheduledCount = 0;

            foreach ($postTimes as $time) {
                if ($remaining <= 0) {
                    break;
                }

                $slot = Carbon::parse($cursor->format('Y-m-d') . ' ' . $time, $config->timezone);

                $exists = AutoPostSchedule::where('scheduled_at', $slot)->exists();
                if ($exists) {
                    continue;
                }

                $topic = $this->getNextTopic();
                if (!$topic) {
                    \Log::warning('⚠️ No available topic for rolling slot', [
                        'slot' => $slot->toDateTimeString(),
                        'remaining' => $remaining,
                    ]);
                    break 2;
                }

                $schedule = AutoPostSchedule::create([
                    'topic_id' => $topic->id,
                    'scheduled_at' => $slot,
                    'status' => 'pending',
                ]);

                $topic->update(['scheduled_for' => $slot]);

                AutoPostLog::create([
                    'schedule_id' => $schedule->id,
                    'topic_id' => $topic->id,
                    'level' => 'info',
                    'event' => 'post_scheduled',
                    'message' => 'Post scheduled via rolling batch scheduler',
                    'context' => [
                        'scheduled_at' => $slot->toDateTimeString(),
                        'overflow_day_offset' => $dayOffset,
                    ],
                ]);

                $scheduled[] = $schedule;
                $remaining--;
                $dateScheduledCount++;
            }

            if ($dateScheduledCount > 0) {
                $daysUsed++;
            }

            $cursor->addDay();
        }

        return [
            'requested' => $targetCount,
            'scheduled_count' => count($scheduled),
            'remaining' => $remaining,
            'scheduled' => $scheduled,
            'days_used' => $daysUsed,
        ];
    }

    /**
     * Get next available topic (with duplicate prevention and category weighting)
     */
    public function getNextTopic(): ?ArticleTopic
    {
        $config = AutoPostConfig::current();
        
        // Get target language and market
        $targetLanguage = $config->getNextLanguage();
        $targetMarket = $config->getTargetMarket($targetLanguage);
        
        // Get target category based on weights
        $targetCategory = $config->getNextCategory();
        
        // Get available topics in target language, market, and category
        $topics = ArticleTopic::available()
            ->byLanguage($targetLanguage)
            ->byMarket($targetMarket)
            ->byCategory($targetCategory)
            ->highPriority()
            ->get();
        
        // If no topics in target category, get from any category
        if ($topics->isEmpty()) {
            $topics = ArticleTopic::available()
                ->byLanguage($targetLanguage)
                ->byMarket($targetMarket)
                ->highPriority()
                ->get();
        }
        
        // If still empty, try other language
        if ($topics->isEmpty()) {
            $otherLanguage = $targetLanguage === 'id' ? 'en' : 'id';
            $topics = ArticleTopic::available()
                ->byLanguage($otherLanguage)
                ->highPriority()
                ->get();
        }
        
        if ($topics->isEmpty()) {
            return null;
        }
        
        // Filter out duplicates
        foreach ($topics as $topic) {
            $isDuplicate = $this->similarityService->isDuplicate($topic, $config->duplicate_threshold);
            
            if (!$isDuplicate) {
                return $topic;
            }
        }
        
        // If all topics are duplicates, return the highest priority one
        return $topics->first();
    }

    /**
     * Get statistics for auto-posting
     */
    public function getStats(int $days = 30): array
    {
        $from = now()->subDays($days);
        
        $totalScheduled = AutoPostSchedule::where('created_at', '>=', $from)->count();
        $completed = AutoPostSchedule::where('created_at', '>=', $from)
            ->where('status', 'completed')
            ->count();
        $failed = AutoPostSchedule::where('created_at', '>=', $from)
            ->where('status', 'failed')
            ->count();
        $pending = AutoPostSchedule::where('created_at', '>=', $from)
            ->where('status', 'pending')
            ->count();
        
        $successRate = $totalScheduled > 0 ? round(($completed / $totalScheduled) * 100, 1) : 0;
        
        $avgGenerationTime = AutoPostSchedule::where('created_at', '>=', $from)
            ->where('status', 'completed')
            ->whereNotNull('generation_time_seconds')
            ->avg('generation_time_seconds');
        
        $totalCost = AutoPostLog::where('created_at', '>=', $from)
            ->whereNotNull('ai_cost')
            ->sum('ai_cost');
        
        $topicsByCategory = ArticleTopic::where('status', 'published')
            ->where('published_at', '>=', $from)
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();
        
        return [
            'total_scheduled' => $totalScheduled,
            'completed' => $completed,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $successRate,
            'avg_generation_time' => round($avgGenerationTime, 1),
            'total_cost' => round($totalCost, 2),
            'articles_by_category' => $topicsByCategory,
        ];
    }

    /**
     * Check system health
     */
    public function checkHealth(): array
    {
        $config = AutoPostConfig::current();
        
        $availableTopics = ArticleTopic::available()->count();
        $pendingSchedules = AutoPostSchedule::pending()->count();
        $failedSchedules = AutoPostSchedule::failed()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        $recentErrors = AutoPostLog::errors()
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        
        $health = [
            'status' => 'healthy',
            'enabled' => $config->is_enabled,
            'available_topics' => $availableTopics,
            'pending_schedules' => $pendingSchedules,
            'recent_failures' => $failedSchedules,
            'recent_errors' => $recentErrors,
            'warnings' => [],
        ];
        
        // Check for issues
        if (!$config->is_enabled) {
            $health['status'] = 'disabled';
            $health['warnings'][] = 'Auto-posting is disabled';
        }
        
        if ($availableTopics < 10) {
            $health['warnings'][] = "Low topic pool: only {$availableTopics} topics available";
        }
        
        if ($failedSchedules > 5) {
            $health['status'] = 'degraded';
            $health['warnings'][] = "{$failedSchedules} failed schedules in last 7 days";
        }
        
        if ($recentErrors > 10) {
            $health['status'] = 'unhealthy';
            $health['warnings'][] = "{$recentErrors} errors in last 24 hours";
        }
        
        return $health;
    }
}
