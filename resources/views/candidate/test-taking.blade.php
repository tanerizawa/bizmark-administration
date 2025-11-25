<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $testSession->testTemplate->title }}</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS Browser Build -->
    <script src="{{ asset('js/tailwind-browser.js') }}" type="module"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        html {
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #000000;
            overflow-x: hidden;
        }
        
        /* Apple Design System Classes */
        .card-elevated {
            background: rgba(28,28,30,0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .rounded-apple-lg { border-radius: 12px; }
        .rounded-apple-xl { border-radius: 16px; }
        .rounded-apple-2xl { border-radius: 24px; }
        
        .btn-primary {
            background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%);
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(10, 132, 255, 0.5);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover:not(:disabled) {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.3);
        }
        
        .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Hide scrollbar for dots navigation */
        .flex.gap-1\.5.overflow-x-auto::-webkit-scrollbar {
            display: none;
        }
        
        /* Custom scrollbar for review modal */
        #reviewContent::-webkit-scrollbar {
            width: 8px;
        }
        
        #reviewContent::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
        }
        
        #reviewContent::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        
        #reviewContent::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .input-apple {
            background: rgba(255,255,255,0.05);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-apple:focus {
            outline: none;
            background: rgba(255,255,255,0.08);
            border-color: #0A84FF;
            box-shadow: 0 0 0 4px rgba(10, 132, 255, 0.15);
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- Welcome Screen (Intro) -->
<div id="welcomeScreen" class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-3xl w-full fade-in">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-apple-2xl mb-6" style="background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%);">
                <i class="fas fa-clipboard-list text-5xl text-white"></i>
            </div>
            <h1 class="text-5xl font-bold text-white mb-3">{{ $testSession->testTemplate->title }}</h1>
            <p class="text-xl text-white/60">{{ $testSession->jobApplication?->jobVacancy?->title ?? 'Position' }}</p>
        </div>

        <!-- Info Card -->
        <div class="card-elevated rounded-apple-xl p-8 mb-6 slide-up">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-400"></i>
                Informasi Test
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                    <i class="fas fa-list-ol text-3xl text-blue-400 mb-3"></i>
                    <div class="text-3xl font-bold text-white mb-1">{{ count($questions) }}</div>
                    <div class="text-sm text-white/60">Total Soal</div>
                </div>
                
                <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                    <i class="fas fa-clock text-3xl text-orange-400 mb-3"></i>
                    <div class="text-3xl font-bold text-white mb-1">{{ $testSession->testTemplate->duration_minutes }}</div>
                    <div class="text-sm text-white/60">Menit</div>
                </div>
                
                <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                    <i class="fas fa-star text-3xl text-yellow-400 mb-3"></i>
                    <div class="text-3xl font-bold text-white mb-1">{{ $testSession->testTemplate->passing_score }}%</div>
                    <div class="text-sm text-white/60">Nilai Lulus</div>
                </div>
            </div>

            <!-- Description -->
            @if($testSession->testTemplate->description)
            <div class="mb-8 p-6 rounded-apple-lg" style="background: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-400"></i>
                    Deskripsi
                </h3>
                <p class="text-white/80 leading-relaxed">{{ $testSession->testTemplate->description }}</p>
            </div>
            @endif

            <!-- Rules -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-orange-400"></i>
                    Ketentuan dan Peraturan
                </h3>
                
                <div class="space-y-3">
                    <div class="flex gap-3 items-start p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                        <div class="text-white/80">Pastikan koneksi internet Anda stabil selama mengerjakan test</div>
                    </div>
                    <div class="flex gap-3 items-start p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                        <div class="text-white/80">Jawaban akan tersimpan otomatis setiap kali Anda berpindah soal</div>
                    </div>
                    <div class="flex gap-3 items-start p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-check-circle text-green-400 mt-1"></i>
                        <div class="text-white/80">Anda dapat menandai soal yang ingin direview nanti sebelum submit</div>
                    </div>
                    <div class="flex gap-3 items-start p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-times-circle text-red-400 mt-1"></i>
                        <div class="text-white/80"><strong>Jangan berpindah tab atau minimize browser</strong> - sistem akan mencatat sebagai pelanggaran</div>
                    </div>
                    <div class="flex gap-3 items-start p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-times-circle text-red-400 mt-1"></i>
                        <div class="text-white/80"><strong>Timer tidak dapat dihentikan</strong> setelah test dimulai</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Button -->
        <div class="text-center">
            <button onclick="startTest()" class="btn-primary">
                <i class="fas fa-play-circle"></i>
                Mulai Test Sekarang
            </button>
            <p class="text-sm text-white/40 mt-4">Test akan dimulai setelah Anda klik tombol di atas</p>
        </div>
    </div>
</div>

<!-- Test Interface (Hidden initially) -->
<div id="testInterface" class="min-h-screen" style="display: none;">
    <!-- Top Bar (Fixed) -->
    <div class="fixed top-0 left-0 right-0 z-50" style="background: rgba(28,28,30,0.95); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Test Info -->
                <div>
                    <h1 class="text-lg font-bold text-white">{{ $testSession->testTemplate->title }}</h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $testSession->jobApplication->full_name }}</p>
                </div>

                <!-- Timer & Progress -->
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Progress</div>
                        <div class="text-lg font-bold text-white">
                            <span id="answeredCount">0</span> / {{ $testSession->testTemplate->total_questions }}
                        </div>
                    </div>

                    <div class="text-center pl-6" style="border-left: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Time Left</div>
                        <div id="timer" class="text-2xl font-bold" style="color: #FF9500;">
                            {{ $testSession->testTemplate->duration_minutes }}:00
                        </div>
                    </div>

                    <!-- Full Screen Toggle -->
                    <button type="button" id="fullscreenBtn" 
                            class="p-2 rounded-lg transition-colors"
                            style="background: rgba(255,255,255,0.1);"
                            onmouseover="this.style.background='rgba(255,255,255,0.15)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'"
                            title="Toggle Fullscreen">
                        <i class="fas fa-expand text-white"></i>
                    </button>
                </div>
            </div>

            <!-- Warning Banner (if tab switched) -->
            <div id="warningBanner" class="hidden mt-4 rounded-apple-lg p-3" style="background: rgba(255,69,58,0.15); border-left: 4px solid #FF453A;">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2" style="color: #FF453A;"></i>
                    <span class="font-medium" style="color: #FF453A;">
                        Warning: Tab switching detected (<span id="tabSwitchCount">0</span> times). Multiple violations may result in disqualification.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-32 pb-20">
        <div class="max-w-4xl mx-auto px-4">
            <form id="testForm" action="{{ route('candidate.test.complete', $testSession->session_token) }}" method="POST">
                @csrf

                <!-- Questions -->
                <div id="questionsContainer">
                    @foreach($questions as $index => $question)
                    <div class="question-card card-elevated rounded-apple-xl {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}" style="min-height: 400px;">
                        <!-- Question Header -->
                        <div class="p-6 flex justify-between items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%);">
                                    <span class="text-xl font-bold text-white">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="text-sm" style="color: rgba(235,235,245,0.6);">Question {{ $index + 1 }} of {{ count($questions) }}</div>
                                    @if(isset($question['points']))
                                    <div class="text-xs mt-0.5" style="color: rgba(255,159,10,0.8);">
                                        <i class="fas fa-star"></i> {{ $question['points'] }} points
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="mark-review-btn p-3 rounded-lg transition-all" style="background: rgba(255,255,255,0.05);" data-question="{{ $index }}"
                                    title="Mark for review">
                                <i class="far fa-bookmark text-lg" style="color: rgba(235,235,245,0.4);"></i>
                            </button>
                        </div>

                        <!-- Question Text -->
                        <div class="p-8">
                            <div class="mb-8">
                                <h3 class="text-2xl text-white leading-relaxed font-medium">{{ $question['question_text'] }}</h3>
                                @if(isset($question['category']))
                                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs" style="background: rgba(10,132,255,0.1); color: #0A84FF;">
                                    <i class="fas fa-tag"></i>
                                    {{ $question['category'] }}
                                </div>
                                @endif
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-3">
                                @if($question['question_type'] === 'multiple-choice')
                                    <div class="text-sm font-medium mb-4" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-hand-pointer mr-2"></i>Select one answer:
                                    </div>
                                    @if(isset($question['options']) && is_array($question['options']))
                                    @foreach($question['options'] as $optIndex => $option)
                                    <label class="group block cursor-pointer">
                                        <div class="flex items-center gap-4 p-5 rounded-xl transition-all answer-option" 
                                             style="background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.1);">
                                            <div class="flex-shrink-0">
                                                <input type="radio" name="answers[{{ $index }}]" value="{{ $optIndex }}" 
                                                       class="w-5 h-5" style="accent-color: #0A84FF;">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold transition-all option-letter" 
                                                          style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.6);">
                                                        {{ chr(65 + $optIndex) }}
                                                    </span>
                                                    <span class="text-base text-white leading-relaxed">{{ $option }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                    @endif

                                @elseif($question['question_type'] === 'true-false')
                                    <div class="text-sm font-medium mb-4" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-hand-pointer mr-2"></i>Select True or False:
                                    </div>
                                    <label class="group block cursor-pointer">
                                        <div class="flex items-center gap-4 p-6 rounded-xl transition-all answer-option" 
                                             style="background: rgba(52,199,89,0.05); border: 2px solid rgba(52,199,89,0.2);">
                                            <div class="flex-shrink-0">
                                                <input type="radio" name="answers[{{ $index }}]" value="true" 
                                                       class="w-5 h-5" style="accent-color: #34C759;">
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                                                    <i class="fas fa-check text-xl" style="color: #34C759;"></i>
                                                </div>
                                                <span class="text-xl font-semibold text-white">True</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="group block cursor-pointer">
                                        <div class="flex items-center gap-4 p-6 rounded-xl transition-all answer-option" 
                                             style="background: rgba(255,69,58,0.05); border: 2px solid rgba(255,69,58,0.2);">
                                            <div class="flex-shrink-0">
                                                <input type="radio" name="answers[{{ $index }}]" value="false" 
                                                       class="w-5 h-5" style="accent-color: #FF453A;">
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(255,69,58,0.2);">
                                                    <i class="fas fa-times text-xl" style="color: #FF453A;"></i>
                                                </div>
                                                <span class="text-xl font-semibold text-white">False</span>
                                            </div>
                                        </div>
                                    </label>

                                @elseif($question['question_type'] === 'essay')
                                    <div class="text-sm font-medium mb-4" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-pen mr-2"></i>Write your answer below:
                                    </div>
                                    <div class="relative">
                                        <textarea name="answers[{{ $index }}]" rows="10"
                                                  class="input-apple w-full resize-none"
                                                  placeholder="Type your detailed answer here..."
                                                  style="font-size: 1rem; line-height: 1.6;"></textarea>
                                        <div class="absolute bottom-3 right-3 text-xs" style="color: rgba(235,235,245,0.4);">
                                            <span id="charCount-{{ $index }}">0</span> characters
                                        </div>
                                    </div>

                                @elseif($question['question_type'] === 'rating')
                                    <div class="text-sm font-medium mb-6 text-center" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-star mr-2"></i>Rate from 1 (Poor) to 5 (Excellent):
                                    </div>
                                    <div class="flex items-center justify-center gap-6 py-8">
                                        @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center cursor-pointer rating-option transition-transform hover:scale-110">
                                            <input type="radio" name="answers[{{ $index }}]" value="{{ $i }}" class="hidden">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold transition-all rating-circle"
                                                 style="background: rgba(255,255,255,0.05); border: 3px solid rgba(255,255,255,0.2); color: rgba(235,235,245,0.6);">
                                                {{ $i }}
                                            </div>
                                            @if($i === 1)
                                            <span class="text-xs mt-3 font-medium" style="color: rgba(255,69,58,0.8);">Poor</span>
                                            @elseif($i === 2)
                                            <span class="text-xs mt-3 font-medium" style="color: rgba(255,159,10,0.8);">Fair</span>
                                            @elseif($i === 3)
                                            <span class="text-xs mt-3 font-medium" style="color: rgba(255,214,10,0.8);">Good</span>
                                            @elseif($i === 4)
                                            <span class="text-xs mt-3 font-medium" style="color: rgba(50,215,75,0.8);">Very Good</span>
                                            @elseif($i === 5)
                                            <span class="text-xs mt-3 font-medium" style="color: rgba(52,199,89,0.8);">Excellent</span>
                                            @endif
                                        </label>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="p-6 flex justify-between items-center" style="border-top: 1px solid rgba(255,255,255,0.1);">
                            <button type="button" class="prev-btn btn-secondary {{ $index === 0 ? 'invisible' : '' }}">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>

                            @if($index < count($questions) - 1)
                            <button type="button" class="next-btn btn-primary">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            @else
                            <button type="button" id="reviewBtn" class="btn-primary" style="background: linear-gradient(135deg, #34C759 0%, #30D158 100%);">
                                <i class="fas fa-check-circle mr-2"></i> Review & Submit
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Question Navigation (Bottom Bar - Simplified) -->
                <div class="fixed bottom-0 left-0 right-0 z-50" style="background: rgba(28,28,30,0.98); backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.1); box-shadow: 0 -4px 20px rgba(0,0,0,0.3);">
                    <div class="max-w-7xl mx-auto px-6 py-3">
                        <div class="flex items-center justify-between gap-8">
                            <!-- Progress Indicator -->
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full" style="background: linear-gradient(135deg, #34C759 0%, #30D158 100%);"></div>
                                    <span class="text-sm text-white/80 font-medium"><span id="bottomAnsweredCount">0</span></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full" style="background: linear-gradient(135deg, #FFD60A 0%, #FF9500 100%);"></div>
                                    <span class="text-sm text-white/80 font-medium"><span id="bottomMarkedCount">0</span></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white/30"></div>
                                    <span class="text-sm text-white/80 font-medium"><span id="bottomUnansweredCount">{{ count($questions) }}</span></span>
                                </div>
                            </div>
                            
                            <!-- Mini Navigation Dots -->
                            <div class="flex-1 flex justify-center">
                                <div class="flex gap-1.5 overflow-x-auto max-w-2xl" style="scrollbar-width: none; -ms-overflow-style: none;">
                                    @foreach($questions as $index => $question)
                                    <button type="button" 
                                            class="nav-btn w-2 h-2 rounded-full transition-all flex-shrink-0 {{ $index === 0 ? 'active' : '' }}" 
                                            data-question="{{ $index }}"
                                            style="background: rgba(255,255,255,0.3);"
                                            title="Question {{ $index + 1 }}">
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Question Counter -->
                            <div class="text-sm font-semibold text-white/80">
                                <span id="currentQuestionNumber">1</span> / {{ count($questions) }}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.85); backdrop-filter: blur(10px);">
    <div class="card-elevated rounded-apple-2xl max-w-4xl w-full flex flex-col" style="max-height: 90vh;">
        <!-- Header -->
        <div class="p-6 flex-shrink-0" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Review Your Answers</h2>
                    <p class="mt-1" style="color: rgba(235,235,245,0.6);">Check your answers before final submission</p>
                </div>
                <button type="button" id="closeReviewTop" onclick="closeReview()" class="p-2 rounded-lg transition-all hover:bg-white/10">
                    <i class="fas fa-times text-xl text-white/60"></i>
                </button>
            </div>
        </div>

        <!-- Content (Scrollable) -->
        <div class="p-6 overflow-y-auto flex-1" id="reviewContent" style="scrollbar-width: thin;">
            <!-- Populated by JavaScript -->
        </div>

        <!-- Footer -->
        <div class="p-6 flex gap-3 flex-shrink-0" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <button type="button" id="closeReview" class="btn-secondary flex-1">
                <i class="fas fa-arrow-left mr-2"></i> Back to Test
            </button>
            <button type="button" id="submitTest" class="btn-primary flex-1" style="background: linear-gradient(135deg, #34C759 0%, #30D158 100%);">
                <i class="fas fa-paper-plane mr-2"></i> Submit Test
            </button>
        </div>
    </div>
</div>

<style>
/* Rating Option Hover Effects */
.rating-option input:checked + .rating-circle {
    background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%) !important;
    border-color: #0A84FF !important;
    color: white !important;
    box-shadow: 0 0 20px rgba(10, 132, 255, 0.5);
}

/* Answer Option Selected State */
.answer-option:has(input:checked) {
    background: rgba(10, 132, 255, 0.15) !important;
    border-color: #0A84FF !important;
    box-shadow: 0 0 0 4px rgba(10, 132, 255, 0.1);
}

.answer-option:has(input:checked) .option-letter {
    background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%) !important;
    color: white !important;
}

