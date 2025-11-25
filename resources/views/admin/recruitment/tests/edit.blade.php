@extends('layouts.app')

@section('title', 'Edit Test Template - ' . $test->title)

@section('content')
<div class="recruitment-shell max-w-6xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-44 h-44 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                    <a href="{{ route('admin.recruitment.tests.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Test Management
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <a href="{{ route('admin.recruitment.tests.show', $test) }}" class="hover:text-white transition-apple">
                        {{ Str::limit($test->title, 30) }}
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Edit</span>
                </div>
                <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">Edit Template Tes</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Perbarui informasi dan pertanyaan template tes.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.tests.show', $test) }}" class="btn-secondary-sm">
                    <i class="fas fa-eye mr-2"></i>Lihat Template
                </a>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="card-elevated rounded-apple-xl p-4" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg" style="color: rgba(52,199,89,0.9);"></i>
                <div>
                    <p class="text-sm font-semibold" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
                    @if($test->test_type === 'document-editing' && $test->template_file_path)
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.7);">
                            File template: <strong>{{ basename($test->template_file_path) }}</strong>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="card-elevated rounded-apple-xl p-4" style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.3);">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-lg" style="color: rgba(255,69,58,0.9);"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold" style="color: rgba(255,69,58,1);">Terdapat kesalahan:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs" style="color: rgba(235,235,245,0.7);">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.recruitment.tests.update', $test) }}" method="POST" enctype="multipart/form-data" id="testForm" class="space-y-4">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Basic Information -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Informasi Dasar</p>
                        <h2 class="text-base font-semibold text-white">Detail Template</h2>
                    </div>
                    
                    <div class="space-y-3 text-sm" style="color: rgba(235,235,245,0.85);">
                        <div class="space-y-1">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Judul Tes <span class="text-apple-red">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $test->title) }}" required
                                   class="w-full"
                                   placeholder="Contoh: Technical Assessment Senior Dev">
                            @error('title')
                                <p class="text-xs text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                      class="w-full"
                                      placeholder="Ringkasan tujuan penilaian">{{ old('description', $test->description) }}</textarea>
                            @error('description')
                                <p class="text-xs text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                    Tipe Tes <span class="text-apple-red">*</span>
                                </label>
                                <select name="test_type" id="test_type" required class="w-full">
                                    <option value="">Pilih tipe...</option>
                                    <option value="psychology" {{ old('test_type', $test->test_type) == 'psychology' ? 'selected' : '' }}>Psikologi</option>
                                    <option value="psychometric" {{ old('test_type', $test->test_type) == 'psychometric' ? 'selected' : '' }}>Psikometrik</option>
                                    <option value="technical" {{ old('test_type', $test->test_type) == 'technical' ? 'selected' : '' }}>Teknis</option>
                                    <option value="aptitude" {{ old('test_type', $test->test_type) == 'aptitude' ? 'selected' : '' }}>Aptitude</option>
                                    <option value="personality" {{ old('test_type', $test->test_type) == 'personality' ? 'selected' : '' }}>Kepribadian</option>
                                    <option value="document-editing" {{ old('test_type', $test->test_type) == 'document-editing' ? 'selected' : '' }}>Document Editing</option>
                                </select>
                                @error('test_type')
                                    <p class="text-xs text-apple-red">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                    Durasi (menit) <span class="text-apple-red">*</span>
                                </label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $test->duration_minutes) }}" required min="5" max="480"
                                       class="w-full">
                                @error('duration_minutes')
                                    <p class="text-xs text-apple-red">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Nilai Lulus (%) <span class="text-apple-red">*</span>
                            </label>
                            <input type="number" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}" required min="0" max="100" step="0.01"
                                   class="w-full">
                            @error('passing_score')
                                <p class="text-xs text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Instruksi Tes
                            </label>
                            <textarea name="instructions" 
                                      rows="8" 
                                      class="w-full text-sm font-mono"
                                      placeholder="Instruksi yang akan dibaca kandidat sebelum memulai tes...">{{ old('instructions', $test->instructions) }}</textarea>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                Gunakan format plain text dengan line breaks. Simbol seperti ▸, [ ], •, ▪ akan ditampilkan dengan styling otomatis.
                            </p>
                            @error('instructions')
                                <p class="text-xs text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-4" id="questionsSection" style="display: {{ $test->test_type === 'document-editing' ? 'none' : 'block' }};">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Soal Tes</p>
                            <h2 class="text-base font-semibold text-white">Daftar Pertanyaan</h2>
                        </div>
                        <button type="button" onclick="addQuestion()" class="btn-primary-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Soal
                        </button>
                    </div>

                    <div id="questions-container" class="space-y-3">
                        @if(old('questions'))
                            @foreach(old('questions') as $index => $question)
                                <div class="question-item card-nested p-3 space-y-2" data-index="{{ $index }}">
                                    <div class="flex justify-between items-start gap-2">
                                        <p class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">SOAL #<span class="question-number">{{ $index + 1 }}</span></p>
                                        <button type="button" onclick="removeQuestion(this)" class="text-apple-red hover:text-red-400 text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-2">
                                        <textarea name="questions[{{ $index }}][question_text]" required
                                                  class="w-full text-sm" rows="2"
                                                  placeholder="Tulis pertanyaan di sini...">{{ $question['question_text'] }}</textarea>

                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="questions[{{ $index }}][question_type]" required class="question-type-select text-sm">
                                                <option value="">Pilih tipe...</option>
                                                <option value="multiple-choice" {{ $question['question_type'] == 'multiple-choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                                <option value="true-false" {{ $question['question_type'] == 'true-false' ? 'selected' : '' }}>Benar/Salah</option>
                                                <option value="essay" {{ $question['question_type'] == 'essay' ? 'selected' : '' }}>Essay</option>
                                                <option value="rating" {{ $question['question_type'] == 'rating' ? 'selected' : '' }}>Rating (1-5)</option>
                                            </select>

                                            <input type="number" name="questions[{{ $index }}][points]" required
                                                   min="0" step="0.5" value="{{ $question['points'] }}"
                                                   class="text-sm" placeholder="Poin">
                                        </div>

                                        <div class="options-container {{ in_array($question['question_type'], ['multiple-choice', 'true-false']) ? '' : 'hidden' }}">
                                            <div class="space-y-1">
                                                @if($question['question_type'] == 'multiple-choice' && isset($question['options']))
                                                    @foreach($question['options'] as $optIndex => $option)
                                                        <div class="flex gap-2">
                                                            <input type="text" name="questions[{{ $index }}][options][]" value="{{ $option }}"
                                                                   class="flex-1 text-sm" placeholder="Pilihan {{ chr(65 + $optIndex) }}">
                                                            @if($optIndex > 1)
                                                                <button type="button" onclick="this.parentElement.remove()" class="text-apple-red text-xs">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if($question['question_type'] == 'multiple-choice')
                                                    <button type="button" onclick="addOption(this)" class="text-xs text-apple-blue hover:text-blue-400">
                                                        + Tambah Pilihan
                                                    </button>
                                                @endif
                                            </div>

                                            <input type="text" name="questions[{{ $index }}][correct_answer]" value="{{ $question['correct_answer'] ?? '' }}"
                                                   class="w-full text-sm mt-2" 
                                                   placeholder="{{ $question['question_type'] == 'true-false' ? 'true / false' : 'Jawaban benar (A, B, C, dst)' }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($test->questions)
                            @foreach($test->questions as $index => $question)
                                <div class="question-item card-nested p-3 space-y-2" data-index="{{ $index }}">
                                    <div class="flex justify-between items-start gap-2">
                                        <p class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">SOAL #<span class="question-number">{{ $index + 1 }}</span></p>
                                        <button type="button" onclick="removeQuestion(this)" class="text-apple-red hover:text-red-400 text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-2">
                                        <textarea name="questions[{{ $index }}][question_text]" required
                                                  class="w-full text-sm" rows="2"
                                                  placeholder="Tulis pertanyaan di sini...">{{ $question['question_text'] }}</textarea>

                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="questions[{{ $index }}][question_type]" required class="question-type-select text-sm">
                                                <option value="">Pilih tipe...</option>
                                                <option value="multiple-choice" {{ $question['question_type'] == 'multiple-choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                                <option value="true-false" {{ $question['question_type'] == 'true-false' ? 'selected' : '' }}>Benar/Salah</option>
                                                <option value="essay" {{ $question['question_type'] == 'essay' ? 'selected' : '' }}>Essay</option>
                                                <option value="rating" {{ $question['question_type'] == 'rating' ? 'selected' : '' }}>Rating (1-5)</option>
                                            </select>

                                            <input type="number" name="questions[{{ $index }}][points]" required
                                                   min="0" step="0.5" value="{{ $question['points'] }}"
                                                   class="text-sm" placeholder="Poin">
                                        </div>

                                        <div class="options-container {{ in_array($question['question_type'], ['multiple-choice', 'true-false']) ? '' : 'hidden' }}">
                                            <div class="space-y-1">
                                                @if($question['question_type'] == 'multiple-choice' && isset($question['options']))
                                                    @foreach($question['options'] as $optIndex => $option)
                                                        <div class="flex gap-2">
                                                            <input type="text" name="questions[{{ $index }}][options][]" value="{{ $option }}"
                                                                   class="flex-1 text-sm" placeholder="Pilihan {{ chr(65 + $optIndex) }}">
                                                            @if($optIndex > 1)
                                                                <button type="button" onclick="this.parentElement.remove()" class="text-apple-red text-xs">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if($question['question_type'] == 'multiple-choice')
                                                    <button type="button" onclick="addOption(this)" class="text-xs text-apple-blue hover:text-blue-400">
                                                        + Tambah Pilihan
                                                    </button>
                                                @endif
                                            </div>

                                            <input type="text" name="questions[{{ $index }}][correct_answer]" value="{{ $question['correct_answer'] ?? '' }}"
                                                   class="w-full text-sm mt-2" 
                                                   placeholder="{{ $question['question_type'] == 'true-false' ? 'true / false' : 'Jawaban benar (A, B, C, dst)' }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    @if((!$test->questions || $test->questions->isEmpty()) && !old('questions'))
                        <p class="text-sm text-center py-8" style="color: rgba(235,235,245,0.5);">
                            Belum ada soal. Klik "Tambah Soal" untuk mulai.
                        </p>
                    @endif
                </div>

                <!-- Document Editing Section (Hidden by default) -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-4" id="documentEditingSection" style="display: {{ $test->test_type === 'document-editing' ? 'block' : 'none' }};">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Document Editing</p>
                        <h2 class="text-base font-semibold text-white">Template & Kriteria Penilaian</h2>
                    </div>

                    {{-- Template File Upload --}}
                    <div class="space-y-1">
                        <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                            Template File (Word Document)
                        </label>
                        @if($test->template_file_path)
                            <div class="bg-dark-surface-secondary rounded-lg p-3 mb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-file-word text-apple-blue"></i>
                                        <span class="text-sm text-white">{{ basename($test->template_file_path) }}</span>
                                    </div>
                                    <a href="{{ route('admin.recruitment.tests.download-template', $test) }}" class="text-xs text-apple-blue hover:text-blue-400">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="template_file" accept=".doc,.docx" class="w-full text-sm"
                               style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-radius: 10px; padding: 8px;">
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                            Upload file Word yang akan diperbaiki kandidat (Max: 10MB) - Kosongkan jika tidak ingin mengubah
                        </p>
                    </div>

                    {{-- Kriteria Penilaian --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Kriteria Penilaian
                            </label>
                            <button type="button" id="addCriteria" class="btn-secondary-sm">
                                <i class="fas fa-plus mr-1"></i>Tambah Kriteria
                            </button>
                        </div>
                        
                        <div id="criteriaContainer" class="space-y-3">
                            @if($test->test_type === 'document-editing' && isset($test->evaluation_criteria['criteria']))
                                @foreach($test->evaluation_criteria['criteria'] as $index => $criterion)
                                    <div class="card-nested p-3 space-y-2">
                                        <div class="flex justify-between items-start">
                                            <p class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">KRITERIA #<span class="criteria-number">{{ $index + 1 }}</span></p>
                                            <button type="button" class="remove-criteria text-apple-red hover:text-red-400 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <input type="text" name="evaluation_criteria[{{ $index }}][category]" 
                                               value="{{ $criterion['category'] }}"
                                               placeholder="Kategori (e.g., Formatting, Content)" 
                                               class="w-full text-sm" required>
                                        
                                        <textarea name="evaluation_criteria[{{ $index }}][description]" 
                                                  placeholder="Deskripsi kriteria penilaian" 
                                                  class="w-full text-sm" rows="2" required>{{ $criterion['description'] }}</textarea>
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="evaluation_criteria[{{ $index }}][points]" 
                                                   value="{{ $criterion['points'] }}"
                                                   placeholder="Poin" min="0" step="0.5"
                                                   class="text-sm" required>
                                            
                                            <select name="evaluation_criteria[{{ $index }}][type]" class="text-sm" required>
                                                <option value="Technical" {{ $criterion['type'] == 'Technical' ? 'selected' : '' }}>Technical</option>
                                                <option value="Analysis" {{ $criterion['type'] == 'Analysis' ? 'selected' : '' }}>Analysis</option>
                                                <option value="Quality" {{ $criterion['type'] == 'Quality' ? 'selected' : '' }}>Quality</option>
                                                <option value="checkbox" {{ $criterion['type'] == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                <option value="rating" {{ $criterion['type'] == 'rating' ? 'selected' : '' }}>Rating</option>
                                                <option value="numeric" {{ $criterion['type'] == 'numeric' ? 'selected' : '' }}>Numeric</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div id="criteriaEmptyState" class="text-center py-6" style="color: rgba(235,235,245,0.5); display: {{ ($test->test_type === 'document-editing' && isset($test->evaluation_criteria['criteria']) && count($test->evaluation_criteria['criteria']) > 0) ? 'none' : 'block' }};">
                            <i class="fas fa-check-circle text-2xl mb-2"></i>
                            <p class="text-xs">Belum ada kriteria. Klik "Tambah Kriteria" untuk memulai.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Status -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Status</p>
                    
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $test->is_active) ? 'checked' : '' }}
                                   class="rounded">
                            <span class="text-sm" style="color: rgba(235,235,245,0.85);">Template Aktif</span>
                        </label>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                            Hanya template aktif yang dapat diassign ke kandidat.
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Statistik</p>
                    
                    <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.85);">
                        <div class="flex justify-between">
                            <span style="color: rgba(235,235,245,0.6);">Total Soal:</span>
                            <strong id="total-questions">{{ $test->questions ? count($test->questions) : 0 }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: rgba(235,235,245,0.6);">Total Poin:</span>
                            <strong id="total-points">{{ $test->questions ? collect($test->questions)->sum('points') : 0 }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: rgba(235,235,245,0.6);">Sesi Digunakan:</span>
                            <strong>{{ $test->testSessions->count() }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-save mr-2"></i>Update Template
                    </button>
                    <a href="{{ route('admin.recruitment.tests.show', $test) }}" class="btn-secondary w-full">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let questionIndex = {{ old('questions') ? count(old('questions')) : ($test->questions ? count($test->questions) : 0) }};

function addQuestion() {
    const container = document.getElementById('questions-container');
    const emptyMessage = container.querySelector('p');
    if (emptyMessage) emptyMessage.remove();
    
    const html = `
        <div class="question-item card-nested p-3 space-y-2" data-index="${questionIndex}">
            <div class="flex justify-between items-start gap-2">
                <p class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">SOAL #<span class="question-number">${questionIndex + 1}</span></p>
                <button type="button" onclick="removeQuestion(this)" class="text-apple-red hover:text-red-400 text-xs">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="space-y-2">
                <textarea name="questions[${questionIndex}][question_text]" required
                          class="w-full text-sm" rows="2"
                          placeholder="Tulis pertanyaan di sini..."></textarea>

                <div class="grid grid-cols-2 gap-2">
                    <select name="questions[${questionIndex}][question_type]" required class="question-type-select text-sm">
                        <option value="">Pilih tipe...</option>
                        <option value="multiple-choice">Pilihan Ganda</option>
                        <option value="true-false">Benar/Salah</option>
                        <option value="essay">Essay</option>
                        <option value="rating">Rating (1-5)</option>
                    </select>

                    <input type="number" name="questions[${questionIndex}][points]" required
                           min="0" step="0.5" value="1"
                           class="text-sm" placeholder="Poin">
                </div>

                <div class="options-container hidden">
                    <div class="space-y-1"></div>
                    <input type="text" name="questions[${questionIndex}][correct_answer]"
                           class="w-full text-sm mt-2" placeholder="Jawaban benar">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    questionIndex++;
    updateStatistics();
}

function removeQuestion(btn) {
    if (confirm('Hapus soal ini?')) {
        btn.closest('.question-item').remove();
        updateQuestionNumbers();
        updateStatistics();
    }
}

function updateQuestionNumbers() {
    document.querySelectorAll('.question-number').forEach((el, idx) => {
        el.textContent = idx + 1;
    });
}

function addOption(btn) {
    const container = btn.previousElementSibling;
    const optionCount = container.querySelectorAll('input[type="text"]').length;
    
    const html = `
        <div class="flex gap-2">
            <input type="text" name="${btn.closest('.question-item').querySelector('select').name.replace('[question_type]', '[options][]')}"
                   class="flex-1 text-sm" placeholder="Pilihan ${String.fromCharCode(65 + optionCount)}">
            <button type="button" onclick="this.parentElement.remove()" class="text-apple-red text-xs">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
}

function updateStatistics() {
    const questions = document.querySelectorAll('.question-item').length;
    let totalPoints = 0;
    
    document.querySelectorAll('input[name*="[points]"]').forEach(input => {
        totalPoints += parseFloat(input.value) || 0;
    });
    
    document.getElementById('total-questions').textContent = questions;
    document.getElementById('total-points').textContent = totalPoints.toFixed(1);
}

// Handle question type change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('question-type-select')) {
        const questionItem = e.target.closest('.question-item');
        const optionsContainer = questionItem.querySelector('.options-container');
        const optionsDiv = optionsContainer.querySelector('.space-y-1');
        const correctAnswerInput = optionsContainer.querySelector('input[type="text"]');
        const type = e.target.value;
        
        if (type === 'multiple-choice') {
            optionsContainer.classList.remove('hidden');
            optionsDiv.innerHTML = `
                <div class="flex gap-2">
                    <input type="text" name="${e.target.name.replace('[question_type]', '[options][]')}"
                           class="flex-1 text-sm" placeholder="Pilihan A" required>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="${e.target.name.replace('[question_type]', '[options][]')}"
                           class="flex-1 text-sm" placeholder="Pilihan B" required>
                </div>
            `;
            optionsDiv.insertAdjacentHTML('beforeend', '<button type="button" onclick="addOption(this)" class="text-xs text-apple-blue hover:text-blue-400">+ Tambah Pilihan</button>');
            correctAnswerInput.placeholder = 'Jawaban benar (A, B, C, dst)';
            correctAnswerInput.required = true;
        } else if (type === 'true-false') {
            optionsContainer.classList.remove('hidden');
            optionsDiv.innerHTML = '';
            correctAnswerInput.placeholder = 'true / false';
            correctAnswerInput.required = true;
        } else {
            optionsContainer.classList.add('hidden');
            correctAnswerInput.required = false;
        }
    }
});

// Update points dynamically
document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[points]')) {
        updateStatistics();
    }
});

// Toggle between Questions and Document Editing sections based on test type
const testTypeSelect = document.getElementById('test_type');
const questionsSection = document.getElementById('questionsSection');
const documentEditingSection = document.getElementById('documentEditingSection');

testTypeSelect.addEventListener('change', function() {
    if (this.value === 'document-editing') {
        questionsSection.style.display = 'none';
        documentEditingSection.style.display = 'block';
    } else {
        questionsSection.style.display = 'block';
        documentEditingSection.style.display = 'none';
    }
});

// Add Criteria for Document Editing
let criteriaIndex = {{ $test->test_type === 'document-editing' && isset($test->evaluation_criteria['criteria']) ? count($test->evaluation_criteria['criteria']) : 0 }};
const criteriaContainer = document.getElementById('criteriaContainer');
const criteriaEmptyState = document.getElementById('criteriaEmptyState');

document.getElementById('addCriteria').addEventListener('click', function() {
    criteriaEmptyState.style.display = 'none';
    
    const criteriaItem = document.createElement('div');
    criteriaItem.className = 'card-nested p-3 space-y-2';
    criteriaItem.innerHTML = `
        <div class="flex justify-between items-start">
            <p class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">KRITERIA #<span class="criteria-number">${criteriaIndex + 1}</span></p>
            <button type="button" class="remove-criteria text-apple-red hover:text-red-400 text-xs">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        <input type="text" name="evaluation_criteria[${criteriaIndex}][category]" 
               placeholder="Kategori (e.g., Formatting, Content)" 
               class="w-full text-sm" required>
        
        <textarea name="evaluation_criteria[${criteriaIndex}][description]" 
                  placeholder="Deskripsi kriteria penilaian (e.g., Perbaiki font menjadi Arial 11pt)" 
                  class="w-full text-sm" rows="2" required></textarea>
        
        <div class="grid grid-cols-2 gap-2">
            <input type="number" name="evaluation_criteria[${criteriaIndex}][points]" 
                   placeholder="Poin" min="0" step="0.5"
                   class="text-sm" required>
            
            <select name="evaluation_criteria[${criteriaIndex}][type]" class="text-sm" required>
                <option value="Technical">Technical</option>
                <option value="Analysis">Analysis</option>
                <option value="Quality">Quality</option>
                <option value="checkbox">Checkbox</option>
                <option value="rating">Rating</option>
                <option value="numeric">Numeric</option>
            </select>
        </div>
    `;
    
    criteriaContainer.appendChild(criteriaItem);
    criteriaIndex++;
    
    // Remove criteria handler
    criteriaItem.querySelector('.remove-criteria').addEventListener('click', function() {
        if (confirm('Hapus kriteria ini?')) {
            criteriaItem.remove();
            updateCriteriaNumbers();
            if (criteriaContainer.children.length === 0) {
                criteriaEmptyState.style.display = 'block';
            }
        }
    });
});

// Add remove handlers for existing criteria
document.querySelectorAll('.remove-criteria').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Hapus kriteria ini?')) {
            this.closest('.card-nested').remove();
            updateCriteriaNumbers();
            if (criteriaContainer.children.length === 0) {
                criteriaEmptyState.style.display = 'block';
            }
        }
    });
});

function updateCriteriaNumbers() {
    const criteria = criteriaContainer.querySelectorAll('.card-nested');
    criteria.forEach((c, index) => {
        c.querySelector('.criteria-number').textContent = index + 1;
    });
}
</script>
@endsection
