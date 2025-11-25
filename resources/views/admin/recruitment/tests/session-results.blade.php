@extends('layouts.app')

@section('title', 'Test Session Results - ' . $session->jobApplication->full_name)

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
        </div>
        <div class="relative space-y-4">
            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                <a href="{{ route('admin.recruitment.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                    <i class="fas fa-arrow-left text-xs"></i> Rekrutmen
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <a href="{{ route('admin.recruitment.tests.index') }}" class="hover:text-white transition-apple">
                    Test
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <a href="{{ route('admin.recruitment.tests.show', $session->testTemplate) }}" class="hover:text-white transition-apple">
                    {{ $session->testTemplate->title }}
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <span>Session Results</span>
            </div>
            
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="space-y-2.5">
                    <h1 class="text-2xl font-semibold text-white leading-tight">Test Session Results</h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                        {{ $session->jobApplication->full_name }} - {{ $session->testTemplate->title }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @php
                            $statusColors = [
                                'completed' => ['bg' => 'rgba(52,199,89,0.2)', 'text' => 'rgba(52,199,89,1)', 'icon' => 'check-circle'],
                                'in-progress' => ['bg' => 'rgba(10,132,255,0.2)', 'text' => 'rgba(10,132,255,1)', 'icon' => 'clock'],
                                'expired' => ['bg' => 'rgba(255,69,58,0.2)', 'text' => 'rgba(255,69,58,1)', 'icon' => 'times-circle'],
                                'pending' => ['bg' => 'rgba(255,214,10,0.2)', 'text' => 'rgba(255,214,10,1)', 'icon' => 'hourglass-half'],
                            ];
                            $statusColor = $statusColors[$session->status] ?? ['bg' => 'rgba(142,142,147,0.2)', 'text' => 'rgba(142,142,147,1)', 'icon' => 'circle'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }};">
                            <i class="fas fa-{{ $statusColor['icon'] }} mr-1"></i>{{ ucfirst($session->status) }}
                        </span>
                        
                        @if($session->requires_manual_review)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                            <i class="fas fa-flag mr-1"></i>Requires Review
                        </span>
                        @endif
                        
                        @if($session->score !== null)
                            @if($session->passed)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                <i class="fas fa-trophy mr-1"></i>Passed
                            </span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,69,58,0.2); color: rgba(255,69,58,1);">
                                <i class="fas fa-times mr-1"></i>Not Passed
                            </span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($session->requires_manual_review && !$session->evaluated_at)
                    <a href="{{ route('admin.recruitment.tests.sessions.evaluate-manual', $session) }}" class="btn-primary">
                        <i class="fas fa-edit mr-2"></i>Evaluate Now
                    </a>
                    @endif
                    <button onclick="window.print()" class="btn-secondary">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <a href="{{ route('admin.recruitment.tests.show', $session->testTemplate) }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Test
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Candidate Info --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user" style="color: rgba(10,132,255,1);"></i>
            Candidate Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Full Name</p>
                    <p class="text-white font-medium mt-1">{{ $session->jobApplication->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Email</p>
                    <p class="text-white font-medium mt-1">{{ $session->jobApplication->email }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Phone</p>
                    <p class="text-white font-medium mt-1">{{ $session->jobApplication->phone ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Position Applied</p>
                    <p class="text-white font-medium mt-1">{{ $session->jobApplication->jobVacancy->title ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Application Date</p>
                    <p class="text-white font-medium mt-1">{{ $session->jobApplication->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">IP Address</p>
                    <p class="text-white font-medium mt-1">{{ $session->ip_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Document Editing Test - Submitted Files --}}
    @if($session->testTemplate->isDocumentEditingTest())
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-file-word" style="color: rgba(10,132,255,1);"></i>
            Document Editing Test - Files
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Template File --}}
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(10,132,255,0.2);">
                        <i class="fas fa-download text-xl" style="color: rgba(10,132,255,1);"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">Template File</p>
                        <p class="text-white font-medium text-sm truncate mb-2">{{ basename($session->testTemplate->template_file_path) }}</p>
                        @if($session->testTemplate->template_file_path && \Storage::disk('private')->exists($session->testTemplate->template_file_path))
                            <a href="{{ route('admin.recruitment.tests.download-template', $session->testTemplate) }}" 
                               class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-lg transition-apple" 
                               style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                <i class="fas fa-download"></i>
                                Download Template
                            </a>
                        @else
                            <p class="text-xs" style="color: rgba(255,69,58,1);">
                                <i class="fas fa-exclamation-circle mr-1"></i>File not found
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Submitted File --}}
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(52,199,89,0.2);">
                        <i class="fas fa-upload text-xl" style="color: rgba(52,199,89,1);"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">Submitted File</p>
                        @if($session->submitted_file_path)
                            <p class="text-white font-medium text-sm truncate mb-1">{{ basename($session->submitted_file_path) }}</p>
                            <p class="text-xs mb-2" style="color: rgba(235,235,245,0.5);">
                                Submitted: {{ $session->submitted_at ? $session->submitted_at->format('d M Y, H:i') : 'N/A' }}
                            </p>
                            @if(\Storage::disk('private')->exists($session->submitted_file_path))
                                <a href="{{ route('admin.recruitment.tests.sessions.download-submission', $session) }}" 
                                   class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-lg transition-apple" 
                                   style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                    <i class="fas fa-download"></i>
                                    Download Submission
                                </a>
                            @else
                                <p class="text-xs" style="color: rgba(255,69,58,1);">
                                    <i class="fas fa-exclamation-circle mr-1"></i>File not found
                                </p>
                            @endif
                        @else
                            <p class="text-white font-medium mb-1">No submission yet</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">Candidate has not uploaded their work</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluation Criteria Display --}}
        @if($session->testTemplate->evaluation_criteria && isset($session->testTemplate->evaluation_criteria['criteria']))
        <div class="mt-6">
            <h3 class="text-base font-semibold text-white mb-3 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-sm" style="color: rgba(255,149,0,1);"></i>
                Evaluation Criteria
            </h3>
            
            <div class="grid grid-cols-1 gap-3">
                @php
                    $groupedCriteria = collect($session->testTemplate->evaluation_criteria['criteria'])->groupBy('category');
                    $evaluationScores = $session->evaluation_scores['criteria_scores'] ?? [];
                @endphp
                
                @foreach($groupedCriteria as $category => $criteria)
                    <div class="card-nested rounded-apple-lg p-4">
                        <h4 class="text-sm font-semibold text-white mb-3">{{ $category }}</h4>
                        <div class="space-y-2">
                            @foreach($criteria as $index => $criterion)
                                @php
                                    $globalIndex = collect($session->testTemplate->evaluation_criteria['criteria'])->search($criterion);
                                    $score = collect($evaluationScores)->firstWhere('criteria_id', $globalIndex);
                                @endphp
                                <div class="flex items-start justify-between gap-3 p-2 rounded-lg" style="background: rgba(255,255,255,0.03);">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white text-sm">{{ $criterion['description'] }}</p>
                                        @if(isset($criterion['type']))
                                            <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.5);">
                                                <i class="fas fa-tag mr-1"></i>{{ $criterion['type'] }}
                                            </p>
                                        @endif
                                        @if($score && isset($score['notes']) && $score['notes'])
                                            <p class="text-xs mt-1 p-2 rounded" style="background: rgba(10,132,255,0.1); color: rgba(10,132,255,1);">
                                                <i class="fas fa-comment mr-1"></i>{{ $score['notes'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        <span class="text-white font-semibold">
                                            @if($score)
                                                {{ $score['score'] }}
                                            @else
                                                <span style="color: rgba(142,142,147,1);">-</span>
                                            @endif
                                             / {{ $criterion['points'] }}
                                        </span>
                                        @if($score)
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                                <i class="fas fa-check text-xs"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                {{-- Total Score --}}
                @if($session->evaluation_scores && isset($session->evaluation_scores['total_score']))
                <div class="card-nested rounded-apple-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-white">Total Score</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                Out of {{ $session->testTemplate->evaluation_criteria['total_points'] ?? 100 }} points
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold" style="color: rgba(52,199,89,1);">
                                {{ $session->evaluation_scores['total_score'] }}
                            </p>
                            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                                ({{ number_format(($session->evaluation_scores['total_score'] / ($session->testTemplate->evaluation_criteria['total_points'] ?? 100)) * 100, 1) }}%)
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- Test Statistics --}}
    <section class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Score</p>
                <i class="fas fa-star text-lg" style="color: rgba(10,132,255,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ $session->score !== null ? number_format($session->score, 1) : 'N/A' }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                @if($session->score !== null)
                    Passing: {{ $session->testTemplate->passing_score }}
                @else
                    Not graded yet
                @endif
            </p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Answered</p>
                <i class="fas fa-check-double text-lg" style="color: rgba(52,199,89,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ $session->testAnswers->count() }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                Total: {{ count($session->testTemplate->questions_data ?? []) }}
            </p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Duration</p>
                <i class="fas fa-clock text-lg" style="color: rgba(255,149,0,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">
                {{ $session->time_taken_minutes ?? ($session->started_at && $session->completed_at ? $session->started_at->diffInMinutes($session->completed_at) : 0) }}
            </p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                Minutes
            </p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Tab Switches</p>
                <i class="fas fa-window-restore text-lg" style="color: rgba(191,90,242,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ $session->tab_switches }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                Anti-cheat
            </p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Completed</p>
                <i class="fas fa-calendar-check text-lg" style="color: rgba(255,69,58,0.5);"></i>
            </div>
            <p class="text-lg font-bold text-white">
                @if($session->completed_at)
                    {{ $session->completed_at->format('d M Y') }}
                @else
                    N/A
                @endif
            </p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                @if($session->completed_at)
                    {{ $session->completed_at->format('H:i') }} WIB
                @else
                    Not completed
                @endif
            </p>
        </div>
    </section>

    {{-- Grading Info --}}
    @if($session->score !== null || $session->requires_manual_review)
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line" style="color: rgba(10,132,255,1);"></i>
            Grading Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($session->score !== null)
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                        <i class="fas fa-robot text-xl" style="color: rgba(52,199,89,1);"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Auto-Graded</p>
                        <p class="text-white font-semibold">{{ $session->score }}%</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Objective questions scored automatically</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($session->requires_manual_review)
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                        <i class="fas fa-user-edit text-xl" style="color: rgba(255,149,0,1);"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Manual Review</p>
                        <p class="text-white font-semibold">{{ $session->evaluated_at ? 'Completed' : 'Pending' }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Subjective questions need evaluation</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        @if($session->requires_manual_review && !$session->evaluated_at)
        <div class="mt-4 p-4 rounded-lg" style="background: rgba(255,149,0,0.1); border: 1px solid rgba(255,149,0,0.3);">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-xl" style="color: rgba(255,149,0,1);"></i>
                    <div>
                        <p class="text-white font-semibold mb-1">Action Required</p>
                        <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                            This test contains essay or rating questions that require manual evaluation. 
                            Click the button to start evaluating subjective questions.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.recruitment.tests.sessions.evaluate-manual', $session) }}" 
                   class="btn-primary flex-shrink-0">
                    <i class="fas fa-edit mr-2"></i>Evaluate Now
                </a>
            </div>
        </div>
        @endif
    </section>
    @endif

    {{-- Questions and Answers --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-list-alt" style="color: rgba(52,199,89,1);"></i>
                Questions & Answers
            </h2>
            <span class="text-sm" style="color: rgba(235,235,245,0.6);">
                {{ $session->testAnswers->count() }} of {{ count($session->testTemplate->questions_data ?? []) }} answered
            </span>
        </div>

        @php
            $questions = $session->testTemplate->questions_data ?? [];
            $answers = $session->testAnswers->keyBy('question_id');
        @endphp

        @if(count($questions) > 0)
            <div class="space-y-4">
                @foreach($questions as $index => $question)
                    @php
                        $answer = $answers->get($index);
                        $answerData = $answer ? $answer->answer_data : null;
                        $answerValue = $answerData['answer_value'] ?? null;
                    @endphp
                    
                    <div class="card-nested rounded-apple-lg p-4 space-y-3">
                        {{-- Question Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold" 
                                          style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-white font-medium mb-1">{{ $question['question_text'] ?? 'No question text' }}</p>
                                        <div class="flex items-center gap-2 text-xs flex-wrap">
                                            <span class="px-2 py-0.5 rounded-full" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                                                {{ ucfirst(str_replace('-', ' ', $question['question_type'] ?? 'N/A')) }}
                                            </span>
                                            @if(isset($question['points']))
                                            <span class="px-2 py-0.5 rounded-full" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                                {{ $question['points'] }} points
                                            </span>
                                            @endif
                                            
                                            {{-- Auto-grading indicator --}}
                                            @if($question['question_type'] === 'multiple-choice' && isset($question['correct_answer']))
                                            <span class="px-2 py-0.5 rounded-full" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                                                <i class="fas fa-robot mr-1"></i>Auto-graded
                                            </span>
                                            @elseif(in_array($question['question_type'], ['essay', 'rating-scale']))
                                            <span class="px-2 py-0.5 rounded-full" style="background: rgba(191,90,242,0.2); color: rgba(191,90,242,1);">
                                                <i class="fas fa-user-edit mr-1"></i>Manual review
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Answer Status Badge --}}
                            @if($answer)
                                <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                    <i class="fas fa-check mr-1"></i>Answered
                                </span>
                            @else
                                <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(142,142,147,0.2); color: rgba(142,142,147,1);">
                                    <i class="fas fa-minus mr-1"></i>No Answer
                                </span>
                            @endif
                        </div>

                        {{-- Answer Display --}}
                        @if($answer)
                            <div class="pl-11 space-y-2">
                                @if($question['question_type'] === 'multiple-choice')
                                    {{-- Multiple Choice Answer --}}
                                    @if(isset($question['options']))
                                        <div class="space-y-1.5">
                                            @foreach($question['options'] as $optIndex => $option)
                                                @php
                                                    $isSelected = $answerValue == $optIndex;
                                                    $isCorrect = isset($question['correct_answer']) && $question['correct_answer'] == $optIndex;
                                                @endphp
                                                
                                                <div class="flex items-start gap-2 p-2.5 rounded-lg transition-apple {{ $isSelected ? ($isCorrect ? 'bg-green-500/10 border border-green-500/30' : 'bg-red-500/10 border border-red-500/30') : 'bg-white/5' }}">
                                                    <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold" 
                                                          style="background: {{ $isSelected ? ($isCorrect ? 'rgba(52,199,89,0.2)' : 'rgba(255,69,58,0.2)') : 'rgba(142,142,147,0.2)' }}; 
                                                                 color: {{ $isSelected ? ($isCorrect ? 'rgba(52,199,89,1)' : 'rgba(255,69,58,1)') : 'rgba(142,142,147,1)' }};">
                                                        {{ chr(65 + $optIndex) }}
                                                    </span>
                                                    <p class="flex-1 text-sm {{ $isSelected ? 'text-white font-medium' : 'text-dark-text-secondary' }}">
                                                        {{ $option }}
                                                    </p>
                                                    @if($isSelected)
                                                        @if($isCorrect)
                                                            <i class="fas fa-check-circle text-green-500"></i>
                                                        @else
                                                            <i class="fas fa-times-circle text-red-500"></i>
                                                        @endif
                                                    @elseif($isCorrect)
                                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                @elseif($question['question_type'] === 'essay')
                                    {{-- Essay Answer --}}
                                    <div class="p-3 rounded-lg" style="background: rgba(255,255,255,0.05);">
                                        <p class="text-sm" style="color: rgba(235,235,245,0.8); white-space: pre-wrap;">{{ $answerValue ?? 'No answer provided' }}</p>
                                    </div>
                                    
                                @elseif($question['question_type'] === 'rating-scale')
                                    {{-- Rating Scale Answer --}}
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm" style="color: rgba(235,235,245,0.6);">Rating:</p>
                                        <div class="flex gap-1">
                                            @for($i = 1; $i <= ($question['max_rating'] ?? 5); $i++)
                                                <i class="fas fa-star {{ $i <= $answerValue ? 'text-yellow-400' : 'text-gray-600' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-white font-semibold">{{ $answerValue ?? 0 }} / {{ $question['max_rating'] ?? 5 }}</span>
                                    </div>
                                    
                                @else
                                    {{-- Other Types --}}
                                    <div class="p-3 rounded-lg" style="background: rgba(255,255,255,0.05);">
                                        <p class="text-sm" style="color: rgba(235,235,245,0.8);">{{ $answerValue ?? 'No answer provided' }}</p>
                                    </div>
                                @endif
                                
                                {{-- Answer Timestamp --}}
                                @if($answer->answered_at)
                                <p class="text-xs" style="color: rgba(235,235,245,0.4);">
                                    <i class="fas fa-clock mr-1"></i>Answered at {{ $answer->answered_at->format('H:i:s') }}
                                </p>
                                @endif
                            </div>
                        @else
                            <div class="pl-11">
                                <p class="text-sm italic" style="color: rgba(235,235,245,0.4);">
                                    Candidate did not answer this question
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-5xl mb-3" style="color: rgba(142,142,147,0.5);"></i>
                <p class="text-dark-text-secondary">No questions found in this test template.</p>
            </div>
        @endif
    </section>

    {{-- Evaluator Notes (if manually reviewed) --}}
    @if($session->evaluator_notes || $session->evaluator_id)
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-comment-dots" style="color: rgba(191,90,242,1);"></i>
            Evaluator Notes
        </h2>
        
        <div class="space-y-3">
            @if($session->evaluator_id)
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Evaluated By</p>
                <p class="text-white font-medium mt-1">{{ $session->evaluator->name ?? 'Unknown' }}</p>
            </div>
            @endif
            
            @if($session->evaluated_at)
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Evaluated At</p>
                <p class="text-white font-medium mt-1">{{ $session->evaluated_at->format('d M Y, H:i') }}</p>
            </div>
            @endif
            
            @if($session->evaluator_notes)
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Notes</p>
                <div class="mt-2 p-3 rounded-lg" style="background: rgba(255,255,255,0.05);">
                    <p class="text-sm" style="color: rgba(235,235,245,0.8); white-space: pre-wrap;">{{ $session->evaluator_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif
</div>

<style>
@media print {
    .btn-primary, .btn-secondary {
        display: none;
    }
}
</style>
@endsection
