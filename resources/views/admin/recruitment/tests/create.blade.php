@extends('layouts.app')

@section('title', 'Create Test Template')

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
                    <span>Template Baru</span>
                </div>
                <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">Buat Template Tes</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Susun template tes dengan pertanyaan terstruktur dan nilai lulus yang jelas.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.tests.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-list mr-2"></i>Daftar Template
                </a>
            </div>
        </div>
    </section>

    <form action="{{ route('admin.recruitment.tests.store') }}" method="POST" id="testForm" class="space-y-4" enctype="multipart/form-data">
        @csrf
        
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
                            <input type="text" name="title" value="{{ old('title') }}" required
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
                                      placeholder="Ringkasan tujuan penilaian">{{ old('description') }}</textarea>
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
                                    <option value="psychology" {{ old('test_type') == 'psychology' ? 'selected' : '' }}>Psikologi</option>
                                    <option value="psychometric" {{ old('test_type') == 'psychometric' ? 'selected' : '' }}>Psikometrik</option>
                                    <option value="technical" {{ old('test_type') == 'technical' ? 'selected' : '' }}>Teknis</option>
                                    <option value="aptitude" {{ old('test_type') == 'aptitude' ? 'selected' : '' }}>Aptitude</option>
                                    <option value="personality" {{ old('test_type') == 'personality' ? 'selected' : '' }}>Kepribadian</option>
                                    <option value="document-editing" {{ old('test_type') == 'document-editing' ? 'selected' : '' }}>Document Editing</option>
                                </select>
                                @error('test_type')
                                    <p class="text-xs text-apple-red">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                    Durasi (menit) <span class="text-apple-red">*</span>
                                </label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="5" max="480"
                                       class="w-full">
                                @error('duration_minutes')
                                    <p class="text-xs text-apple-red">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                                Passing Score (%) <span class="text-apple-red">*</span>
                            </label>
                            <input type="number" name="passing_score" value="{{ old('passing_score', 70) }}" required min="0" max="100"
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
                                      placeholder="Instruksi yang akan dibaca kandidat sebelum memulai tes...">{{ old('instructions') }}</textarea>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                Gunakan format plain text dengan line breaks. Simbol seperti ▸, [ ], •, ▪ akan ditampilkan dengan styling otomatis.
                            </p>
                            @error('instructions')
                                <p class="text-xs text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="card-elevated rounded-apple-xl p-4" id="questionsSection">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Pertanyaan</p>
                            <h2 class="text-base font-semibold text-white">Daftar Pertanyaan</h2>
                        </div>
                        <button type="button" id="addQuestion" 
                                class="btn-primary-sm flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Tambah Pertanyaan
                        </button>
                    </div>

                    <div id="questionsContainer" class="space-y-4">
                        <!-- Questions will be added here dynamically -->
                    </div>

                    <div id="emptyState" class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                        <i class="fas fa-clipboard-list text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada pertanyaan</p>
                        <p class="text-xs">Klik "Tambah Pertanyaan" untuk memulai</p>
                    </div>
                </div>

                <!-- Document Editing Section (Hidden by default) -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-4" id="documentEditingSection" style="display: none;">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Document Editing</p>
                        <h2 class="text-base font-semibold text-white">Template & Kriteria Penilaian</h2>
                    </div>

                    {{-- Template File Upload --}}
                    <div class="space-y-1">
                        <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">
                            Template File (Word Document)
                        </label>
                        <input type="file" name="template_file" accept=".doc,.docx" class="w-full text-sm"
                               style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-radius: 10px; padding: 8px;">
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                            Upload file Word yang akan diperbaiki kandidat (Max: 10MB)
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
                            <!-- Criteria will be added here dynamically -->
                        </div>

                        <div id="criteriaEmptyState" class="text-center py-6" style="color: rgba(235,235,245,0.5);">
                            <i class="fas fa-check-circle text-2xl mb-2"></i>
                            <p class="text-xs">Belum ada kriteria. Klik "Tambah Kriteria" untuk memulai.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Status -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-2">
                    <h3 class="text-base font-semibold text-white">Status</h3>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-apple-blue">
                        <span class="text-sm" style="color: rgba(235,235,245,0.8);">Template Aktif</span>
                    </label>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                        Template aktif dapat diberikan ke kandidat.
                    </p>
                </div>

                <!-- Summary -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                    <h3 class="text-base font-semibold text-white">Ringkasan</h3>
                    <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.8);">
                        <div class="flex justify-between">
                            <span>Total Pertanyaan</span>
                            <span id="totalQuestions" class="font-semibold text-white">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Poin</span>
                            <span id="totalPoints" class="font-semibold text-white">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Durasi</span>
                            <span id="durationDisplay" class="font-semibold text-white">60 menit</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-elevated rounded-apple-xl p-4 space-y-2">
                    <button type="submit" 
                            class="btn-primary-sm w-full">
                        Buat Template
                    </button>
                    <a href="{{ route('admin.recruitment.tests.index') }}" 
                       class="btn-secondary-sm w-full text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template (Hidden) -->
