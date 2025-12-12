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
        
        \Log::info('🚀 Starting scheduled article generation', [
            'schedule_id' => $schedule->id,
            'topic_id' => $schedule->topic_id,
        ]);

        AutoPostLog::logInfo('generation_started', 'Starting article generation', [
            'schedule_id' => $schedule->id,
            'topic_id' => $schedule->topic_id,
        ]);

        try {
            // 1. Load configuration
            $config = AutoPostConfig::current();
            
            if (!$config->is_enabled) {
                throw new \Exception('Auto-posting is disabled in configuration');
            }
            
            // 2. Load topic
            $topic = $schedule->topic;
            
            if (!$topic) {
                throw new \Exception("Topic not found for schedule #{$schedule->id}");
            }
            
            // 3. Check for duplicates
            if ($this->similarityService->isDuplicate($topic, $config->duplicate_threshold)) {
                throw new \Exception("Topic is too similar to recent articles (duplicate detected)");
            }
            
            // 4. Mark topic as processing
            $topic->markAsProcessing();
            
            // 5. Generate content via AI
            AutoPostLog::logInfo('ai_generation_started', 'Generating article content with AI', [
                'topic_id' => $topic->id,
                'context' => ['model' => $config->ai_model],
            ]);
            
            $articleData = $this->generationService->generateArticle($topic, $config);
            
            AutoPostLog::logSuccess('ai_generation_completed', 'AI content generation completed', [
                'topic_id' => $topic->id,
                'word_count' => str_word_count(strip_tags($articleData['content'])),
            ]);
            
            // 6. Validate quality
            $qualityCheck = $this->qualityService->validateQuality($articleData, $config);
            
            if (!$qualityCheck['valid']) {
                AutoPostLog::logWarning('quality_check_failed', 'Article quality below standards', [
                    'topic_id' => $topic->id,
                    'context' => [
                        'issues' => $qualityCheck['issues'],
                        'quality_score' => $qualityCheck['quality_score'],
                    ],
                ]);
                
                // Proceed with warnings but log them
                if ($qualityCheck['quality_score'] < 50) {
                    throw new \Exception('Article quality too low: ' . implode(', ', $qualityCheck['issues']));
                }
            }
            
            AutoPostLog::logSuccess('quality_check_passed', 'Article quality validation passed', [
                'topic_id' => $topic->id,
                'context' => [
                    'quality_score' => $qualityCheck['quality_score'],
                    'warnings' => count($qualityCheck['warnings']),
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
            
            AutoPostLog::logSuccess('article_created', 'Article created in database', [
                'article_id' => $article->id,
                'topic_id' => $topic->id,
                'context' => ['status' => $article->status],
            ]);
            
            // 9. Mark topic as published
            $topic->markAsPublished($article->id);
            
            // 10. Calculate generation time and cost
            $generationTime = round((microtime(true) - $startTime), 2);
            $estimatedCost = $this->generationService->estimateCost($config);
            
            // 11. Final success log
            AutoPostLog::logSuccess('article_published', 'Article auto-posted successfully', [
                'article_id' => $article->id,
                'topic_id' => $topic->id,
                'schedule_id' => $schedule->id,
                'word_count' => $qualityCheck['metrics']['word_count'],
                'reading_time' => $article->reading_time,
                'internal_links' => $linkStats['internal_links'],
                'quality_score' => $qualityCheck['quality_score'],
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
            AutoPostLog::logError('article_generation_failed', $e->getMessage(), [
                'schedule_id' => $schedule->id,
                'topic_id' => $schedule->topic_id,
            ]);
            
            // Mark topic as failed
            if (isset($topic)) {
                $topic->markAsFailed();
            }
            
            \Log::error('❌ Article generation failed', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Create article in database
     */
    protected function createArticle(array $articleData, ArticleTopic $topic): Article
    {
        // Get the first admin user as author (or use a specific bot user)
        $author = \App\Models\User::where('role_id', 1)->first();
        
        if (!$author) {
            $author = \App\Models\User::first();
        }
        
        return Article::create([
            'title' => $articleData['title'],
            'content' => $articleData['content'],
            'excerpt' => $articleData['excerpt'],
            'category' => $articleData['category'],
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
            
            AutoPostLog::logInfo('post_scheduled', 'Post scheduled for publication', [
                'schedule_id' => $schedule->id,
                'topic_id' => $topic->id,
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
        
        // Get target category based on weights
        $targetCategory = $config->getNextCategory();
        
        // Get available topics in target category
        $topics = ArticleTopic::available()
            ->byCategory($targetCategory)
            ->highPriority()
            ->get();
        
        // If no topics in target category, get from any category
        if ($topics->isEmpty()) {
            $topics = ArticleTopic::available()
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