.answer-option:hover {
    background: rgba(255,255,255,0.08) !important;
    border-color: rgba(255,255,255,0.3) !important;
}

/* Mark Review Button States */
.mark-review-btn.marked i {
    color: #FFD60A !important;
}

.mark-review-btn:hover {
    background: rgba(255,255,255,0.1) !important;
}

.mark-review-btn:hover i {
    color: #FFD60A !important;
}

/* Character Counter for Essay */
textarea:focus + .char-counter {
    color: #0A84FF !important;
}

/* Navigation Dots States */
.nav-btn {
    position: relative;
    cursor: pointer;
    border: none;
}

.nav-btn.active {
    background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%) !important;
    width: 24px !important;
    box-shadow: 0 0 10px rgba(10, 132, 255, 0.6);
}

.nav-btn.answered {
    background: linear-gradient(135deg, #34C759 0%, #30D158 100%) !important;
    box-shadow: 0 0 8px rgba(52, 199, 89, 0.4);
}

.nav-btn.marked {
    background: linear-gradient(135deg, #FFD60A 0%, #FF9500 100%) !important;
    box-shadow: 0 0 8px rgba(255, 214, 10, 0.4);
}

.nav-btn:hover {
    transform: scale(1.3);
}
</style>

/* Nav Button Active State */
.nav-btn.answered {
    background: linear-gradient(135deg, #34C759 0%, #30D158 100%) !important;
    box-shadow: 0 0 8px rgba(52, 199, 89, 0.4);
}

.nav-btn.marked {
    background: linear-gradient(135deg, #FFD60A 0%, #FF9500 100%) !important;
    box-shadow: 0 0 8px rgba(255, 214, 10, 0.4);
}

.nav-btn.active {
    background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%) !important;
    width: 24px !important;
    box-shadow: 0 0 10px rgba(10, 132, 255, 0.6);
}

/* Smooth Transitions */
* {
    transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
}
</style>

<script>
// Global variables
let currentQuestion = 0;
let tabSwitchCount = 0;
let timeRemaining = {{ $remainingMinutes * 60 }}; // in seconds from server
let timerInterval;
let markedForReview = [];
let autoSaveInterval;
let testStarted = {{ $testSession->status === 'in-progress' ? 'true' : 'false' }};
let isSubmitting = false; // Flag untuk prevent beforeunload saat submit

// Start Test Function
function startTest() {
    // Call backend to mark test as started
    fetch('{{ route("candidate.test.start", $testSession->session_token) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            // Hide welcome screen
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('testInterface').style.display = 'block';
            
            // Mark as started
            testStarted = true;
            localStorage.setItem('test_started_{{ $testSession->session_token }}', 'true');
            localStorage.setItem('test_start_time_{{ $testSession->session_token }}', Date.now());
            
            // Initialize test
            initTest();
            
            // Start timer
            startTimer();
            
            // Request fullscreen
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen request failed:', err);
            });
        }
    }).catch(err => {
        console.error('Failed to start test:', err);
        alert('Failed to start test. Please refresh the page and try again.');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Check if test already started
    if (testStarted) {
        // Sync time from server on page load
        syncTimeFromServer().then(() => {
            // Hide welcome screen, show test interface directly
            document.getElementById('welcomeScreen').style.display = 'none';
            document.getElementById('testInterface').style.display = 'block';
            
            // Initialize
            initTest();
            startTimer();
        });
    }
    
    // Don't start timer automatically, wait for user to click "Start Test"
    setupNavigation();
    setupAnswerTracking();
    setupFullscreen();
});

