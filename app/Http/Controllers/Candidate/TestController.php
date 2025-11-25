<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use App\Models\TestAnswer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TestController extends Controller
{
    /**
     * Show test session to candidate via token.
     */
    public function show(Request $request, string $token)
    {
        $session = TestSession::with(['testTemplate', 'jobApplication.jobVacancy'])
            ->where('session_token', $token)
            ->firstOrFail();

        // Check if expired
        if ($session->expires_at->isPast()) {
            return view('candidate.test-expired', ['testSession' => $session]);
        }

        // Check if already completed
        if ($session->status === 'completed') {
            return view('candidate.test-completed', ['testSession' => $session]);
        }

        // If not started, show instructions
        if ($session->status === 'not-started') {
            return view('candidate.test-instructions', ['testSession' => $session]);
        }

        // If started, show test interface
        $session->load('testAnswers');
        
        // Calculate remaining time
        if ($session->status === 'in-progress' && $session->started_at) {
            // Calculate time elapsed since started_at
            $elapsedMinutes = now()->diffInMinutes($session->started_at);
            $totalDuration = $session->testTemplate->duration_minutes;
            $remainingMinutes = max(0, $totalDuration - $elapsedMinutes);
        } else {
            // Test not started yet, use full duration
            $remainingMinutes = $session->testTemplate->duration_minutes;
        }
        
        $progress = $session->getProgressPercentage();
        
        // Check if this is a document-editing test
        if ($session->testTemplate->isDocumentEditingTest()) {
            return view('candidate.test-document-editing', [
                'testSession' => $session,
                'remainingMinutes' => $remainingMinutes,
            ]);
        }
        
        // Get questions from template for regular tests
        $questions = $session->testTemplate->questions_data ?? [];

        return view('candidate.test-taking', [
            'testSession' => $session,
            'remainingMinutes' => $remainingMinutes,
            'progress' => $progress,
            'questions' => $questions
        ]);
    }

    /**
     * Start test session.
     */
    public function start(Request $request, string $token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();

        // Validate can start
        if (!in_array($session->status, ['not-started', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Test sudah dimulai atau selesai.'
            ], 400);
        }

        if ($session->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Test sudah expired.'
            ], 400);
        }

        $session->start();

        return response()->json([
            'success' => true,
            'message' => 'Test dimulai. Waktu sedang berjalan!',
            'started_at' => $session->started_at->toIso8601String(),
        ]);
    }

    /**
     * Submit answer for a question.
     */
    public function submitAnswer(Request $request, string $token)
    {
        $validated = $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'required',
            'time_spent_seconds' => 'required|integer|min:0',
        ]);

        $session = TestSession::where('session_token', $token)->firstOrFail();

        // Check if session is active
        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Test session tidak aktif atau sudah expired.',
            ], 400);
        }

        // Save or update answer
        $answer = TestAnswer::updateOrCreate(
            [
                'test_session_id' => $session->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'answer' => $validated['answer'],
                'time_spent_seconds' => $validated['time_spent_seconds'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban tersimpan.',
            'answered_count' => $session->testAnswers()->count(),
            'total_questions' => $session->testTemplate->total_questions,
        ]);
    }

    /**
     * Complete test session.
     */
    public function complete(Request $request, string $token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();

        // Validate can complete
        if (!in_array($session->status, ['in-progress', 'started'])) {
            return redirect()
                ->route('candidate.test.show', $token)
                ->with('error', 'Test sudah diselesaikan atau tidak dapat disubmit.');
        }
        
        // Ensure started_at is set (fallback if user somehow bypassed start button)
        if (!$session->started_at) {
            $session->update(['started_at' => now()]);
        }

        // Process answers from form
        $answers = $request->input('answers', []);
        
        // Save all answers to database
        foreach ($answers as $questionIndex => $answerValue) {
            // Get question from template to find question_id
            $questions = $session->testTemplate->questions_data ?? [];
            
            if (isset($questions[$questionIndex])) {
                $question = $questions[$questionIndex];
                
                // Use question index as question_id since questions don't have id field
                $questionId = $questionIndex;
                
                // Prepare answer data
                $answerData = [
                    'question_index' => $questionIndex,
                    'question_text' => $question['question_text'] ?? '',
                    'question_type' => $question['question_type'] ?? '',
                    'answer_value' => $answerValue,
                ];
                
                // If multiple choice, include option text
                if (isset($question['question_type']) && $question['question_type'] === 'multiple-choice') {
                    if (isset($question['options'][$answerValue])) {
                        $answerData['answer_text'] = $question['options'][$answerValue];
                    }
                }
                
                // Save or update answer
                $session->testAnswers()->updateOrCreate(
                    ['question_id' => $questionId],
                    [
                        'answer_data' => $answerData,
                        'answered_at' => now(),
                    ]
                );
            }
        }

        // Complete test session
        $session->completeWithoutScore();

        // Reload with relationships for the completion page
        $session->load(['testTemplate', 'jobApplication.jobVacancy', 'testAnswers']);

        // Redirect to completion page (not back to test)
        return view('candidate.test-completed', ['testSession' => $session]);
    }

    /**
     * Track tab switch (anti-cheat).
     */
    public function trackTabSwitch(Request $request, string $token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();

        if ($session->status === 'started') {
            $session->incrementTabSwitches();

            // Optional: auto-complete if too many switches
            if ($session->tab_switches >= 5) {
                $session->update([
                    'status' => 'flagged',
                    'notes' => 'Auto-flagged: Too many tab switches (' . $session->tab_switches . ')',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Test telah ditandai karena terlalu banyak berpindah tab.',
                    'flagged' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'tab_switches' => $session->tab_switches,
                'warning' => $session->tab_switches >= 3 ? 'Peringatan: Jangan berpindah tab!' : null,
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Get remaining time (AJAX).
     */
    public function getRemainingTime(string $token)
    {
        $session = TestSession::where('session_token', $token)->firstOrFail();

        return response()->json([
            'remaining_minutes' => $session->getRemainingMinutes(),
            'is_active' => $session->isActive(),
            'status' => $session->status,
        ]);
    }
}
