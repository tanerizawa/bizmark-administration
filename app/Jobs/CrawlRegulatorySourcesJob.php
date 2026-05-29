<?php

namespace App\Jobs;

use App\Services\RegulatorySourceCrawlerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * P7 — Master job: crawl all regulatory sources, dispatch analyze jobs per document.
 */
class CrawlRegulatorySourcesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function handle(RegulatorySourceCrawlerService $crawler): void
    {
        try {
            $documents = $crawler->crawlAll();
            Log::info('[P7] CrawlRegulatorySourcesJob: '.count($documents).' documents found.');

            foreach ($documents as $document) {
                AnalyzeRegulatoryChangeJob::dispatch($document)->delay(now()->addSeconds(2));
            }
        } catch (\Throwable $e) {
            Log::error('[P7] CrawlRegulatorySourcesJob failed: '.$e->getMessage(), [
                'exception' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // re-throw so queue marks job as failed and retries
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('[P7] CrawlRegulatorySourcesJob exhausted all retries.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
