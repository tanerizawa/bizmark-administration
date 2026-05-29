<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\OssCredential;
use App\Models\OssPermitStatus;
use App\Notifications\OssStatusChangedNotification;
use App\Services\OssScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOssStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        private readonly int $clientId,
        private readonly string $applicationNumber,
        private readonly string $permitType
    ) {}

    public function handle(OssScraperService $scraper): void
    {
        $credential = OssCredential::where('client_id', $this->clientId)
            ->where('is_active', true)
            ->first();

        if (! $credential) {
            Log::warning('[OSS] No active credential for client', ['client_id' => $this->clientId]);

            return;
        }

        $fetched = $scraper->fetchStatus($credential, $this->applicationNumber);
        if (! $fetched) {
            return;
        }

        // Find or create permit status record
        $record = OssPermitStatus::firstOrNew([
            'client_id' => $this->clientId,
            'application_number' => $this->applicationNumber,
        ]);

        $previousLabel = $record->status_label ?? null;
        $statusChanged = $record->exists && $record->status_code !== $fetched['status_code'];

        $record->fill([
            'permit_type' => $this->permitType,
            'status_code' => $fetched['status_code'],
            'status_label' => $fetched['status_label'],
            'raw_response' => $fetched['raw'],
            'last_checked_at' => now(),
            'status_changed_at' => $statusChanged ? now() : ($record->status_changed_at ?? now()),
        ])->save();

        // Notify client on status change
        if ($statusChanged && $previousLabel) {
            $client = Client::find($this->clientId);
            if ($client) {
                $client->notify(new OssStatusChangedNotification($record, $previousLabel));
            }
        }

        Log::info('[OSS] Status checked', [
            'client_id' => $this->clientId,
            'app_number' => $this->applicationNumber,
            'status' => $fetched['status_code'],
            'changed' => $statusChanged,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[OSS] Job failed: '.$e->getMessage(), [
            'client_id' => $this->clientId,
            'app_number' => $this->applicationNumber,
        ]);
    }
}