<template id="questionTemplate">
<div class="question-item card-elevated rounded-apple-lg p-4">
        <div class="flex justify-between items-start mb-3">
            <h3 class="text-sm font-semibold text-white">Pertanyaan <span class="question-number"></span></h3>
            <button type="button" class="remove-question text-apple-red hover:opacity-80 transition-apple">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.55);">
                    Teks Pertanyaan <span class="text-apple-red">*</span>
                </label>
                <textarea name="questions[INDEX][question_text]" rows="2" required
                          class="w-full"
                          placeholder="Tuliskan pertanyaan..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.55);">
                        Jenis Pertanyaan <span class="text-apple-red">*</span>
                    </label>
                    <select name="questions[INDEX][question_type]" required class="question-type-select w-full">
                        <option value="multiple-choice">Multiple Choice</option>
                        <option value="true-false">True/False</option>
                        <option value="essay">Essay</option>
                        <option value="rating">Rating Scale</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.55);">
                        Poin <span class="text-apple-red">*</span>
                    </label>
                    <input type="number" name="questions[INDEX][points]" value="1" required min="0" step="0.5"
                           class="question-points w-full">
                </div>
            </div>

            <!-- Options Container (for multiple-choice) -->
            <div class="options-container">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Answer Options <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2 options-list">
                    <div class="flex gap-2">
                        <input type="text" name="questions[INDEX][options][]" required
                               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                               placeholder="Option 1">
                        <button type="button" class="add-option bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-3 rounded-lg hover:bg-green-200 dark:hover:bg-green-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Correct Answer -->
            <div class="correct-answer-container">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Correct Answer <span class="text-red-500">*</span>
                </label>
                <input type="text" name="questions[INDEX][correct_answer]" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                       placeholder="Enter the correct answer">
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let questionIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    const addQuestionBtn = document.getElementById('addQuestion');
    const questionsContainer = document.getElementById('questionsContainer');
    const emptyState = document.getElementById('emptyState');
    const template = document.getElementById('questionTemplate');
    const durationInput = document.querySelector('[name="duration_minutes"]');
    const durationDisplay = document.getElementById('durationDisplay');

    // Add question
    addQuestionBtn.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        const div = document.createElement('div');
        div.innerHTML = clone.querySelector('.question-item').outerHTML.replace(/INDEX/g, questionIndex);
        const questionItem = div.firstChild;
        
        questionsContainer.appendChild(questionItem);
        questionIndex++;
        
        emptyState.style.display = 'none';
        updateSummary();
        attachQuestionEvents(questionItem);
    });

    // Update duration display
    durationInput.addEventListener('input', function() {
        durationDisplay.textContent = this.value + ' min';
    });

    function attachQuestionEvents(questionItem) {
        // Remove question
        questionItem.querySelector('.remove-question').addEventListener('click', function() {
            questionItem.remove();
            updateSummary();
            if (questionsContainer.children.length === 0) {
                emptyState.style.display = 'block';
            }
        });

        // Question type change
        const typeSelect = questionItem.querySelector('.question-type-select');
        const optionsContainer = questionItem.querySelector('.options-container');
        const correctAnswerContainer = questionItem.querySelector('.correct-answer-container');
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'multiple-choice') {
                optionsContainer.style.display = 'block';
                correctAnswerContainer.querySelector('input').placeholder = 'Enter option number (e.g., 1)';
            } else if (this.value === 'true-false') {
                optionsContainer.style.display = 'none';
                correctAnswerContainer.querySelector('input').placeholder = 'Enter "true" or "false"';
            } else if (this.value === 'essay') {
                optionsContainer.style.display = 'none';
                correctAnswerContainer.style.display = 'none';
            } else {
                optionsContainer.style.display = 'none';
                correctAnswerContainer.querySelector('input').placeholder = 'Enter rating value';
            }
        });

        // Add option
        questionItem.querySelector('.add-option').addEventListener('click', function() {
            const optionsList = questionItem.querySelector('.options-list');
            const newOption = document.createElement('div');
            newOption.className = 'flex gap-2';
            newOption.innerHTML = `
                <input type="text" name="${typeSelect.name.replace('[question_type]', '[options][]')}" required
                       class="flex-1 w-full"
                       placeholder="Option ${optionsList.children.length + 1}">
                <button type="button" class="remove-option btn-secondary-sm" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9); border-color: rgba(255,59,48,0.2);">
                    <i class="fas fa-times"></i>
                </button>
            `;
            optionsList.appendChild(newOption);
            
            newOption.querySelector('.remove-option').addEventListener('click', function() {
                newOption.remove();
            });
        });

        // Points change
        questionItem.querySelector('.question-points').addEventListener('input', updateSummary);
    }

    function updateSummary() {
        const questions = questionsContainer.querySelectorAll('.question-item');
        let totalPoints = 0;
        
        questions.forEach((q, index) => {
            q.querySelector('.question-number').textContent = index + 1;
            const points = parseFloat(q.querySelector('.question-points').value) || 0;
            totalPoints += points;
        });
        
        document.getElementById('totalQuestions').textContent = questions.length;
        document.getElementById('totalPoints').textContent = totalPoints;
    }

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
    let criteriaIndex = 0;
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
            criteriaItem.remove();
            updateCriteriaNumbers();
            if (criteriaContainer.children.length === 0) {
                criteriaEmptyState.style.display = 'block';
            }
        });
    });
    
    function updateCriteriaNumbers() {
        const criteria = criteriaContainer.querySelectorAll('.card-nested');
        criteria.forEach((c, index) => {
            c.querySelector('.criteria-number').textContent = index + 1;
        });
    }
});
</script>
@endpush
@endsection