// Sync remaining time from server (untuk handle refresh)
function syncTimeFromServer() {
    return fetch('{{ route("candidate.test.time", $testSession->session_token) }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.remaining_minutes !== undefined) {
            timeRemaining = data.remaining_minutes * 60; // convert to seconds
            console.log('Time synced from server:', timeRemaining, 'seconds');
        }
        
        // Check if expired
        if (data.status === 'expired' || data.remaining_minutes <= 0) {
            autoSubmitTest();
        }
    })
    .catch(err => {
        console.error('Failed to sync time:', err);
    });
}

function initTest() {
    showQuestion(0);
    setupAntiCheat();
    setupAutoSave();
    setupReviewModal();
}

function setupReviewModal() {
    const reviewBtn = document.getElementById('reviewBtn');
    const closeReviewBtn = document.getElementById('closeReview');
    const closeReviewTopBtn = document.getElementById('closeReviewTop');
    const submitBtn = document.getElementById('submitTest');
    
    if (reviewBtn) {
        reviewBtn.addEventListener('click', showReview);
    }
    
    if (closeReviewBtn) {
        closeReviewBtn.addEventListener('click', closeReview);
    }
    
    if (closeReviewTopBtn) {
        closeReviewTopBtn.addEventListener('click', closeReview);
    }
    
    if (submitBtn) {
        submitBtn.addEventListener('click', submitTest);
    }
}

