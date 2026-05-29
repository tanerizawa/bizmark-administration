<?php

namespace App\Jobs;

use App\Models\Client;
use App\Notifications\RegulatoryChangeAlertNotification;
use App\Services\RegulatoryAnalyzerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * P7 — Analyze one regulatory document and notify relevant clients.
 */
class AnalyzeRegulatoryChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(private array $document) {}

    public function handle(RegulatoryAnalyzerService $analyzer): void
    {
        $change = $analyzer->analyze($this->document);

        if ($change === null) {
            return; // Already exists or AI failed
        }

        Log::info("[P7] Analyzed: {$change->title} | score={$change->relevance_score}");

        if ($change->relevance_score < 0.3) {
            return; // Too low relevance — no notification
        }

        // Notify all active clients
        $clients = Client::where('is_active', true)->get();
        foreach ($clients as $client) {
            try {
                $client->notify(new RegulatoryChangeAlertNotification($change));
            } catch (\Throwable $e) {
                Log::warning("[P7] Notify failed for client {$client->id}: ".$e->getMessage());
            }
        }

        $change->update(['notified' => true]);

        Log::info("[P7] Notified {$clients->count()} clients for: {$change->title}");
    }
}
