<?php

namespace App\Console\Commands;

use App\Services\CompetitiveIntelligenceService;
use App\Services\ContentGapService;
use App\Services\ContentRefreshService;
use App\Services\KeywordResearchService;
use App\Services\SmartMetaOptimizerService;
use App\Services\TopicClusterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoOrchestratorCommand extends Command
{
    protected $signature = 'seo:orchestrate
        {--phase=all : Which phase to run (all, intelligence, content, optimize, distribute)}
        {--queue-gaps=20 : Auto-queue top N gaps as article topics}
        {--convert-clusters : Convert cluster subtopics to article topics}
        {--cluster-limit=0 : Limit topics per cluster (0=all)}
        {--refresh-days=90 : Refresh articles older than N days}
        {--refresh-limit=5 : Max articles to refresh}
        {--meta-limit=10 : Articles to optimize meta}
        {--competitor-limit=20 : Keywords to analyze}
        {--language=id : Language (id/en)}';

    protected $description = 'SEO Domination Engine v2: Full orchestration of all SEO services';

    public function handle(
        KeywordResearchService $keywordService,
        TopicClusterService $clusterService,
        ContentGapService $gapService,
        SmartMetaOptimizerService $metaOptimizer,
        CompetitiveIntelligenceService $competitorService,
        ContentRefreshService $refreshService
    ): int {
        $phase = $this->option('phase');
        $language = $this->option('language');
        $startTime = microtime(true);

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║        SEO DOMINATION ENGINE v2 — ORCHESTRATOR      ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->info('');

        $summary = [];

        try {
            // ── PHASE 1: INTELLIGENCE ──
            if (in_array($phase, ['all', 'intelligence'])) {
                $summary['intelligence'] = $this->runIntelligence(
                    $keywordService, $clusterService, $gapService, $language
                );
            }

            // ── PHASE 2: CONTENT PIPELINE ──
            if (in_array($phase, ['all', 'content'])) {
                $summary['content'] = $this->runContentPipeline(
                    $clusterService, $gapService
                );
            }

            // ── PHASE 3: OPTIMIZATION ──
            if (in_array($phase, ['all', 'optimize'])) {
                $summary['optimize'] = $this->runOptimization(
                    $metaOptimizer, $refreshService, $language
                );
            }

            // ── PHASE 4: DISTRIBUTION ──
            if (in_array($phase, ['all', 'distribute'])) {
                $summary['distribute'] = $this->runDistribution();
            }

        } catch (\Throwable $e) {
            $this->error("Orchestrator error: {$e->getMessage()}");
            Log::error('SEO Orchestrator failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // ── SUMMARY ──
        $elapsed = round(microtime(true) - $startTime, 1);
        $this->info('');
        $this->info('═══ ORCHESTRATION SUMMARY ═══');
        $this->renderSummary($summary, $elapsed);

        Log::info('SEO Orchestrator completed', [
            'phase' => $phase,
            'elapsed_seconds' => $elapsed,
            'summary' => $summary,
        ]);

        return 0;
    }

    /**
     * Phase 1: Intelligence — keyword research, topic clusters, content gaps
     */
    protected function runIntelligence(
        KeywordResearchService $keywordService,
        TopicClusterService $clusterService,
        ContentGapService $gapService,
        string $language
    ): array {
        $result = ['keyword_clusters' => 0, 'topic_clusters' => 0, 'content_gaps' => 0];

        // Step 1: Keyword Research
        $this->info('━━━ Phase 1.1: AI Keyword Research ━━━');
        try {
            $keywords = $keywordService->researchAllServices($language);
            $result['keyword_clusters'] = count($keywords);
            $this->info("  ✓ Generated {$result['keyword_clusters']} keyword clusters");

            foreach ($keywords as $kw) {
                $this->line("    · {$kw['service']}: {$kw['keywords_count']} keywords, {$kw['long_tail_count']} long-tail");
            }
        } catch (\Throwable $e) {
            $this->warn("  ✗ Keyword research failed: {$e->getMessage()}");
            Log::error('Orchestrator: Keyword research failed', ['error' => $e->getMessage()]);
        }
        $this->newLine();

        // Step 2: Topic Clusters
        $this->info('━━━ Phase 1.2: Topic Cluster Generation ━━━');
        try {
            $clusters = $clusterService->generateClusters($language);
            $result['topic_clusters'] = count($clusters);
            $this->info("  ✓ Generated {$result['topic_clusters']} topic clusters");

            foreach ($clusters as $c) {
                $this->line("    · {$c['service']}: {$c['subtopics']} subtopics");
            }
        } catch (\Throwable $e) {
            $this->warn("  ✗ Topic clusters failed: {$e->getMessage()}");
            Log::error('Orchestrator: Topic clusters failed', ['error' => $e->getMessage()]);
        }
        $this->newLine();

        // Step 3: Content Gap Analysis
        $this->info('━━━ Phase 1.3: Content Gap Analysis ━━━');
        try {
            $allGaps = $gapService->analyzeAll($language);
            $totalGaps = 0;
            foreach ($allGaps as $name => $gaps) {
                $count = count($gaps);
                $totalGaps += $count;
                $this->line("    · {$name}: {$count} gaps");
            }
            $result['content_gaps'] = $totalGaps;
            $this->info("  ✓ Found {$totalGaps} content gaps");
        } catch (\Throwable $e) {
            $this->warn("  ✗ Content gap analysis failed: {$e->getMessage()}");
            Log::error('Orchestrator: Content gap analysis failed', ['error' => $e->getMessage()]);
        }
        $this->newLine();

        return $result;
    }

    /**
     * Phase 2: Content Pipeline — convert clusters to topics, queue gaps
     */
    protected function runContentPipeline(
        TopicClusterService $clusterService,
        ContentGapService $gapService
    ): array {
        $result = ['topics_from_clusters' => 0, 'topics_from_gaps' => 0];

        // Step 1: Convert cluster subtopics to ArticleTopics
        if ($this->option('convert-clusters')) {
            $this->info('━━━ Phase 2.1: Convert Clusters → Article Topics ━━━');
            try {
                $limit = (int) $this->option('cluster-limit');
                $converted = $clusterService->convertAllClustersToTopics($limit);
                $total = 0;
                foreach ($converted as $slug => $data) {
                    $total += $data['topics_created'];
                    $this->line("    · {$slug}: {$data['topics_created']} new topics");
                }
                $result['topics_from_clusters'] = $total;
                $this->info("  ✓ Created {$total} article topics from cluster subtopics");
            } catch (\Throwable $e) {
                $this->warn("  ✗ Cluster conversion failed: {$e->getMessage()}");
            }
            $this->newLine();
        }

        // Step 2: Queue top gaps
        $queueCount = (int) $this->option('queue-gaps');
        if ($queueCount > 0) {
            $this->info("━━━ Phase 2.2: Queue Top {$queueCount} Gaps → Article Topics ━━━");
            try {
                $queued = $gapService->queueTopGaps($queueCount);
                $result['topics_from_gaps'] = count($queued);
                $this->info("  ✓ Queued {$result['topics_from_gaps']} gaps as article topics");

                foreach ($queued as $q) {
                    $this->line("    · {$q['title']}");
                }
            } catch (\Throwable $e) {
                $this->warn("  ✗ Gap queuing failed: {$e->getMessage()}");
            }
            $this->newLine();
        }

        return $result;
    }

    /**
     * Phase 3: Optimization — meta optimization, content refresh, scoring
     */
    protected function runOptimization(
        SmartMetaOptimizerService $metaOptimizer,
        ContentRefreshService $refreshService,
        string $language
    ): array {
        $result = ['meta_optimized' => 0, 'articles_refreshed' => 0, 'articles_scored' => 0];

        // Step 1: Meta Optimization
        $metaLimit = (int) $this->option('meta-limit');
        if ($metaLimit > 0) {
            $this->info("━━━ Phase 3.1: Meta Optimization ({$metaLimit} articles) ━━━");
            try {
                $results = $metaOptimizer->optimizeBatch($metaLimit, $language);
                $result['meta_optimized'] = collect($results)->where('status', 'optimized')->count();
                $this->info("  ✓ Optimized {$result['meta_optimized']} articles");
            } catch (\Throwable $e) {
                $this->warn("  ✗ Meta optimization failed: {$e->getMessage()}");
            }
            $this->newLine();
        }

        // Step 2: Content Refresh
        $refreshDays = (int) $this->option('refresh-days');
        $refreshLimit = (int) $this->option('refresh-limit');
        if ($refreshLimit > 0) {
            $this->info("━━━ Phase 3.2: Content Refresh (articles > {$refreshDays} days) ━━━");
            try {
                $stale = $refreshService->getStaleArticles($refreshDays, $refreshLimit);
                foreach ($stale as $article) {
                    $refreshService->refreshArticle($article, 'seo:orchestrate');
                    $result['articles_refreshed']++;
                    $this->line("    · Refreshed: {$article->title}");
                }
                $this->info("  ✓ Refreshed {$result['articles_refreshed']} articles");
            } catch (\Throwable $e) {
                $this->warn("  ✗ Content refresh failed: {$e->getMessage()}");
            }
            $this->newLine();
        }

        // Step 3: SEO Scoring
        $this->info('━━━ Phase 3.3: SEO Score Batch ━━━');
        try {
            $this->call('seo:score-articles', ['--limit' => 20]);
            $result['articles_scored'] = 20;
        } catch (\Throwable $e) {
            $this->warn("  ✗ Scoring failed: {$e->getMessage()}");
        }
        $this->newLine();

        return $result;
    }

    /**
     * Phase 4: Distribution — sitemap, indexnow, syndication
     */
    protected function runDistribution(): array
    {
        $result = ['sitemap' => false, 'indexnow' => false, 'syndication' => 0];

        // Step 1: Regenerate Sitemap
        $this->info('━━━ Phase 4.1: Sitemap Regeneration ━━━');
        try {
            $this->call('sitemap:generate', ['--ping' => true]);
            $result['sitemap'] = true;
            $this->info('  ✓ Sitemap regenerated & pinged');
        } catch (\Throwable $e) {
            $this->warn("  ✗ Sitemap generation failed: {$e->getMessage()}");
        }
        $this->newLine();

        // Step 2: IndexNow — submit recent articles
        $this->info('━━━ Phase 4.2: IndexNow Submission ━━━');
        try {
            $this->call('seo:index-now', ['--recent' => 7]);
            $result['indexnow'] = true;
            $this->info('  ✓ Recent URLs submitted to IndexNow');
        } catch (\Throwable $e) {
            $this->warn("  ✗ IndexNow failed: {$e->getMessage()}");
        }
        $this->newLine();

        // Step 3: Content Syndication
        $this->info('━━━ Phase 4.3: Content Syndication ━━━');
        try {
            $this->call('content:syndicate', ['--limit' => 3]);
            $result['syndication'] = 3;
            $this->info('  ✓ Content syndication run');
        } catch (\Throwable $e) {
            $this->warn("  ✗ Syndication failed: {$e->getMessage()}");
        }
        $this->newLine();

        return $result;
    }

    /**
     * Render summary table
     */
    protected function renderSummary(array $summary, float $elapsed): void
    {
        $rows = [];

        if (isset($summary['intelligence'])) {
            $i = $summary['intelligence'];
            $rows[] = ['Intelligence', 'Keyword Clusters', $i['keyword_clusters']];
            $rows[] = ['', 'Topic Clusters', $i['topic_clusters']];
            $rows[] = ['', 'Content Gaps', $i['content_gaps']];
        }

        if (isset($summary['content'])) {
            $c = $summary['content'];
            $rows[] = ['Content', 'Topics from Clusters', $c['topics_from_clusters']];
            $rows[] = ['', 'Topics from Gaps', $c['topics_from_gaps']];
        }

        if (isset($summary['optimize'])) {
            $o = $summary['optimize'];
            $rows[] = ['Optimize', 'Meta Optimized', $o['meta_optimized']];
            $rows[] = ['', 'Articles Refreshed', $o['articles_refreshed']];
            $rows[] = ['', 'Articles Scored', $o['articles_scored']];
        }

        if (isset($summary['distribute'])) {
            $d = $summary['distribute'];
            $rows[] = ['Distribute', 'Sitemap', $d['sitemap'] ? '✓' : '✗'];
            $rows[] = ['', 'IndexNow', $d['indexnow'] ? '✓' : '✗'];
            $rows[] = ['', 'Syndication', $d['syndication']];
        }

        $this->table(['Phase', 'Metric', 'Value'], $rows);
        $this->info("Total time: {$elapsed}s");
        $this->info('');
    }
}