function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(function() {
        timeRemaining--;
        updateTimerDisplay();

        if (timeRemaining <= 300) { // 5 minutes warning
            document.getElementById('timer').classList.add('text-red-600', 'dark:text-red-400', 'animate-pulse');
        }

        if (timeRemaining <= 0) {
            autoSubmitTest();
        }
    }, 1000);
    
    // Sync time from server every 30 seconds to prevent drift
    setInterval(function() {
        syncTimeFromServer();
    }, 30000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timer').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function setupAntiCheat() {
    // Tab visibility change detection
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            tabSwitchCount++;
            document.getElementById('tabSwitchCount').textContent = tabSwitchCount;
            document.getElementById('warningBanner').classList.remove('hidden');
            
            // Send to server
            fetch('{{ route("candidate.test.track-tab", $testSession->session_token) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: 'tab_switch',
                    count: tabSwitchCount
                })
            });

            if (tabSwitchCount >= 5) {
                alert('Multiple tab switches detected. Your test may be flagged for review.');
            }
        }
    });

    // Prevent right-click
    document.addEventListener('contextmenu', e => e.preventDefault());

    // Detect copy attempts
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        alert('Copying is not allowed during the test.');
    });
}

function setupAutoSave() {
    autoSaveInterval = setInterval(function() {
        saveAnswers();
    }, 30000); // Every 30 seconds
}

