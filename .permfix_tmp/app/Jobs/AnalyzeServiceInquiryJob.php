<?php

namespace App\Jobs;

use App\Models\ServiceInquiry;
use App\Services\FreeAIAnalysisService;
use App\Mail\ServiceInquiryResultEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnalyzeServiceInquiryJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300; // 5 min: primary model (60s×2 attempts) + fallback model (60s×2 attempts) + overhead

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $inquiryId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FreeAIAnalysisService $aiService): void
    {
        $inquiry = ServiceInquiry::find($this->inquiryId);

        if (!$inquiry) {
            Log::error('AnalyzeServiceInquiryJob: Inquiry not found', ['id' => $this->inquiryId]);
            return;
        }

        try {
            Log::info('AnalyzeServiceInquiryJob: Starting analysis', [
                'inquiry_number' => $inquiry->inquiry_number
            ]);

            // Prepare form data for AI - merge all relevant fields
            $formData = array_merge(
                [
                    'business_activity' => $inquiry->business_activity,
                    'kbli_code' => $inquiry->kbli_code,
                    'kbli_description' => $inquiry->kbli_description,
                    'company_type' => $inquiry->company_type,
                    'company_name' => $inquiry->company_name,
                ],
                $inquiry->form_data ?? []
            );

            // Call AI service
            $analysis = $aiService->analyze($formData);

            // Calculate priority based on complexity & estimated value
            $priority = $this->calculatePriority($analysis);

            // Update inquiry
            $inquiry->update([
                'ai_analysis' => $analysis,
                'ai_model_used' => $analysis['ai_model_used'] ?? 'unknown',
                'ai_tokens_used' => $analysis['ai_tokens_used'] ?? 0,
                'ai_processing_time' => $analysis['ai_processing_time'] ?? 0,
                'analyzed_at' => now(),
                'status' => 'analyzed',
                'priority' => $priority,
            ]);

            Log::info('AnalyzeServiceInquiryJob: Analysis completed', [
                'inquiry_number' => $inquiry->inquiry_number,
                'priority' => $priority,
                'tokens' => $analysis['ai_tokens_used'] ?? 0
            ]);

            // Send email to user with results
            Mail::to($inquiry->email)->queue(new ServiceInquiryResultEmail($inquiry));

            // TODO: Notify admin if high priority
            if ($priority === 'high') {
                // Slack notification or admin alert
                Log::info('AnalyzeServiceInquiryJob: High priority inquiry', [
                    'inquiry_number' => $inquiry->inquiry_number,
                    'company' => $inquiry->company_name,
                    'estimated_value' => $inquiry->estimated_value
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AnalyzeServiceInquiryJob: Analysis failed', [
                'inquiry_number' => $inquiry->inquiry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update status to indicate error for frontend polling detection
            $inquiry->update([
                'status' => 'error',
                'admin_notes' => ($inquiry->admin_notes ? $inquiry->admin_notes . "\n" : '') . 
                    '[' . now()->format('Y-m-d H:i') . '] AI analysis attempt ' . ($this->attempts()) . ' failed: ' . $e->getMessage()
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Calculate inquiry priority based on AI analysis
     */
    private function calculatePriority(array $analysis): string
    {
        $complexity = $analysis['complexity_score'] ?? 5;
        $estimatedCost = $analysis['total_estimated_cost'] ?? [];
        
        // Check grand_total first (AI response format), then fallback to direct min/max
        $avgCost = 0;
        if (isset($estimatedCost['grand_total']['min'], $estimatedCost['grand_total']['max'])) {
            $avgCost = ($estimatedCost['grand_total']['min'] + $estimatedCost['grand_total']['max']) / 2;
        } elseif (isset($estimatedCost['min'], $estimatedCost['max'])) {
            $avgCost = ($estimatedCost['min'] + $estimatedCost['max']) / 2;
        }

        // High priority: Complex projects (>7) OR high value (>100M)
        if ($complexity > 7 || $avgCost > 100000000) {
            return 'high';
        }

        // Low priority: Simple projects (<4) AND low value (<20M)
        if ($complexity < 4 && $avgCost < 20000000) {
            return 'low';
        }

        // Everything else is medium
        return 'medium';
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AnalyzeServiceInquiryJob: Job failed after retries', [
            'inquiry_id' => $this->inquiryId,
            'error' => $exception->getMessage()
        ]);

        // Update inquiry to indicate permanent failure - use fallback analysis
        $inquiry = ServiceInquiry::find($this->inquiryId);
        if ($inquiry) {
            // Generate fallback analysis so user still gets a result
            $aiService = app(FreeAIAnalysisService::class);
            $fallbackData = array_merge(
                [
                    'business_activity' => $inquiry->business_activity,
                    'company_type' => $inquiry->company_type,
                    'kbli_code' => $inquiry->kbli_code,
                    'kbli_description' => $inquiry->kbli_description,
                ],
                $inquiry->form_data ?? []
            );
            $fallback = $aiService->analyze($fallbackData); // Will return fallback since AI already failed
            
            $inquiry->update([
                'ai_analysis' => $fallback,
                'ai_model_used' => 'fallback-v2',
                'analyzed_at' => now(),
                'status' => 'analyzed',
                'priority' => 'medium',
                'admin_notes' => ($inquiry->admin_notes ? $inquiry->admin_notes . "\n" : '') .
                    '[' . now()->format('Y-m-d H:i') . '] AI analysis failed permanently - fallback used. Error: ' . $exception->getMessage()
            ]);

            // Still send email with fallback results
            try {
                Mail::to($inquiry->email)->queue(new ServiceInquiryResultEmail($inquiry->fresh()));
            } catch (\Exception $e) {
                Log::error('Failed to send fallback email', ['error' => $e->getMessage()]);
            }
        }
    }
}

