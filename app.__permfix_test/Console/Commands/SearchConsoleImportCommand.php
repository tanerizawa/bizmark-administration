<?php

namespace App\Console\Commands;

use App\Services\SearchConsoleService;
use Illuminate\Console\Command;

class SearchConsoleImportCommand extends Command
{
    protected $signature = 'seo:gsc-import
                            {--days=7 : Days of data to import}
                            {--crossref : Cross-reference GSC data against keyword_clusters after import}
                            {--crossref-days=28 : Look-back window (days) for the cross-reference step}
                            {--status : Show current GSC credentials status and exit}';

    protected $description = 'Import Google Search Console data and optionally cross-reference against AI keyword estimates';

    public function handle(SearchConsoleService $service): int
    {
        // ── Status check only ────────────────────────────────────────────────
        if ($this->option('status')) {
            $hasReal = $service->hasRealCredentials();
            $this->info('GSC Credentials Status:');
            $this->table(['Setting', 'Configured'], [
                ['GOOGLE_CLIENT_ID',     config('services.google.client_id') ? '✅ Set' : '❌ Missing'],
                ['GOOGLE_CLIENT_SECRET', config('services.google.client_secret') ? '✅ Set' : '❌ Missing'],
                ['GOOGLE_REFRESH_TOKEN', config('services.google.refresh_token') ? '✅ Set' : '❌ Missing'],
                ['GSC_SITE_URL',         config('services.google.gsc_site_url') ? '✅ '.config('services.google.gsc_site_url') : '❌ Missing'],
            ]);
            $this->line($hasReal
                ? '<fg=green>✅ Real GSC API is ACTIVE</>  — next import will fetch live data'
                : '<fg=yellow>⚠ Simulation mode</>  — set all four .env keys to activate real GSC API'
            );

            return 0;
        }

        // ── Import step ──────────────────────────────────────────────────────
        $days = (int) $this->option('days');
        $this->info('📊 Importing Search Console data ('.$days.' days)...');

        $result = $service->importFromGSC($days);

        $source = $result['source'] === 'google_search_console'
            ? '<fg=green>Google Search Console API (REAL)</>'
            : '<fg=yellow>Simulated (credentials not configured)</>';

        $this->table(
            ['Metric', 'Value'],
            [
                ['Records Imported', number_format($result['imported'])],
                ['Data Source',      $source],
            ]
        );

        // Quick summary
        $summary = $service->getSummary($days);
        $this->newLine();
        $this->info('📈 Overview:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Clicks',       number_format($summary['total_clicks'])],
                ['Total Impressions',  number_format($summary['total_impressions'])],
                ['Avg CTR',            $summary['avg_ctr'].'%'],
                ['Avg Position',       $summary['avg_position']],
                ['Unique Queries',     $summary['unique_queries']],
                ['Unique Pages',       $summary['unique_pages']],
            ]
        );

        // Opportunities
        $opportunities = $service->getOpportunities($days);
        if ($opportunities->count() > 0) {
            $this->newLine();
            $this->info('🎯 Quick Win Opportunities (high impressions, low CTR, pos 5-20):');
            $rows = $opportunities->take(5)->map(fn ($o) => [
                \Illuminate\Support\Str::limit($o->query, 40),
                number_format($o->total_impressions),
                $o->total_clicks,
                round($o->avg_ctr, 1).'%',
                round($o->avg_position, 1),
            ])->toArray();
            $this->table(['Query', 'Impressions', 'Clicks', 'CTR', 'Position'], $rows);
        }

        // ── Cross-reference step (optional) ──────────────────────────────────
        if ($this->option('crossref')) {
            $crossDays = (int) $this->option('crossref-days');
            $this->newLine();
            $this->info("🔀 Cross-referencing GSC data against keyword clusters ({$crossDays} days)...");

            $xref = $service->crossReferenceWithKeywordClusters($crossDays);

            $this->line("  Updated clusters: <fg=green>{$xref['updated']}</>");

            if (! empty($xref['report'])) {
                $this->newLine();
                $this->info('📊 GSC vs AI Estimate Comparison:');
                $this->table(
                    ['Cluster', 'AI Est', 'Real Impr.', 'Real Clicks', 'Avg Pos', 'CTR', 'Discrepancy'],
                    array_map(fn ($r) => [
                        \Illuminate\Support\Str::limit($r['cluster'], 30),
                        number_format($r['ai_est_volume']),
                        number_format($r['real_impressions']),
                        number_format($r['real_clicks']),
                        $r['real_position'],
                        $r['real_ctr'],
                        $r['discrepancy_pct'],
                    ], $xref['report'])
                );
            }

            if (! empty($xref['discrepancies'])) {
                $this->newLine();
                $this->warn('⚠ Large Discrepancies Detected (action recommended):');
                foreach ($xref['discrepancies'] as $d) {
                    $this->line("  <fg=yellow>{$d['cluster']}</> — AI: {$d['ai_est']}, Real: {$d['real']}, Diff: {$d['diff_pct']}");
                    $this->line("  → {$d['action']}");
                }
            }
        }

        $this->newLine();
        $this->info('✅ GSC import complete');

        return 0;
    }
}