function saveAnswers() {
    const formData = new FormData(document.getElementById('testForm'));
    
    fetch('{{ route("candidate.test.answer", $testSession->session_token) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
}

function setupNavigation() {
    // Next buttons
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentQuestion < {{ count($questions) - 1 }}) {
                showQuestion(currentQuestion + 1);
            }
        });
    });

    // Previous buttons
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        });
    });

    // Navigation buttons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionIndex = parseInt(this.dataset.question);
            showQuestion(questionIndex);
        });
    });

    // Mark for review
    document.querySelectorAll('.mark-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionIndex = parseInt(this.dataset.question);
            const navBtn = document.querySelector(`.nav-btn[data-question="${questionIndex}"]`);
            const icon = this.querySelector('i');
            
            if (markedForReview.includes(questionIndex)) {
                // Unmark
                markedForReview = markedForReview.filter(q => q !== questionIndex);
                this.classList.remove('marked');
                icon.classList.remove('fas');
                icon.classList.add('far');
                navBtn.classList.remove('marked');
            } else {
                // Mark
                markedForReview.push(questionIndex);
                this.classList.add('marked');
                icon.classList.remove('far');
                icon.classList.add('fas');
                navBtn.classList.add('marked');
            }
            updateProgress(); // Update counters
        });
    });
}

