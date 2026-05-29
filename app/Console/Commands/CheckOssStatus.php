<?php

namespace App\Console\Commands;

use App\Jobs\CheckOssStatusJob;
use App\Models\OssPermitStatus;
use Illuminate\Console\Command;

class CheckOssStatus extends Command
{
    protected $signature = 'oss:check-status
                            {--client_id= : Hanya periksa client tertentu}
                            {--dry-run : Tampilkan jumlah tanpa menjalankan job}';

    protected $description = 'Dispatch CheckOssStatusJob untuk semua permit status yang perlu dicek';

    public function handle(): int
    {
        // Query permits yang belum dicek hari ini atau belum pernah dicek
        $query = OssPermitStatus::where(function ($q) {
            $q->whereNull('last_checked_at')
                ->orWhereDate('last_checked_at', '<', now()->toDateString());
        });

        if ($this->option('client_id')) {
            $query->where('client_id', (int) $this->option('client_id'));
        }

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Dry-run: {$count} permit status akan dicek.");

            return Command::SUCCESS;
        }

        $query->each(function (OssPermitStatus $status) {
            CheckOssStatusJob::dispatch(
                $status->client_id,
                $status->application_number,
                $status->permit_type
            )->onQueue('oss-tracker');
        });

        $this->info("Dispatched {$count} CheckOssStatusJob ke queue 'oss-tracker'.");

        return Command::SUCCESS;
    }
}
