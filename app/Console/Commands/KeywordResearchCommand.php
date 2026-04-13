<?php

namespace App\Console\Commands;

use App\Services\KeywordResearchService;
use App\Services\TopicClusterService;
use App\Services\ContentGapService;
use Illuminate\Console\Command;

class KeywordResearchCommand extends Command
{
    protected $signature = 'seo:keyword-research
        {--service= : Research a specific service slug}
        {--topic= : Research a custom topic}
        {--all : Research all services}
        {--with-clusters : Also generate topic clusters}
        {--with-gaps : Also analyze content gaps}
        {--language=id : Language (id/en)}';

    protected $description = 'AI-powered keyword research and content intelligence';

    public function handle(
        KeywordResearchService $keywordService,
        TopicClusterService $clusterService,
        ContentGapService $gapService
    ): int {
        $language = $this->option('language');

        // Step 1: Keyword Research
        if ($this->option('all')) {
            $this->info('🔍 Researching keywords for all services...');
            $results = $keywordService->researchAllServices($language);

            $this->table(
                ['Service', 'Cluster ID', 'Keywords', 'Long-tail'],
                collect($results)->map(fn($r) => [
                    $r['service'], $r['cluster_id'], $r['keywords_count'], $r['long_tail_count']
                ])->toArray()
            );
        } elseif ($service = $this->option('service')) {
            $this->info("🔍 Researching keywords for: {$service}");
            $cluster = $keywordService->researchForService($service, $language);
            if ($cluster) {
                $this->info("✓ Cluster: {$cluster->cluster_name}");
                $this->info("  Keywords: " . count($cluster->keywords ?? []));
                $this->info("  Long-tail: " . count($cluster->long_tail_keywords ?? []));
                $this->info("  Top keywords: " . implode(', ', $cluster->getTopKeywords(5)));
            } else {
                $this->error("Failed to research keywords for: {$service}");
            }
        } elseif ($topic = $this->option('topic')) {
            $this->info("🔍 Researching keywords for topic: {$topic}");
            $cluster = $keywordService->researchForTopic($topic, 'general', $language);
            if ($cluster) {
                $this->info("✓ Cluster: {$cluster->cluster_name}");
                $this->info("  Keywords: " . implode(', ', array_slice($cluster->keywords ?? [], 0, 10)));
                $this->info("  Long-tail: " . implode(', ', array_slice($cluster->long_tail_keywords ?? [], 0, 10)));
            }
        } else {
            $this->info('Usage: --all, --service=slug, or --topic="your topic"');
            return 0;
        }

        // Step 2: Topic Clusters (optional)
        if ($this->option('with-clusters')) {
            $this->newLine();
            $this->info('🏗️  Generating topic clusters...');
            $clusters = $clusterService->generateClusters($language);

            $this->table(
                ['Service', 'Pillar', 'Subtopics', 'Articles Mapped'],
                collect($clusters)->map(fn($c) => [
                    $c['service'], $c['pillar'], $c['subtopics'], $c['articles_mapped']
                ])->toArray()
            );
        }

        // Step 3: Content Gap Analysis (optional)
        if ($this->option('with-gaps')) {
            $this->newLine();
            $this->info('🔎 Analyzing content gaps...');
            $gaps = $gapService->analyzeAll($language);

            $totalGaps = 0;
            foreach ($gaps as $clusterName => $clusterGaps) {
                $this->info("  {$clusterName}: " . count($clusterGaps) . " gaps found");
                $totalGaps += count($clusterGaps);
            }
            $this->info("Total content gaps found: {$totalGaps}");

            if ($totalGaps > 0 && $this->confirm('Queue top 5 gaps as article topics?', false)) {
                $queued = $gapService->queueTopGaps(5);
                $this->info("Queued " . count($queued) . " gaps as article topics");
                foreach ($queued as $q) {
                    $this->info("  → [{$q['topic_id']}] {$q['title']}");
                }
            }
        }

        $this->newLine();
        $this->info('✅ Keyword research complete.');

        return 0;
    }
}
