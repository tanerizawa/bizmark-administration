<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Models\AutoPostSchedule;
use App\Models\AutoPostLog;
use App\Jobs\GenerateAutoPostArticle;
use App\Services\AutoPost\ArticleAutoPostArticleCreator;
use App\Services\AutoPost\ArticleAutoPostContentHelper;
use App\Services\AutoPost\ArticleAutoPostImageHelper;
use App\Services\AutoPost\ArticleAutoPostSeoHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        ArticleQualityService $qualityService,
        protected ArticleAutoPostImageHelper $imageHelper,
        protected ArticleAutoPostArticleCreator $articleCreator,
        protected ArticleAutoPostSeoHelper $seoHelper,
        protected ArticleAutoPostContentHelper $contentHelper,
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
            $articleData['content'] = $this->contentHelper->ensureBacaJugaSection($articleData['content'], $topic);
            
            $linkStats = $this->linkService->validateLinks($articleData['content']);
            
            AutoPostLog::logSuccess('internal_links_added', 'Internal links added successfully', [
                'topic_id' => $topic->id,
                'internal_links' => $linkStats['internal_links'],
            ]);
            
            // 7.5. Fetch featured image from Pexels
            if ($config->include_featured_image) {
                $articleData['featured_image'] = $this->imageHelper->fetchFeaturedImage($topic, $schedule);
            }
            
            // 8. Create article
            $article = $this->articleCreator->createArticle($articleData, $topic);
            
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
            $seoResult = $this->seoHelper->applySeoCompliance($article, $schedule);
            
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
