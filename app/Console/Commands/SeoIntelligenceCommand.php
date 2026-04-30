<?php

namespace App\Console\Commands;

use App\Services\ContentGapService;
use App\Services\KeywordResearchService;
use App\Services\SmartMetaOptimizerService;
use App\Services\TopicClusterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoIntelligenceCommand extends Command
{
    protected $signature = 'seo:intelligence
        {--skip-keywords : Skip keyword research}
        {--skip-clusters : Skip topic cluster generation}
        {--skip-gaps : Skip content gap analysis}
        {--skip-meta : Skip meta optimization}
        {--queue-gaps=0 : Auto-queue top N gaps as article topics}
        {--meta-limit=3 : Number of articles to optimize meta}
        {--language=id : Language (id/en)}';

    protected $description = 'Full SEO intelligence pipeline: keywords → clusters → gaps → meta optimize';

    public function handle(
        KeywordResearchService $keywordService,
        TopicClusterService $clusterService,
        ContentGapService $gapService,
        SmartMetaOptimizerService $metaOptimizer
    ): int {
        try {
            $language = $this->option('language');
            $this->info('🧠 SEO Intelligence Pipeline Starting...');
            $this->newLine();

            // Phase 1: Keyword Research
            if (! $this->option('skip-keywords')) {
                $this->info('━━━ Phase 1: AI Keyword Research ━━━');
                $results = $keywordService->researchAllServices($language);
                $this->table(
                    ['Service', 'Cluster ID', 'Keywords', 'Long-tail'],
                    collect($results)->map(fn ($r) => [
                        $r['service'], $r['cluster_id'], $r['keywords_count'], $r['long_tail_count'],
                    ])->toArray()
                );
                $this->newLine();
            }

            // Phase 2: Topic Clusters
            if (! $this->option('skip-clusters')) {
                $this->info('━━━ Phase 2: Topic Clusters ━━━');
                $clusters = $clusterService->generateClusters($language);
                $this->table(
                    ['Service', 'Pillar', 'Subtopics', 'Articles'],
                    collect($clusters)->map(fn ($c) => [
                        $c['service'], mb_substr($c['pillar'], 0, 40), $c['subtopics'], $c['articles_mapped'],
                    ])->toArray()
                );
                $this->newLine();
            }

            // Phase 3: Content Gaps
            if (! $this->option('skip-gaps')) {
                $this->info('━━━ Phase 3: Content Gap Analysis ━━━');
                $allGaps = $gapService->analyzeAll($language);
                $totalGaps = 0;
                foreach ($allGaps as $name => $gaps) {
                    $this->info("  {$name}: ".count($gaps).' gaps');
                    $totalGaps += count($gaps);
                }
                $this->info("Total new content gaps: {$totalGaps}");

                $queueCount = (int) $this->option('queue-gaps');
                if ($queueCount > 0) {
                    $queued = $gapService->queueTopGaps($queueCount);
                    $this->info('Auto-queued '.count($queued).' gaps as article topics');
                }
                $this->newLine();
            }

            // Phase 4: Meta Optimization
            if (! $this->option('skip-meta')) {
                $metaLimit = (int) $this->option('meta-limit');
                $this->info("━━━ Phase 4: Meta Optimization ({$metaLimit} articles) ━━━");
                $results = $metaOptimizer->optimizeBatch($metaLimit, $language);
                $optimized = collect($results)->where('status', 'optimized')->count();
                $this->info("Optimized: {$optimized}/{$metaLimit} articles");
                $this->newLine();
            }

            $this->info('🎯 SEO Intelligence Pipeline Complete!');

            return 0;
        } catch (\Exception $e) {
            Log::error('SEO Intelligence Pipeline failed: '.$e->getMessage());
            $this->error('Pipeline failed: '.$e->getMessage());

            return 1;
        }
    }
}
