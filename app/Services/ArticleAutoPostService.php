<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Models\AutoPostSchedule;
use App\Models\AutoPostLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ArticleAutoPostService
{
    protected ArticleGenerationService $generationService;
    protected InternalLinkService $linkService;
    protected TopicSimilarityService $similarityService;
    protected ArticleQualityService $qualityService;

    public function __construct(
        ArticleGenerationService $generationService,
        InternalLinkService $linkService,
        TopicSimilarityService $similarityService,
        ArticleQualityService $qualityService
    ) {
        $this->generationService = $generationService;
        $this->linkService = $linkService;
        $this->similarityService = $similarityService;
        $this->qualityService = $qualityService;
    }

    /**
     * Main workflow for generating and posting article
     */
    public function executeScheduledPost(AutoPostSchedule $schedule): Article
    {
        $startTime = microtime(true);
        
        // Mark as processing
        $schedule->update([
            'status' => 'processing',
            'started_at' => now(),
            'attempts' => $schedule->attempts + 1,
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
            
            // 7. Add internal links
            AutoPostLog::logInfo('internal_links_processing', 'Adding internal links', [
                'topic_id' => $topic->id,
                'context' => ['target_count' => $config->internal_links_count],
            ]);
            
            $articleData['content'] = $this->linkService->addInternalLinks(
                $articleData['content'],
                $topic,
                $config->internal_links_count
            );
            
            $linkStats = $this->linkService->validateLinks($articleData['content']);
            
            AutoPostLog::logSuccess('internal_links_added', 'Internal links added successfully', [
                'topic_id' => $topic->id,
                'internal_links' => $linkStats['internal_links'],
            ]);
            
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
                'message' => '🚀 Article auto-posted successfully: ' . $article->title,
                'context' => [
                    'article_id' => $article->id,
                    'url' => url('/blog/' . $article->slug),
                    'quality_score' => $qualityCheck['quality_score'],
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
            
            AutoPostLog::logError('article_generation_failed', $e->getMessage(), [
                'schedule_id' => $schedule->id,
                'topic_id' => $schedule->topic_id,
            ]);
            
            // Update schedule status
            $shouldRetry = $schedule->attempts < ($config->retry_attempts ?? 3);
            
            $schedule->update([
                'status' => $shouldRetry ? 'pending' : 'failed',
                'error_message' => substr($e->getMessage(), 0, 500),
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
                'meta_title' => $articleData['meta_title'],
                'meta_description' => $articleData['meta_description'],
                'meta_keywords' => $articleData['meta_keywords'],
                'reading_time' => $articleData['reading_time'],
                'is_featured' => false,
            ]);
        });
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
