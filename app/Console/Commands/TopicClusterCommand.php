<?php

namespace App\Console\Commands;

use App\Models\TopicCluster;
use App\Services\TopicClusterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TopicClusterCommand extends Command
{
    protected $signature = 'seo:topic-clusters
        {--build-links : Build internal links between cluster articles}
        {--language=id : Language (id/en)}';

    protected $description = 'Generate topic clusters and build internal links';

    public function handle(TopicClusterService $service): int
    {
        try {
            $language = $this->option('language');

            $this->info('🏗️  Generating topic clusters for all services...');
            $results = $service->generateClusters($language);

            if (empty($results)) {
                $this->warn('No clusters generated.');

                return 0;
            }

            $this->table(
                ['Service', 'Pillar', 'Subtopics', 'Articles Mapped'],
                collect($results)->map(fn ($c) => [
                    $c['service'], $c['pillar'], $c['subtopics'], $c['articles_mapped'],
                ])->toArray()
            );

            // Build internal links
            if ($this->option('build-links')) {
                $this->newLine();
                $this->info('🔗 Building internal links...');

                $totalLinks = 0;
                $clusters = TopicCluster::where('language', $language)->active()->get();

                foreach ($clusters as $cluster) {
                    $links = $service->buildInternalLinks($cluster);
                    if ($links > 0) {
                        $this->info("  {$cluster->pillar_title}: {$links} links built");
                        $totalLinks += $links;
                    }
                }

                $this->info("Total internal links built: {$totalLinks}");
            }

            $this->newLine();
            $this->info('✅ Topic clusters complete.');

            return 0;
        } catch (\Exception $e) {
            Log::error('Topic cluster generation failed: '.$e->getMessage());
            $this->error('Cluster generation failed: '.$e->getMessage());

            return 1;
        }
    }
}