function showQuestion(index) {
    document.querySelectorAll('.question-card').forEach(card => card.classList.add('hidden'));
    document.querySelector(`.question-card[data-question="${index}"]`).classList.remove('hidden');
    
    // Update nav dots
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.width = '8px';
    });
    
    const activeBtn = document.querySelector(`.nav-btn[data-question="${index}"]`);
    activeBtn.classList.add('active');
    activeBtn.style.width = '24px';
    
    // Update current question number
    document.getElementById('currentQuestionNumber').textContent = index + 1;
    
    currentQuestion = index;
    updateProgress();
    
    // Smooth scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function setupAnswerTracking() {
    // Radio buttons and textarea tracking
    document.querySelectorAll('input[type="radio"], textarea').forEach(input => {
        input.addEventListener('change', function() {
            const card = this.closest('.question-card');
            const questionIndex = parseInt(card.dataset.question);
            const navBtn = document.querySelector(`.nav-btn[data-question="${questionIndex}"]`);
            
            if (!navBtn.classList.contains('marked')) {
                navBtn.classList.add('answered');
            }
            updateProgress();
        });
    });
    
    // Character counter for essay questions
    document.querySelectorAll('textarea').forEach(textarea => {
        const card = textarea.closest('.question-card');
        const questionIndex = card.dataset.question;
        const counter = document.getElementById(`charCount-${questionIndex}`);
        
        if (counter) {
            textarea.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }
    });
}

