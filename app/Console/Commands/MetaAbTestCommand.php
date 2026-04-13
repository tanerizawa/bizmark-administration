<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MetaAbTestService;

class MetaAbTestCommand extends Command
{
    protected $signature = 'seo:meta-ab-test {--create : Auto-create tests for top articles} {--evaluate : Evaluate running tests} {--limit=3 : Max tests to create} {--all : Run both create and evaluate}';
    protected $description = 'Manage Meta A/B tests for improving CTR';

    public function handle(MetaAbTestService $service): int
    {
        $runAll = $this->option('all');

        if ($this->option('evaluate') || $runAll) {
            $this->info('📊 Evaluating running A/B tests...');
            $results = $service->evaluateTests();

            if (empty($results)) {
                $this->line('   No tests ready for evaluation yet.');
            } else {
                $rows = array_map(fn($r) => [
                    $r['test_id'],
                    \Illuminate\Support\Str::limit($r['article'], 40),
                    strtoupper($r['winner']),
                    $r['confidence'] . '%',
                    $r['ctr_a'] . '%',
                    $r['ctr_b'] . '%',
                ], $results);

                $this->table(['ID', 'Article', 'Winner', 'Confidence', 'CTR A', 'CTR B'], $rows);
                $this->info("✅ " . count($results) . " tests completed");
            }
        }

        if ($this->option('create') || $runAll) {
            $limit = (int) $this->option('limit');
            $this->info("🧪 Creating A/B tests for top {$limit} articles...");
            $created = $service->autoCreateTests($limit);

            if (empty($created)) {
                $this->line('   No eligible articles for new tests.');
            } else {
                foreach ($created as $test) {
                    $this->line("   ✓ Test #{$test->id}: " . ($test->article->title ?? 'Unknown'));
                }
                $this->info("✅ Created " . count($created) . " new A/B tests");
            }
        }

        if (!$this->option('create') && !$this->option('evaluate') && !$runAll) {
            // Show status summary
            $running = \App\Models\MetaAbTest::running()->count();
            $completed = \App\Models\MetaAbTest::where('status', 'completed')->count();
            $bWins = \App\Models\MetaAbTest::where('winner', 'b')->count();

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Running Tests', $running],
                    ['Completed Tests', $completed],
                    ['Variant B Wins', $bWins],
                    ['Win Rate', $completed > 0 ? round($bWins / $completed * 100) . '%' : 'N/A'],
                ]
            );

            $this->line('');
            $this->line('Usage: --create (new tests), --evaluate (check results), --all (both)');
        }

        return 0;
    }
}
