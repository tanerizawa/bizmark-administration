@extends('layouts.app')

@section('title', 'Evaluate Test Session - ' . $session->jobApplication->full_name)

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-purple opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
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
                <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" class="hover:text-white transition-apple">
                    Results
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <span>Evaluate</span>
            </div>
            
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="space-y-2.5">
                    <h1 class="text-2xl font-semibold text-white leading-tight">Manual Evaluation</h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                        Score subjective questions for {{ $session->jobApplication->full_name }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                            <i class="fas fa-hourglass-half mr-1"></i>Pending Evaluation
                        </span>
                        @if($session->score !== null)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                            <i class="fas fa-robot mr-1"></i>Partial Score: {{ number_format($session->score, 1) }}%
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Results
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="rounded-apple-lg p-4" style="background: rgba(52,199,89,0.15); border: 1px solid rgba(52,199,89,0.3);">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg" style="color: rgba(52,199,89,1);"></i>
                <p class="text-white">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4" style="background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3);">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg" style="color: rgba(255,59,48,1);"></i>
                <p class="text-white">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-apple-lg p-4" style="background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3);">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-lg" style="color: rgba(255,59,48,1);"></i>
                <div>
                    <p class="text-white font-semibold mb-2">Validation Errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-white text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Candidate Info --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user" style="color: rgba(10,132,255,1);"></i>
            Candidate Information
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Full Name</p>
                <p class="text-white font-medium mt-1">{{ $session->jobApplication->full_name }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Position Applied</p>
                <p class="text-white font-medium mt-1">{{ $session->jobApplication->jobVacancy->title ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Completed At</p>
                <p class="text-white font-medium mt-1">{{ $session->completed_at ? $session->completed_at->format('d M Y, H:i') : 'N/A' }}</p>
            </div>
        </div>
    </section>

    {{-- Document Editing Test - Files & Criteria --}}
    @if($session->testTemplate->isDocumentEditingTest())
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-file-word" style="color: rgba(10,132,255,1);"></i>
            Document Files
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
                               style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);"
                               target="_blank">
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
                                   style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);"
                                   target="_blank">
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
    </section>
    @endif

    {{-- Test Summary --}}
    @if($session->score !== null)
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line" style="color: rgba(52,199,89,1);"></i>
            Auto-Grading Summary
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                        <i class="fas fa-robot text-xl" style="color: rgba(10,132,255,1);"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Current Score</p>
                        <p class="text-white font-semibold text-2xl">{{ number_format($session->score, 1) }}%</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">From objective questions</p>
                    </div>
                </div>
            </div>
            
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                        <i class="fas fa-tasks text-xl" style="color: rgba(255,149,0,1);"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pending Questions</p>
                        <p class="text-white font-semibold text-2xl">{{ count($subjectiveQuestions) }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Need manual scoring</p>
                    </div>
                </div>
            </div>
            
            <div class="card-nested rounded-apple-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(191,90,242,0.2);">
                        <i class="fas fa-graduation-cap text-xl" style="color: rgba(191,90,242,1);"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Passing Score</p>
                        <p class="text-white font-semibold text-2xl">{{ $session->testTemplate->passing_score }}%</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Required to pass</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Evaluation Form --}}
    <form action="{{ $session->testTemplate->isDocumentEditingTest() 
        ? route('admin.recruitment.tests.sessions.submit-evaluation', $session) 
        : route('admin.recruitment.tests.sessions.submit-evaluation-manual', $session) }}" method="POST">
        @csrf
        
        {{-- Document Editing Criteria Evaluation --}}
        @if($session->testTemplate->isDocumentEditingTest() && $session->testTemplate->evaluation_criteria)
        <section class="card-elevated rounded-apple-xl p-5 md:p-6">
            <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-clipboard-check" style="color: rgba(255,149,0,1);"></i>
                Evaluation Criteria
            </h2>

            <div class="space-y-6">
                @php
                    $groupedCriteria = collect($session->testTemplate->evaluation_criteria['criteria'])->groupBy('category');
                @endphp
                
                @foreach($groupedCriteria as $category => $criteria)
                    <div class="card-nested rounded-apple-lg p-5">
                        <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-folder-open text-sm" style="color: rgba(10,132,255,1);"></i>
                            {{ $category }}
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($criteria as $index => $criterion)
                                @php
                                    $globalIndex = collect($session->testTemplate->evaluation_criteria['criteria'])->search($criterion);
                                @endphp
                                
                                <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex-1">
                                            <p class="text-white font-medium mb-1">{{ $criterion['description'] }}</p>
                                            <div class="flex items-center gap-2 text-xs flex-wrap">
                                                <span class="px-2 py-0.5 rounded-full" style="background: rgba(191,90,242,0.2); color: rgba(191,90,242,1);">
                                                    <i class="fas fa-tag mr-1"></i>{{ $criterion['type'] }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                                    Max: {{ $criterion['points'] }} points
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Score Input --}}
                                        <div>
                                            <label class="block mb-2">
                                                <span class="text-sm font-medium text-white">Score <span class="text-red-500">*</span></span>
                                            </label>
                                            <input type="hidden" name="criteria_scores[{{ $globalIndex }}][criteria_id]" value="{{ $globalIndex }}">
                                            <div class="flex items-center gap-3">
                                                <input 
                                                    type="number" 
                                                    name="criteria_scores[{{ $globalIndex }}][score]" 
                                                    min="0" 
                                                    max="{{ $criterion['points'] }}" 
                                                    step="0.5"
                                                    class="form-control flex-1 bg-dark-secondary border-dark-border text-white focus:border-apple-blue focus:ring-apple-blue"
                                                    placeholder="0"
                                                    required
                                                >
                                                <span class="text-sm whitespace-nowrap" style="color: rgba(235,235,245,0.6);">/ {{ $criterion['points'] }}</span>
                                            </div>
                                            @error('criteria_scores.' . $globalIndex . '.score')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        {{-- Notes Input --}}
                                        <div>
                                            <label class="block mb-2">
                                                <span class="text-sm font-medium text-white">Notes (Optional)</span>
                                            </label>
                                            <textarea 
                                                name="criteria_scores[{{ $globalIndex }}][notes]" 
                                                rows="2" 
                                                class="form-control w-full bg-dark-secondary border-dark-border text-white focus:border-apple-blue focus:ring-apple-blue text-sm"
                                                placeholder="Add specific feedback for this criterion..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                {{-- Total Points Info --}}
                <div class="card-nested rounded-apple-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-white mb-1">Total Points Available</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                Sum of all evaluation criteria
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold" style="color: rgba(10,132,255,1);">
                                {{ $session->testTemplate->evaluation_criteria['total_points'] ?? 100 }}
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">points</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @else
        {{-- Regular Question Evaluation --}}
        <section class="card-elevated rounded-apple-xl p-5 md:p-6">
            <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-clipboard-list" style="color: rgba(255,149,0,1);"></i>
                Subjective Questions to Evaluate
            </h2>

            @if(count($subjectiveQuestions) === 0)
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-5xl mb-3" style="color: rgba(52,199,89,0.5);"></i>
                    <p class="text-dark-text-secondary">No subjective questions found. Test is fully auto-graded.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($subjectiveQuestions as $index => $item)
                        @php
                            $question = $item['question'];
                            $answer = $item['answer'];
                            $answerValue = $answer ? ($answer->answer_data['answer_value'] ?? null) : null;
                            $maxPoints = $question['points'] ?? 10;
                        @endphp
                        
                        <div class="card-nested rounded-apple-lg p-5 space-y-4">
                            {{-- Question Header --}}
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold" 
                                      style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                                    Q{{ $index + 1 }}
                                </span>
                                <div class="flex-1">
                                    <p class="text-white font-medium text-lg mb-2">{{ $question['question_text'] ?? 'No question text' }}</p>
                                    <div class="flex items-center gap-2 text-xs flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full" style="background: rgba(191,90,242,0.2); color: rgba(191,90,242,1);">
                                            {{ ucfirst(str_replace('-', ' ', $question['question_type'] ?? 'N/A')) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">
                                            Max: {{ $maxPoints }} points
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Candidate Answer --}}
                            <div class="pl-13">
                                <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Candidate's Answer:</p>
                                
                                @if($answer && $answerValue)
                                    @if($question['question_type'] === 'essay')
                                        <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                            <p class="text-sm" style="color: rgba(235,235,245,0.8); white-space: pre-wrap;">{{ $answerValue }}</p>
                                        </div>
                                    @elseif($question['question_type'] === 'rating' || $question['question_type'] === 'rating-scale')
                                        <div class="flex items-center gap-3">
                                            <div class="flex gap-1">
                                                @for($i = 1; $i <= ($question['max_rating'] ?? 5); $i++)
                                                    <i class="fas fa-star {{ $i <= $answerValue ? 'text-yellow-400' : 'text-gray-600' }} text-xl"></i>
                                                @endfor
                                            </div>
                                            <span class="text-white font-semibold text-lg">{{ $answerValue }} / {{ $question['max_rating'] ?? 5 }}</span>
                                        </div>
                                        @if(isset($question['options']) && is_array($question['options']) && isset($question['options'][$answerValue - 1]))
                                            <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">{{ $question['options'][$answerValue - 1] }}</p>
                                        @endif
                                    @else
                                        <div class="p-4 rounded-lg" style="background: rgba(255,255,255,0.05);">
                                            <p class="text-sm" style="color: rgba(235,235,245,0.8);">{{ $answerValue }}</p>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm italic" style="color: rgba(235,235,245,0.4);">No answer provided</p>
                                @endif
                            </div>

                            {{-- Score Input --}}
                            <div class="pl-13">
                                <label class="block mb-2">
                                    <span class="text-sm font-medium text-white">Score (0 - {{ $maxPoints }} points) <span class="text-red-500">*</span></span>
                                </label>
                                <div class="flex items-center gap-4">
                                    <input 
                                        type="number" 
                                        name="manual_scores[{{ $index }}]" 
                                        min="0" 
                                        max="{{ $maxPoints }}" 
                                        step="0.5"
                                        class="form-control w-32 bg-dark-secondary border-dark-border text-white focus:border-apple-blue focus:ring-apple-blue"
                                        placeholder="0"
                                        required
                                    >
                                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">out of {{ $maxPoints }} points</span>
                                </div>
                                @error('manual_scores.' . $index)
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
        @endif

        {{-- Evaluator Notes --}}
        <section class="card-elevated rounded-apple-xl p-5 md:p-6">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-comment-dots" style="color: rgba(191,90,242,1);"></i>
                Evaluator Notes (Optional)
            </h2>
            
            <div>
                <label class="block mb-2">
                    <span class="text-sm font-medium text-white">Overall feedback or comments</span>
                </label>
                <textarea 
                    name="evaluator_notes" 
                    rows="5" 
                    class="form-control w-full bg-dark-secondary border-dark-border text-white focus:border-apple-blue focus:ring-apple-blue"
                    placeholder="Add any overall comments about the candidate's performance..."
                >{{ old('evaluator_notes') }}</textarea>
                @error('evaluator_notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- Submit Button --}}
        @if(($session->testTemplate->isDocumentEditingTest() && $session->testTemplate->evaluation_criteria) || count($subjectiveQuestions) > 0)
        <section class="card-elevated rounded-apple-xl p-5 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-semibold mb-1">Ready to submit evaluation?</p>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                        @if($session->testTemplate->isDocumentEditingTest())
                            Final score will be calculated based on the evaluation criteria.
                        @else
                            Final score will be calculated by combining auto-graded and manual scores.
                        @endif
                    </p>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check mr-2"></i>Submit Evaluation
                </button>
            </div>
        </section>
        @endif
    </form>
</div>

<style>
.form-control {
    display: block;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.5rem;
    border: 1px solid;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(10, 132, 255, 0.25);
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
}
</style>
@endsection