function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked, textarea:not(:empty)').length;
    const marked = markedForReview.length;
    const unanswered = {{ count($questions) }} - answered;
    
    // Update top bar
    document.getElementById('answeredCount').textContent = answered;
    
    // Update bottom bar
    document.getElementById('bottomAnsweredCount').textContent = answered;
    document.getElementById('bottomMarkedCount').textContent = marked;
    document.getElementById('bottomUnansweredCount').textContent = unanswered;
}

function setupFullscreen() {
    const btn = document.getElementById('fullscreenBtn');
    btn.addEventListener('click', toggleFullscreen);
    
    // Request fullscreen on start (removed automatic request)
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function showReview() {
    const reviewContent = document.getElementById('reviewContent');
    let html = '<div class="space-y-4">';
    
    const questions = @json($questions);
    
    document.querySelectorAll('.question-card').forEach((card, index) => {
        const question = questions[index];
        const questionText = question.question_text;
        const questionType = question.question_type;
        
        let answerText = 'Not answered';
        let isAnswered = false;
        
        if (questionType === 'multiple-choice') {
            const selectedOption = card.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                const optionIndex = parseInt(selectedOption.value);
                answerText = question.options[optionIndex];
                isAnswered = true;
            }
        } else if (questionType === 'true-false') {
            const selectedOption = card.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                answerText = selectedOption.value === 'true' ? 'True' : 'False';
                isAnswered = true;
            }
        } else if (questionType === 'essay') {
            const textarea = card.querySelector('textarea');
            if (textarea && textarea.value.trim()) {
                answerText = textarea.value.substring(0, 100) + (textarea.value.length > 100 ? '...' : '');
                isAnswered = true;
            }
        } else if (questionType === 'rating') {
            const selectedRating = card.querySelector('input[type="radio"]:checked');
            if (selectedRating) {
                answerText = `Rating: ${selectedRating.value} / 5`;
                isAnswered = true;
            }
        }
        
        const isMarked = markedForReview.includes(index);
        
        html += `
            <div class="p-5 rounded-apple-lg transition-all" style="background: rgba(255,255,255,0.05); border: 2px solid ${isAnswered ? 'rgba(52,199,89,0.3)' : 'rgba(255,69,58,0.3)'};">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold" style="background: rgba(10,132,255,0.2); color: #0A84FF;">
                            Q${index + 1}
                        </span>
                        ${isMarked ? '<i class="fas fa-bookmark" style="color: #FFD60A;"></i>' : ''}
                    </div>
                    <span class="text-sm font-medium px-3 py-1 rounded-full" style="background: ${isAnswered ? 'rgba(52,199,89,0.2)' : 'rgba(255,69,58,0.2)'}; color: ${isAnswered ? '#34C759' : '#FF453A'};">
                        ${isAnswered ? '✓ Answered' : '✗ Unanswered'}
                    </span>
                </div>
                <p class="text-white mb-3 leading-relaxed">${questionText}</p>
                <div class="p-3 rounded-lg" style="background: rgba(255,255,255,0.03);">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Your answer:</span>
                    <p class="text-white mt-1">${answerText}</p>
                </div>
                ${!isAnswered ? '<button type="button" class="mt-3 text-sm text-blue-400 hover:text-blue-300" onclick="closeReview(); showQuestion(' + index + ');">Go to question →</button>' : ''}
            </div>
        `;
    });
    
    html += '</div>';
    
    // Summary
    const totalQuestions = {{ count($questions) }};
    const answeredCount = document.querySelectorAll('input[type="radio"]:checked, textarea:not(:placeholder-shown)').length;
    const unansweredCount = totalQuestions - answeredCount;
    
    html = `
        <div class="mb-6 p-5 rounded-apple-xl" style="background: linear-gradient(135deg, rgba(10,132,255,0.1) 0%, rgba(0,81,213,0.1) 100%); border: 1px solid rgba(10,132,255,0.3);">
            <h3 class="text-xl font-bold text-white mb-4">Summary</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">${totalQuestions}</div>
                    <div class="text-sm" style="color: rgba(235,235,245,0.6);">Total Questions</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: #34C759;">${answeredCount}</div>
                    <div class="text-sm" style="color: rgba(235,235,245,0.6);">Answered</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold" style="color: #FF453A;">${unansweredCount}</div>
                    <div class="text-sm" style="color: rgba(235,235,245,0.6);">Unanswered</div>
                </div>
            </div>
            ${unansweredCount > 0 ? '<p class="mt-4 text-sm text-center" style="color: #FF9500;"><i class="fas fa-exclamation-triangle mr-2"></i>You have unanswered questions. Review them before submitting.</p>' : ''}
        </div>
    ` + html;
    
    reviewContent.innerHTML = html;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReview() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function submitTest() {
    const totalQuestions = {{ count($questions) }};
    const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length + 
                          Array.from(document.querySelectorAll('textarea')).filter(t => t.value.trim()).length;
    const unansweredCount = totalQuestions - answeredCount;
    
    let confirmMessage = 'Are you sure you want to submit your test?\n\n';
    confirmMessage += `✓ Answered: ${answeredCount}\n`;
    confirmMessage += `✗ Unanswered: ${unansweredCount}\n\n`;
    confirmMessage += 'You cannot change your answers after submission.';
    
    if (confirm(confirmMessage)) {
        // Set submitting flag to prevent beforeunload warning
        isSubmitting = true;
        
        // Close modal
        closeReview();
        
        // Show loading overlay
        showLoadingOverlay('Submitting your test...');
        
        // Save answers first
        saveAnswers();
        
        // Clear intervals
        if (timerInterval) clearInterval(timerInterval);
        if (autoSaveInterval) clearInterval(autoSaveInterval);
        
        // Submit form after short delay
        setTimeout(() => {
            document.getElementById('testForm').submit();
        }, 1000);
    }
}

