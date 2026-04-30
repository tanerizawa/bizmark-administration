<?php

namespace App\Console\Commands;

use App\Models\KeywordPositionHistory;
use App\Models\RankingAlert;
use App\Services\CompetitiveIntelligenceService;
use Illuminate\Console\Command;

class SeoPositionTrackingCommand extends Command
{
    protected $signature = 'seo:track-positions
        {--limit=50 : Maximum keywords to track}
        {--keyword= : Track a specific keyword}
        {--show-alerts : Show recent alerts}
        {--show-trends : Show position trends}
        {--summary : Show tracking summary only}
        {--days=7 : Days to include in trend/alerts}';

    protected $description = 'Track SERP positions for monitored keywords and generate alerts';

    public function handle(CompetitiveIntelligenceService $intelligence): int
    {
        $this->info('📊 SEO Position Tracking');
        $this->newLine();

        // Summary mode
        if ($this->option('summary')) {
            return $this->showSummary($intelligence);
        }

        // Show alerts only
        if ($this->option('show-alerts')) {
            return $this->showAlerts();
        }

        // Show trends only
        if ($this->option('show-trends')) {
            return $this->showTrends();
        }

        // Track specific keyword
        if ($keyword = $this->option('keyword')) {
            return $this->trackSingleKeyword($intelligence, $keyword);
        }

        // Batch tracking
        return $this->trackAllKeywords($intelligence);
    }

    protected function trackSingleKeyword(CompetitiveIntelligenceService $intelligence, string $keyword): int
    {
        $this->info("Tracking keyword: \"{$keyword}\"");

        $history = $intelligence->trackPosition($keyword);

        if (! $history) {
            $this->error("Failed to track position for: {$keyword}");

            return 1;
        }

        $this->displayPositionResult($history);

        return 0;
    }

    protected function trackAllKeywords(CompetitiveIntelligenceService $intelligence): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Tracking positions for up to {$limit} keywords...");
        $this->newLine();

        $bar = $this->output->createProgressBar($limit);
        $bar->start();

        $results = $intelligence->trackAllPositions($limit);

        $bar->finish();
        $this->newLine(2);

        // Summary table
        $this->table(['Metric', 'Count'], [
            ['Keywords Tracked', $results['tracked']],
            ['Skipped (already tracked today)', $results['skipped']],
            ['Failed', $results['failed']],
            ['Alerts Created', $results['alerts_created']],
        ]);

        // Show notable changes
        if (! empty($results['details'])) {
            $this->newLine();
            $this->info('━━━ Notable Position Changes ━━━');

            $notable = collect($results['details'])
                ->filter(fn ($d) => abs($d['change']) >= 3)
                ->sortByDesc(fn ($d) => abs($d['change']))
                ->take(10);

            if ($notable->isEmpty()) {
                $this->line('No significant position changes detected.');
            } else {
                $this->table(
                    ['Keyword', 'Position', 'Change'],
                    $notable->map(fn ($d) => [
                        mb_substr($d['keyword'], 0, 50),
                        $d['position'] ?? 'N/A',
                        $this->formatChange($d['change']),
                    ])->toArray()
                );
            }
        }

        // Show critical alerts
        $criticalAlerts = RankingAlert::critical()
            ->recent(1)
            ->get();

        if ($criticalAlerts->isNotEmpty()) {
            $this->newLine();
            $this->error('⚠️ CRITICAL ALERTS:');
            foreach ($criticalAlerts as $alert) {
                $this->line("  {$alert->alert_icon} {$alert->message}");
            }
        }

