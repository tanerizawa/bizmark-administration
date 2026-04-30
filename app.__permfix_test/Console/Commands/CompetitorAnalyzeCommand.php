<?php

namespace App\Console\Commands;

use App\Services\CompetitiveIntelligenceService;
use Illuminate\Console\Command;

class CompetitorAnalyzeCommand extends Command
{
    protected $signature = 'seo:competitor-analyze {--keyword= : Specific keyword} {--limit=5 : Max keywords to analyze}';

    protected $description = 'Analyze competitor landscape for target keywords';

    public function handle(CompetitiveIntelligenceService $service): int
    {
        $keyword = $this->option('keyword');

        if ($keyword) {
            $this->info("🔍 Analyzing: {$keyword}");
            $result = $service->analyzeKeyword($keyword);

            if (! $result) {
                $this->error('Analysis failed.');

                return 1;
            }

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Keyword', $result->keyword],
                    ['Our Position', $result->our_position ?? 'Not ranking'],
                    ['Search Volume', number_format($result->search_volume ?? 0)],
                    ['Difficulty', $result->difficulty],
                    ['Competitors', count($result->top_competitors ?? [])],
                    ['Content Gaps', count($result->content_gaps ?? [])],
                    ['Recommendations', count($result->recommendations ?? [])],
                ]
            );

            if (! empty($result->content_gaps)) {
                $this->newLine();
                $this->info('📋 Content Gaps:');
                foreach ($result->content_gaps as $i => $gap) {
                    $this->line('   '.($i + 1).". {$gap}");
                }
            }

            if (! empty($result->recommendations)) {
                $this->newLine();
                $this->info('💡 Recommendations:');
                foreach ($result->recommendations as $i => $rec) {
                    $this->line('   '.($i + 1).". {$rec}");
                }
            }

            return 0;
        }

        $limit = (int) $this->option('limit');
        $this->info("🔍 Competitor Analysis — Top {$limit} keywords");

        $results = $service->analyzeTopKeywords($limit);

        if (empty($results)) {
            $this->info('No new keywords to analyze (all recently analyzed).');

            return 0;
        }

        $rows = array_map(fn ($r) => [
            $r->keyword,
            $r->our_position ?? '-',
            number_format($r->search_volume ?? 0),
            $r->difficulty,
            count($r->content_gaps ?? []),
        ], $results);

        $this->table(['Keyword', 'Position', 'Volume', 'Difficulty', 'Gaps'], $rows);
        $this->info('✅ Analyzed '.count($results).' keywords');

        return 0;
    }
}
