<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use App\Models\TestTemplate;
use App\Events\TestCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentEditingTestController extends Controller
{
    /**
     * Sanitize filename by removing invalid characters.
     */
    private function sanitizeFilename($filename)
    {
        // Remove invalid characters: / \ : * ? " < > |
        $sanitized = preg_replace('/[\/\\\:\*\?"<>\|]/', '_', $filename);
        // Replace multiple spaces with single underscore
        $sanitized = preg_replace('/\s+/', '_', $sanitized);
        // Remove leading/trailing underscores
        $sanitized = trim($sanitized, '_');
        // Limit length to 200 characters
        $sanitized = mb_substr($sanitized, 0, 200);
        
        return $sanitized ?: 'document'; // Fallback if empty
    }
    
    /**
     * Upload template file when creating test.
     */
    public function uploadTemplate(Request $request, TestTemplate $test)
    {
        $request->validate([
            'template_file' => 'required|file|mimes:doc,docx|max:10240', // 10MB
        ]);
        
        // Delete old file if exists
        if ($test->template_file_path) {
            Storage::disk('private')->delete($test->template_file_path);
        }
        
        // Store new file
        $path = $request->file('template_file')->store('test-templates', 'private');
        
        $test->update([
            'template_file_path' => $path,
        ]);
        
        return back()->with('success', 'Template file berhasil diupload.');
    }
    
    /**
     * Download template file (for candidates).
     */
    public function downloadTemplate($token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();
        
        // Check if test is document editing type
        if (!$session->testTemplate->isDocumentEditingTest()) {
            abort(403, 'Invalid test type');
        }
        
        // Check session status
        if ($session->status !== 'pending' && $session->status !== 'in-progress') {
            abort(403, 'Session expired or completed');
        }
        
        // Start session if pending
        if ($session->status === 'pending') {
            $session->update([
                'status' => 'in-progress',
                'started_at' => now(),
            ]);
        }
        
        $filePath = $session->testTemplate->template_file_path;
        
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'Template file not found');
        }
        
        return Storage::disk('private')->download(
            $filePath,
            'template_' . $session->id . '.docx'
        );
    }
    
    /**
     * Submit edited document.
     */
    public function submitDocument(Request $request, $token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();
        
        $request->validate([
            'document' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);
        
        // Check if time is still valid
        if (now()->gt($session->expires_at)) {
            $session->update(['status' => 'expired']);
            return back()->with('error', 'Waktu test telah habis.');
        }
        
        // Delete old submission if exists
        if ($session->submitted_file_path) {
            Storage::disk('private')->delete($session->submitted_file_path);
        }
        
        // Store submission
        $path = $request->file('document')->store('test-submissions', 'private');
        
        $session->update([
            'submitted_file_path' => $path,
            'submitted_at' => now(),
            'completed_at' => now(),
            'status' => 'completed',
            'requires_manual_review' => true,
            'time_taken_minutes' => $session->started_at ? 
                (int) $session->started_at->diffInMinutes(now()) : null,
        ]);
        
        return redirect()->route('candidate.test.show', $token)
            ->with('success', 'Dokumen berhasil disubmit. Menunggu penilaian dari evaluator.');
    }
    
    /**
     * Evaluation form for HR.
     */
    public function showEvaluationForm(TestSession $session)
    {
        // Check permission
        if (!auth()->user()->can('recruitment.manage')) {
            abort(403, 'Unauthorized');
        }
        
        if (!$session->requires_manual_review) {
            abort(403, 'This session does not require manual review');
        }
        
        $session->load(['application', 'testTemplate']);
        
        return view('admin.recruitment.tests.evaluate', compact('session'));
    }
    
    /**
     * Submit evaluation scores.
     */
    public function submitEvaluation(Request $request, TestSession $session)
    {
        // Check permission
        if (!auth()->user()->can('recruitment.manage')) {
            abort(403, 'Unauthorized');
        }
        
        // Log the incoming request for debugging
        \Log::info('Document Editing Evaluation Submission', [
            'session_id' => $session->id,
            'request_data' => $request->all(),
            'criteria_scores_count' => count($request->criteria_scores ?? []),
        ]);
        
        try {
            $request->validate([
                'criteria_scores' => 'required|array',
                'criteria_scores.*.criteria_id' => 'required|integer',
                'criteria_scores.*.score' => 'required|numeric|min:0',
                'criteria_scores.*.notes' => 'nullable|string',
                'evaluator_notes' => 'nullable|string|max:1000',
            ]);
        
            $evaluationScores = [
                'criteria_scores' => $request->criteria_scores,
                'total_score' => collect($request->criteria_scores)->sum('score'),
            ];
            
            $session->update([
                'evaluation_scores' => $evaluationScores,
            ]);
            
            $finalScore = $session->calculateScoreFromEvaluation();
            $passed = $finalScore >= $session->testTemplate->passing_score;
            
            $session->update([
                'score' => $finalScore,
                'passed' => $passed,
                'evaluator_id' => auth()->id(),
                'evaluator_notes' => $request->evaluator_notes,
                'evaluated_at' => now(),
                'requires_manual_review' => false,
            ]);
            
            \Log::info('Document Editing Evaluation Completed', [
                'session_id' => $session->id,
                'final_score' => $finalScore,
                'passed' => $passed,
                'evaluator_id' => auth()->id(),
            ]);

            // Dispatch event to trigger recruitment workflow automation
            if ($session->recruitment_stage_id) {
                event(new TestCompleted($session, $passed, $finalScore));
            }
            
            return redirect()->route('admin.recruitment.tests.sessions.results', $session)
                ->with('success', 'Evaluasi berhasil disimpan. Score: ' . number_format($finalScore, 2) . '%');
                
        } catch (\Exception $e) {
            \Log::error('Document Editing Evaluation Failed', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan evaluasi: ' . $e->getMessage());
        }
    }
    
    /**
     * Download submitted file (for evaluators).
     */
    public function downloadSubmission(TestSession $session)
    {
        // Check permission
        if (!auth()->user()->can('recruitment.manage')) {
            abort(403, 'Unauthorized');
        }
        
        if (!$session->submitted_file_path) {
            abort(404, 'No submission found');
        }
        
        if (!Storage::disk('private')->exists($session->submitted_file_path)) {
            abort(404, 'Submission file not found');
        }
        
        // Generate safe download filename
        $candidateName = $session->jobApplication->full_name ?? 'candidate';
        $filename = 'submission_' . $this->sanitizeFilename($candidateName) . '_' . $session->id . '.docx';
        
        return Storage::disk('private')->download(
            $session->submitted_file_path,
            $filename
        );
    }
    
    /**
     * Download template file (for HR).
     */
    public function downloadTemplateForHR(TestTemplate $test)
    {
        // Check permission
        if (!auth()->user()->can('recruitment.manage')) {
            abort(403, 'Unauthorized');
        }
        
        if (!$test->template_file_path) {
            abort(404, 'Template file not found');
        }
        
        if (!Storage::disk('private')->exists($test->template_file_path)) {
            abort(404, 'Template file not found on disk');
        }
        
        // Generate safe download filename
        $downloadFilename = 'template_' . $this->sanitizeFilename($test->title) . '.docx';
        
        return Storage::disk('private')->download(
            $test->template_file_path,
            $downloadFilename
        );
    }
}