        return 0;
    }

    protected function showSummary(CompetitiveIntelligenceService $intelligence): int
    {
        $summary = $intelligence->getPositionTrackingSummary();

        $this->info('━━━ Position Tracking Summary ━━━');
        $this->newLine();

        // Stats table
        $stats = $summary['position_stats'];
        $tiers = $stats['tiers'];

        $this->table(['Metric', 'Value'], [
            ['Total Keywords Tracked', $stats['tracked_keywords']],
            ['Top 3', $tiers['top3']],
            ['Page 1 (4-10)', $tiers['page1']],
            ['Page 2 (11-20)', $tiers['page2']],
            ['Page 3+', $tiers['page3Plus']],
            ['Not Ranking', $tiers['notRanking']],
        ]);

        $this->newLine();
        $this->info('━━━ Recent Changes ━━━');
        $changes = $stats['changes'];
        $this->table(['Change Type', 'Count'], [
            ['↑ Gains', $changes['gains']],
            ['↓ Drops', $changes['drops']],
            ['→ Stable', $changes['stable']],
            ['Avg Change', $changes['avgChange']],
        ]);

        // Alert summary
        $this->newLine();
        $this->info('━━━ Alert Summary (7 days) ━━━');
        $alertSummary = $summary['alert_summary'];
        $this->table(['Alert Type', 'Count'], [
            ['Total Alerts', $alertSummary['total']],
            ['Unread', $alertSummary['unread']],
            ['Critical', $alertSummary['critical']],
            ['Warnings', $alertSummary['warnings']],
            ['Drops', $alertSummary['drops']],
            ['Gains', $alertSummary['gains']],
        ]);

        // At-risk keywords
        if (! empty($summary['at_risk_keywords'])) {
            $this->newLine();
            $this->warn('━━━ Keywords At Risk ━━━');
            $this->table(
                ['Keyword', 'Position', 'Change'],
                collect($summary['at_risk_keywords'])->take(10)->map(fn ($k) => [
                    mb_substr($k['keyword'], 0, 50),
                    $k['position'] ?? 'Lost',
                    $this->formatChange($k['position_change']),
                ])->toArray()
            );
        }

        return 0;
    }

    protected function showAlerts(): int
    {
        $days = (int) $this->option('days');

        $alerts = RankingAlert::recent($days)
            ->orderByDesc('severity')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        if ($alerts->isEmpty()) {
            $this->info("No alerts in the last {$days} days.");

            return 0;
        }

        $this->info("━━━ Recent Alerts (last {$days} days) ━━━");
        $this->newLine();

        foreach ($alerts as $alert) {
            $severityColor = match ($alert->severity) {
                RankingAlert::SEVERITY_CRITICAL => 'error',
                RankingAlert::SEVERITY_WARNING => 'warn',
                default => 'info',
            };

            $this->{$severityColor}(
                "{$alert->alert_icon} [{$alert->severity_label}] {$alert->message}"
            );
            $this->line("   Created: {$alert->created_at->diffForHumans()}");
        }

        return 0;
    }

    protected function showTrends(): int
    {
        $days = (int) $this->option('days');

        // Get top tracked keywords
        $keywords = KeywordPositionHistory::query()
            ->select('keyword')
            ->groupBy('keyword')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->pluck('keyword');

        if ($keywords->isEmpty()) {
            $this->info('No position history data available.');

            return 0;
        }

        $this->info("━━━ Position Trends (last {$days} days) ━━━");
        $this->newLine();

        foreach ($keywords as $keyword) {
            $trend = KeywordPositionHistory::getTrendFor($keyword, $days);

            if (empty($trend)) {
                continue;
            }

            $latest = end($trend);
            $first = reset($trend);

            $change = ($first['position'] ?? 0) - ($latest['position'] ?? 0);
            $arrow = $change > 0 ? '↑' : ($change < 0 ? '↓' : '→');

            $this->line(sprintf(
                '%s <comment>%s</comment>: #%s %s%d',
                $arrow,
                mb_substr($keyword, 0, 40),
                $latest['position'] ?? 'N/A',
                $change >= 0 ? '+' : '',
                $change
            ));
        }

        return 0;
    }

    protected function displayPositionResult(KeywordPositionHistory $history): void
    {
        $this->newLine();
        $this->table(['Field', 'Value'], [
            ['Keyword', $history->keyword],
            ['Position', $history->position ?? 'Not ranking'],
            ['Previous', $history->previous_position ?? 'N/A'],
            ['Change', $this->formatChange($history->position_change)],
            ['Rank Tier', $history->rank_tier],
            ['Data Source', $history->getDataSourceLabel()],
            ['URL', $history->our_url ?? 'N/A'],
            ['Tracked At', $history->tracked_at->format('Y-m-d')],
        ]);

        // Show competitors
        if (! empty($history->top_competitors)) {
            $this->newLine();
            $this->info('Top Competitors:');
            foreach (array_slice($history->top_competitors, 0, 5) as $i => $comp) {
                $pos = $comp['position'] ?? ($i + 1);
                $this->line("  #{$pos} {$comp['domain']} — {$comp['title']}");
            }
        }

        // Show alerts generated
        $alerts = $history->alerts;
        if ($alerts->isNotEmpty()) {
            $this->newLine();
            $this->warn('Alerts Generated:');
            foreach ($alerts as $alert) {
                $this->line("  {$alert->alert_icon} {$alert->message}");
            }
        }
    }

    protected function formatChange(int $change): string
    {
        if ($change === 0) {
            return '→ 0';
        }

        if ($change > 0) {
            return "<info>↑ +{$change}</info>";
        }

        $absChange = abs($change);

        return "<error>↓ -{$absChange}</error>";
    }
}
