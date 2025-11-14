<?php

namespace App\Jobs;

use App\Models\DocumentDraft;
use App\Models\ComplianceCheck;
use App\Services\UKLUPLComplianceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ComplianceCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $draftId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(UKLUPLComplianceService $complianceService): void
    {
        try {
            $draft = DocumentDraft::findOrFail($this->draftId);

            Log::info('Starting compliance check', [
                'draft_id' => $this->draftId,
                'title' => $draft->title,
            ]);

            // Update status to checking
            ComplianceCheck::updateOrCreate(
                ['draft_id' => $this->draftId],
                ['status' => 'checking']
            );

            // Run compliance validation
            $complianceCheck = $complianceService->validate($draft);

            Log::info('Compliance check completed', [
                'draft_id' => $this->draftId,
                'overall_score' => $complianceCheck->overall_score,
                'total_issues' => $complianceCheck->total_issues,
                'critical_issues' => $complianceCheck->critical_issues,
            ]);

        } catch (\Exception $e) {
            Log::error('Compliance check job failed', [
                'draft_id' => $this->draftId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update status to failed
            ComplianceCheck::updateOrCreate(
                ['draft_id' => $this->draftId],
                [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Compliance check job permanently failed', [
            'draft_id' => $this->draftId,
            'error' => $exception->getMessage(),
        ]);

        ComplianceCheck::updateOrCreate(
            ['draft_id' => $this->draftId],
            [
                'status' => 'failed',
                'error_message' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage(),
            ]
        );
    }
}
