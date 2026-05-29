<?php

namespace App\Jobs;

use App\Models\ComplianceReport;
use App\Services\ComplianceReportGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * P10 — Generate a compliance report PDF using AI + DomPDF.
 */
class GenerateComplianceReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 300; // 5 minutes for AI + PDF

    public function __construct(private int $reportId) {}

    public function handle(ComplianceReportGeneratorService $generator): void
    {
        $report = ComplianceReport::with(['template', 'project', 'generatedBy'])->find($this->reportId);

        if (! $report) {
            Log::warning("[P10] ComplianceReport #{$this->reportId} not found.");

            return;
        }

        $report->update(['status' => 'generating']);

        try {
            $pdfPath = $generator->generate($report);
            $report->update(['status' => 'ready', 'pdf_path' => $pdfPath]);

            Log::info("[P10] Report #{$report->id} generated: $pdfPath");

            // Notify the client
            $report->generatedBy->notify(
                new \App\Notifications\ComplianceReportReadyNotification($report)
            );
        } catch (\Throwable $e) {
            $report->update(['status' => 'draft']);
            Log::error('[P10] Report generation failed: '.$e->getMessage());
            throw $e;
        }
    }
}
