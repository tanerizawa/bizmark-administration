<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Backlink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BacklinkMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backlink:monitor 
                            {--limit=50 : Maximum backlinks to check}
                            {--force : Force check all regardless of last check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor backlink health - check if backlinks are still active and accessible';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Backlink Health Monitor Started');
        $this->newLine();

        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        // Get backlinks to monitor (prioritize older checks)
        $query = Backlink::query()
            ->where('status', 'active')
            ->orderBy('last_checked_at', 'asc')
            ->orderBy('id', 'desc');

        if (!$force) {
            // Only check backlinks not checked in last 7 days
            $query->where(function($q) {
                $q->whereNull('last_checked_at')
                  ->orWhere('last_checked_at', '<', now()->subDays(7));
            });
        }

        $backlinks = $query->limit($limit)->get();

        if ($backlinks->isEmpty()) {
            $this->warn('⚠️  No backlinks need monitoring at this time');
            return 0;
        }

        $this->info("Found {$backlinks->count()} backlink(s) to monitor");
        $this->newLine();

        $stats = [
            'checked' => 0,
            'healthy' => 0,
            'broken' => 0,
            'errors' => 0,
        ];

        $progressBar = $this->output->createProgressBar($backlinks->count());
        $progressBar->start();

        foreach ($backlinks as $backlink) {
            $this->checkBacklink($backlink, $stats);
            $progressBar->advance();
            usleep(100000); // 0.1 second delay between requests
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('📊 Monitoring Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Healthy', $stats['healthy']],
                ['❌ Broken', $stats['broken']],
                ['⚠️  Errors', $stats['errors']],
                ['Total Checked', $stats['checked']],
            ]
        );

        $this->newLine();
        
        if ($stats['broken'] > 0) {
            $this->warn("⚠️  {$stats['broken']} backlink(s) are broken or inaccessible!");
            $this->info('   Run: php artisan backlink:crawl --all to re-verify');
        } else {
            $this->info('✅ All monitored backlinks are healthy!');
        }

        return 0;
    }

    /**
     * Check individual backlink health
     */
    private function checkBacklink(Backlink $backlink, array &$stats)
    {
        $stats['checked']++;

        try {
            // Make HTTP request to check if page is accessible
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; BizmarkBot/1.0; +https://bizmark.id)',
                ])
                ->get($backlink->source_url);

            $statusCode = $response->status();
            $isHealthy = $statusCode >= 200 && $statusCode < 400;

            if ($isHealthy) {
                // Check if bizmark.id link still exists in content
                $content = $response->body();
                $hasBizmarkLink = str_contains($content, 'bizmark.id');

                if ($hasBizmarkLink) {
                    $stats['healthy']++;
                    $backlink->update([
                        'last_checked_at' => now(),
                        'status' => 'active',
                        'notes' => "✅ Healthy (HTTP {$statusCode})",
                    ]);
                } else {
                    $stats['broken']++;
                    $backlink->update([
                        'last_checked_at' => now(),
                        'status' => 'lost',
                        'notes' => "❌ Link removed from page (HTTP {$statusCode})",
                    ]);
                }
            } else {
                $stats['broken']++;
                $backlink->update([
                    'last_checked_at' => now(),
                    'status' => 'lost',
                    'notes' => "❌ Broken - HTTP {$statusCode}",
                ]);
            }

        } catch (\Exception $e) {
            $stats['errors']++;
            $backlink->update([
                'last_checked_at' => now(),
                'status' => 'lost',
                'notes' => '⚠️ Error: ' . substr($e->getMessage(), 0, 200),
            ]);

            Log::warning('Backlink monitor error', [
                'backlink_id' => $backlink->id,
                'url' => $backlink->source_url,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