function autoSubmitTest() {
    // Set submitting flag
    isSubmitting = true;
    
    // Show alert
    alert('⏰ Time is up! Your test will be automatically submitted.');
    
    // Show loading overlay
    showLoadingOverlay('Time is up! Submitting your test...');
    
    // Save answers first
    saveAnswers();
    
    // Clear intervals
    if (timerInterval) clearInterval(timerInterval);
    if (autoSaveInterval) clearInterval(autoSaveInterval);
    
    // Submit form
    setTimeout(() => {
        document.getElementById('testForm').submit();
    }, 1000);
}

function showLoadingOverlay(message) {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    `;
    
    overlay.innerHTML = `
        <div style="width: 60px; height: 60px; border: 4px solid rgba(10,132,255,0.3); border-top-color: #0A84FF; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="color: white; font-size: 1.25rem; font-weight: 600;">${message}</p>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.875rem;">Please wait, do not close this page</p>
    `;
    
    document.body.appendChild(overlay);
    
    // Add spin animation
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
}

// Prevent form resubmission and accidental page leave
window.addEventListener('beforeunload', function(e) {
    // If submitting, allow page leave without warning
    if (isSubmitting) {
        return undefined;
    }
    
    // If test is active, warn user
    if (testStarted && !isSubmitting) {
        saveAnswers();
        e.preventDefault();
        e.returnValue = 'Your test is still in progress. Are you sure you want to leave?';
        return e.returnValue;
    }
});
</script>

</body>
</html>