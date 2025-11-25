# Analisis & Implementasi: Document Editing Test Type

## 📋 Overview

Fitur test baru untuk menguji kemampuan kandidat dalam memperbaiki/mengedit dokumen Word sesuai instruksi dalam waktu tertentu.

**Use Case:**
- Posisi Admin/Secretary
- Document Specialist
- Office Manager
- Virtual Assistant
- Data Entry Specialist

---

## 🎯 Konsep Test

### Alur Test:
1. **Admin/HR Membuat Template Test:**
   - Upload file Word yang sengaja dibuat "rusak" (formatting berantakan, typo, struktur salah)
   - Tulis instruksi detail perbaikan yang harus dilakukan
   - Set kriteria penilaian (checklist rubrik)
   - Set durasi test (misal: 30 menit)

2. **Kandidat Mengambil Test:**
   - Membaca instruksi test
   - Download file Word template yang "rusak"
   - Timer countdown dimulai otomatis saat download
   - Kandidat edit file secara offline
   - Upload hasil perbaikan sebelum waktu habis

3. **HR/Evaluator Menilai:**
   - Download file submission kandidat
   - Review berdasarkan checklist kriteria
   - Input score manual dengan catatan
   - Sistem hitung pass/fail berdasarkan passing score

---

## 🗄️ Database Schema Changes

### 1. Modifikasi `test_templates` Table

**Migration: `add_document_editing_fields_to_test_templates_table.php`**

```sql
ALTER TABLE test_templates 
  -- Ubah enum test_type untuk include 'document-editing'
  MODIFY COLUMN test_type ENUM(
    'psychology', 
    'psychometric', 
    'technical', 
    'aptitude', 
    'personality',
    'document-editing'  -- NEW
  );

-- Add new columns
ALTER TABLE test_templates
  ADD COLUMN template_file_path VARCHAR(500) NULL COMMENT 'Path to template document file',
  ADD COLUMN evaluation_criteria JSON NULL COMMENT 'Checklist/rubrik penilaian untuk document editing';
```

**Struktur `evaluation_criteria` JSON:**
```json
{
  "criteria": [
    {
      "id": 1,
      "category": "Formatting",
      "description": "Perbaiki font menjadi Arial 11pt untuk body text",
      "points": 10,
      "type": "checkbox"
    },
    {
      "id": 2,
      "category": "Formatting",
      "description": "Heading 1 harus Bold 14pt",
      "points": 10,
      "type": "checkbox"
    },
    {
      "id": 3,
      "category": "Content",
      "description": "Perbaiki 5 typo yang ada",
      "points": 15,
      "type": "numeric",
      "max_points": 15
    },
    {
      "id": 4,
      "category": "Structure",
      "description": "Susun ulang paragraf sesuai urutan logis",
      "points": 25,
      "type": "rating",
      "scale": 5
    }
  ],
  "total_points": 100
}
```

### 2. Modifikasi `test_sessions` Table

**Migration: `add_document_editing_fields_to_test_sessions_table.php`**

```sql
ALTER TABLE test_sessions
  ADD COLUMN submitted_file_path VARCHAR(500) NULL COMMENT 'Path to submitted document',
  ADD COLUMN submitted_at TIMESTAMP NULL COMMENT 'When candidate uploaded the file',
  ADD COLUMN evaluation_scores JSON NULL COMMENT 'Detailed scores per criteria',
  ADD COLUMN evaluator_id BIGINT UNSIGNED NULL COMMENT 'User who evaluated',
  ADD COLUMN evaluator_notes TEXT NULL COMMENT 'Notes from evaluator',
  ADD COLUMN evaluated_at TIMESTAMP NULL COMMENT 'When evaluation completed',
  ADD COLUMN requires_manual_review BOOLEAN DEFAULT FALSE COMMENT 'For document-editing type';

ALTER TABLE test_sessions
  ADD CONSTRAINT fk_evaluator
  FOREIGN KEY (evaluator_id) 
  REFERENCES users(id) 
  ON DELETE SET NULL;
```

**Struktur `evaluation_scores` JSON:**
```json
{
  "criteria_scores": [
    {
      "criteria_id": 1,
      "score": 10,
      "achieved": true,
      "notes": "Font sudah benar semua"
    },
    {
      "criteria_id": 2,
      "score": 8,
      "achieved": false,
      "notes": "Heading 1 benar, tapi ada 2 heading yang terlewat"
    }
  ],
  "total_score": 85,
  "completion_percentage": 85
}
```

---

## 💻 Backend Implementation

### 1. Update Model `TestTemplate.php`

```php
// Add to $fillable
protected $fillable = [
    // ... existing fields
    'template_file_path',
    'evaluation_criteria',
];

// Add to $casts
protected $casts = [
    // ... existing casts
    'evaluation_criteria' => 'array',
];

/**
 * Check if this is a document editing test.
 */
public function isDocumentEditingTest(): bool
{
    return $this->test_type === 'document-editing';
}

/**
 * Get template file URL for download.
 */
public function getTemplateFileUrl(): ?string
{
    if (!$this->template_file_path) {
        return null;
    }
    
    return Storage::url($this->template_file_path);
}

/**
 * Get total evaluation points.
 */
public function getTotalEvaluationPoints(): int
{
    if (!$this->evaluation_criteria || !isset($this->evaluation_criteria['criteria'])) {
        return 100;
    }
    
    return collect($this->evaluation_criteria['criteria'])->sum('points');
}
```

### 2. Update Model `TestSession.php`

```php
// Add to $fillable
protected $fillable = [
    // ... existing fields
    'submitted_file_path',
    'submitted_at',
    'evaluation_scores',
    'evaluator_id',
    'evaluator_notes',
    'evaluated_at',
    'requires_manual_review',
];

// Add to $casts
protected $casts = [
    // ... existing casts
    'evaluation_scores' => 'array',
    'submitted_at' => 'datetime',
    'evaluated_at' => 'datetime',
    'requires_manual_review' => 'boolean',
];

/**
 * Relation: evaluator user.
 */
public function evaluator(): BelongsTo
{
    return $this->belongsTo(User::class, 'evaluator_id');
}

/**
 * Get submitted file URL.
 */
public function getSubmittedFileUrl(): ?string
{
    if (!$this->submitted_file_path) {
        return null;
    }
    
    return Storage::url($this->submitted_file_path);
}

/**
 * Check if evaluation is pending.
 */
public function isPendingEvaluation(): bool
{
    return $this->requires_manual_review && !$this->evaluated_at;
}

/**
 * Calculate score from evaluation.
 */
public function calculateScoreFromEvaluation(): float
{
    if (!$this->evaluation_scores || !isset($this->evaluation_scores['criteria_scores'])) {
        return 0;
    }
    
    $totalScore = collect($this->evaluation_scores['criteria_scores'])->sum('score');
    $template = $this->testTemplate;
    $totalPoints = $template->getTotalEvaluationPoints();
    
    return round(($totalScore / $totalPoints) * 100, 2);
}
```

### 3. Controller: `DocumentEditingTestController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestSession;
use App\Models\TestTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentEditingTestController extends Controller
{
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
            Storage::delete($test->template_file_path);
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
        
        return Storage::download(
            $session->testTemplate->template_file_path,
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
            'document' => 'required|file|mimes:doc,docx|max:10240',
        ]);
        
        // Check if time is still valid
        if (now()->gt($session->expires_at)) {
            $session->update(['status' => 'expired']);
            return back()->with('error', 'Waktu test telah habis.');
        }
        
        // Delete old submission if exists
        if ($session->submitted_file_path) {
            Storage::delete($session->submitted_file_path);
        }
        
        // Store submission
        $path = $request->file('document')->store('test-submissions', 'private');
        
        $session->update([
            'submitted_file_path' => $path,
            'submitted_at' => now(),
            'completed_at' => now(),
            'status' => 'completed',
            'requires_manual_review' => true,
            'time_taken_minutes' => $session->started_at->diffInMinutes(now()),
        ]);
        
        return redirect()->route('candidate.test.completed', $token)
            ->with('success', 'Dokumen berhasil disubmit. Menunggu penilaian dari evaluator.');
    }
    
    /**
     * Evaluation form for HR.
     */
    public function showEvaluationForm(TestSession $session)
    {
        // Check permission
        $this->authorize('evaluate-test');
        
        if (!$session->requires_manual_review) {
            abort(403, 'This session does not require manual review');
        }
        
        return view('admin.recruitment.tests.evaluate', compact('session'));
    }
    
    /**
     * Submit evaluation scores.
     */
    public function submitEvaluation(Request $request, TestSession $session)
    {
        $this->authorize('evaluate-test');
        
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
        
        $finalScore = $session->calculateScoreFromEvaluation();
        $passed = $finalScore >= $session->testTemplate->passing_score;
        
        $session->update([
            'evaluation_scores' => $evaluationScores,
            'score' => $finalScore,
            'passed' => $passed,
            'evaluator_id' => auth()->id(),
            'evaluator_notes' => $request->evaluator_notes,
            'evaluated_at' => now(),
            'requires_manual_review' => false,
        ]);
        
        return redirect()->route('admin.recruitment.tests.sessions.results', $session)
            ->with('success', 'Evaluasi berhasil disimpan. Score: ' . $finalScore);
    }
    
    /**
     * Download submitted file (for evaluators).
     */
    public function downloadSubmission(TestSession $session)
    {
        $this->authorize('evaluate-test');
        
        if (!$session->submitted_file_path) {
            abort(404, 'No submission found');
        }
        
        return Storage::download(
            $session->submitted_file_path,
            'submission_' . $session->application->full_name . '_' . $session->id . '.docx'
        );
    }
}
```

---

## 🎨 Frontend Implementation

### 1. Update Test Creation Form

**File: `resources/views/admin/recruitment/tests/create.blade.php`**

Tambahkan conditional field untuk document-editing:

```blade
{{-- Test Type Selection --}}
<select name="test_type" id="test_type" required>
    <option value="">Pilih Tipe Test</option>
    <option value="psychology">Psychology</option>
    <option value="psychometric">Psychometric</option>
    <option value="technical">Technical</option>
    <option value="aptitude">Aptitude</option>
    <option value="personality">Personality</option>
    <option value="document-editing">Document Editing</option> <!-- NEW -->
</select>

{{-- Document Editing Specific Fields (Hidden by default) --}}
<div id="document-editing-fields" style="display: none;">
    <div class="space-y-4">
        <div>
            <label>Template File (Word Document)</label>
            <input type="file" name="template_file" accept=".doc,.docx">
            <p class="text-xs text-gray-500">Upload file Word yang akan diperbaiki kandidat (Max: 10MB)</p>
        </div>
        
        <div>
            <label>Kriteria Penilaian</label>
            <div id="criteria-container" class="space-y-2">
                <!-- Criteria items will be added here dynamically -->
            </div>
            <button type="button" onclick="addCriteria()" class="btn-secondary-sm mt-2">
                <i class="fas fa-plus mr-1"></i>Tambah Kriteria
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('test_type').addEventListener('change', function() {
    const docEditFields = document.getElementById('document-editing-fields');
    const questionFields = document.getElementById('questions-section');
    
    if (this.value === 'document-editing') {
        docEditFields.style.display = 'block';
        questionFields.style.display = 'none';
    } else {
        docEditFields.style.display = 'none';
        questionFields.style.display = 'block';
    }
});

let criteriaIndex = 0;

function addCriteria() {
    const container = document.getElementById('criteria-container');
    const html = `
        <div class="card-nested p-3 space-y-2 criteria-item" data-index="${criteriaIndex}">
            <div class="flex justify-between">
                <p class="text-xs font-semibold">KRITERIA #${criteriaIndex + 1}</p>
                <button type="button" onclick="this.closest('.criteria-item').remove()" class="text-red-500">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <input type="text" 
                   name="evaluation_criteria[${criteriaIndex}][category]" 
                   placeholder="Kategori (e.g., Formatting, Content)" 
                   class="w-full text-sm" required>
            
            <textarea name="evaluation_criteria[${criteriaIndex}][description]" 
                      placeholder="Deskripsi kriteria penilaian" 
                      class="w-full text-sm" rows="2" required></textarea>
            
            <div class="grid grid-cols-2 gap-2">
                <input type="number" 
                       name="evaluation_criteria[${criteriaIndex}][points]" 
                       placeholder="Poin" min="0" 
                       class="text-sm" required>
                
                <select name="evaluation_criteria[${criteriaIndex}][type]" class="text-sm" required>
                    <option value="checkbox">Checkbox (Ya/Tidak)</option>
                    <option value="rating">Rating (1-5)</option>
                    <option value="numeric">Numeric Input</option>
                </select>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    criteriaIndex++;
}

// Add first criteria by default
if (document.getElementById('test_type').value === 'document-editing') {
    addCriteria();
}
</script>
```

### 2. Candidate Test Interface

**File: `resources/views/candidate/test/document-editing.blade.php`**

```blade
@extends('layouts.candidate')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="card-elevated rounded-apple-xl p-6 space-y-6">
        {{-- Header --}}
        <div class="text-center space-y-3">
            <h1 class="text-2xl font-bold text-white">{{ $session->testTemplate->title }}</h1>
            <p class="text-sm text-gray-400">{{ $session->testTemplate->description }}</p>
            
            {{-- Timer --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-apple-blue/20">
                <i class="fas fa-clock text-apple-blue"></i>
                <span id="countdown" class="text-xl font-bold text-apple-blue">
                    {{ $session->getRemainingMinutes() }}:00
                </span>
            </div>
        </div>
        
        {{-- Instructions --}}
        <div class="card-nested p-4 space-y-3">
            <h3 class="font-semibold text-white flex items-center gap-2">
                <i class="fas fa-info-circle text-apple-blue"></i>
                Instruksi Test
            </h3>
            <div class="text-sm text-gray-300 space-y-2">
                {!! nl2br(e($session->testTemplate->instructions)) !!}
            </div>
        </div>
        
        {{-- Download Template --}}
        @if(!$session->submitted_file_path)
            <div class="text-center space-y-3">
                <p class="text-sm text-gray-400">Klik tombol di bawah untuk download file template:</p>
                <a href="{{ route('candidate.test.download-template', $session->session_token) }}" 
                   class="btn-primary" id="download-btn">
                    <i class="fas fa-download mr-2"></i>Download Template File
                </a>
                <p class="text-xs text-yellow-400">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Timer akan dimulai saat Anda download file
                </p>
            </div>
        @endif
        
        {{-- Upload Form --}}
        @if($session->status === 'in-progress')
            <form action="{{ route('candidate.test.submit-document', $session->session_token) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-4">
                @csrf
                
                <div class="card-nested p-4 space-y-3">
                    <h3 class="font-semibold text-white">Upload Hasil Perbaikan</h3>
                    
                    <div class="space-y-2">
                        <input type="file" 
                               name="document" 
                               accept=".doc,.docx" 
                               required 
                               class="w-full px-3 py-2 rounded-apple text-sm">
                        <p class="text-xs text-gray-500">
                            Format: .doc atau .docx | Max: 10MB
                        </p>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full" onclick="return confirm('Yakin submit? Anda tidak bisa mengubah setelah submit.')">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Dokumen
                    </button>
                </div>
            </form>
        @endif
        
        {{-- Completed Message --}}
        @if($session->status === 'completed')
            <div class="text-center space-y-3 py-6">
                <i class="fas fa-check-circle text-6xl text-green-500"></i>
                <h3 class="text-xl font-bold text-white">Test Selesai!</h3>
                <p class="text-sm text-gray-400">
                    Dokumen Anda telah berhasil disubmit dan sedang menunggu penilaian dari evaluator.
                </p>
                <p class="text-xs text-gray-500">
                    Waktu penyelesaian: {{ $session->time_taken_minutes }} menit
                </p>
            </div>
        @endif
    </div>
</div>

<script>
// Countdown timer
const expiresAt = new Date("{{ $session->expires_at->toIso8601String() }}");

function updateCountdown() {
    const now = new Date();
    const diff = expiresAt - now;
    
    if (diff <= 0) {
        document.getElementById('countdown').textContent = '00:00';
        document.getElementById('countdown').classList.add('text-red-500');
        
        // Auto submit form or redirect
        window.location.reload();
        return;
    }
    
    const minutes = Math.floor(diff / 1000 / 60);
    const seconds = Math.floor((diff / 1000) % 60);
    
    document.getElementById('countdown').textContent = 
        `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    
    // Warning if less than 5 minutes
    if (minutes < 5) {
        document.getElementById('countdown').classList.add('text-red-500');
    }
}

// Update every second
setInterval(updateCountdown, 1000);
updateCountdown();

// Start timer on download
document.getElementById('download-btn')?.addEventListener('click', function() {
    setTimeout(() => {
        document.getElementById('download-btn').style.display = 'none';
    }, 1000);
});
</script>
@endsection
```

### 3. Evaluation Interface for HR

**File: `resources/views/admin/recruitment/tests/evaluate.blade.php`**

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-6">
        <h1 class="text-2xl font-bold text-white">Evaluasi Submission</h1>
        <p class="text-sm text-gray-400 mt-2">
            Kandidat: <strong>{{ $session->application->full_name }}</strong> | 
            Test: <strong>{{ $session->testTemplate->title }}</strong>
        </p>
    </section>
    
    {{-- Download Files --}}
    <section class="card-elevated rounded-apple-xl p-6 space-y-4">
        <h3 class="font-semibold text-white">Download Files</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.recruitment.tests.download-template', $session->testTemplate) }}" 
               class="btn-secondary">
                <i class="fas fa-file-word mr-2"></i>Template Original
            </a>
            
            <a href="{{ route('admin.recruitment.tests.download-submission', $session) }}" 
               class="btn-primary">
                <i class="fas fa-download mr-2"></i>Submission Kandidat
            </a>
        </div>
    </section>
    
    {{-- Evaluation Form --}}
    <form action="{{ route('admin.recruitment.tests.submit-evaluation', $session) }}" 
          method="POST">
        @csrf
        
        <section class="card-elevated rounded-apple-xl p-6 space-y-5">
            <h3 class="font-semibold text-white">Kriteria Penilaian</h3>
            
            @foreach($session->testTemplate->evaluation_criteria['criteria'] as $criteria)
                <div class="card-nested p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs text-apple-blue uppercase tracking-wide">
                                {{ $criteria['category'] }}
                            </p>
                            <p class="text-sm font-semibold text-white mt-1">
                                {{ $criteria['description'] }}
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-apple-blue/20 text-apple-blue rounded-full text-xs font-semibold">
                            Max: {{ $criteria['points'] }} poin
                        </span>
                    </div>
                    
                    <input type="hidden" 
                           name="criteria_scores[{{ $loop->index }}][criteria_id]" 
                           value="{{ $criteria['id'] }}">
                    
                    @if($criteria['type'] === 'checkbox')
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   id="criteria_{{ $criteria['id'] }}_check"
                                   onchange="this.nextElementSibling.value = this.checked ? {{ $criteria['points'] }} : 0">
                            <input type="number" 
                                   name="criteria_scores[{{ $loop->index }}][score]" 
                                   value="0" 
                                   min="0" 
                                   max="{{ $criteria['points'] }}" 
                                   class="w-24 text-sm" 
                                   readonly>
                            <label for="criteria_{{ $criteria['id'] }}_check" class="text-sm text-gray-300">
                                Terpenuhi
                            </label>
                        </div>
                    @elseif($criteria['type'] === 'rating')
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        class="rating-btn w-10 h-10 rounded-full border-2 border-gray-600 hover:border-apple-blue transition"
                                        onclick="setRating(this, {{ $i }}, {{ $criteria['points'] }}, {{ $loop->parent->index }})">
                                    {{ $i }}
                                </button>
                            @endfor
                            <input type="number" 
                                   name="criteria_scores[{{ $loop->index }}][score]" 
                                   value="0" 
                                   min="0" 
                                   max="{{ $criteria['points'] }}" 
                                   class="w-24 text-sm ml-3" 
                                   readonly>
                        </div>
                    @else
                        <input type="number" 
                               name="criteria_scores[{{ $loop->index }}][score]" 
                               value="0" 
                               min="0" 
                               max="{{ $criteria['points'] }}" 
                               step="0.5"
                               class="w-32 text-sm" 
                               required>
                    @endif
                    
                    <textarea name="criteria_scores[{{ $loop->index }}][notes]" 
                              placeholder="Catatan evaluator (opsional)" 
                              class="w-full text-sm" 
                              rows="2"></textarea>
                </div>
            @endforeach
        </section>
        
        {{-- Overall Notes --}}
        <section class="card-elevated rounded-apple-xl p-6 space-y-4">
            <h3 class="font-semibold text-white">Catatan Keseluruhan</h3>
            <textarea name="evaluator_notes" 
                      placeholder="Tulis catatan atau feedback untuk kandidat..." 
                      class="w-full text-sm" 
                      rows="4"></textarea>
            
            <button type="submit" class="btn-primary w-full">
                <i class="fas fa-check mr-2"></i>Submit Evaluasi
            </button>
        </section>
    </form>
</div>

<script>
function setRating(btn, rating, maxPoints, index) {
    // Reset all buttons in this group
    const buttons = btn.parentElement.querySelectorAll('.rating-btn');
    buttons.forEach(b => {
        b.classList.remove('bg-apple-blue', 'border-apple-blue', 'text-white');
        b.classList.add('border-gray-600', 'text-gray-400');
    });
    
    // Highlight selected rating
    for (let i = 0; i < rating; i++) {
        buttons[i].classList.add('bg-apple-blue', 'border-apple-blue', 'text-white');
        buttons[i].classList.remove('border-gray-600', 'text-gray-400');
    }
    
    // Calculate score
    const score = (rating / 5) * maxPoints;
    const scoreInput = btn.parentElement.querySelector('input[type="number"]');
    scoreInput.value = score.toFixed(1);
}
</script>
@endsection
```

---

## 🔐 Security & Validation

### File Upload Validation Rules:

```php
// For Template Upload (Admin)
'template_file' => [
    'required',
    'file',
    'mimes:doc,docx',
    'max:10240', // 10MB
    function ($attribute, $value, $fail) {
        // Check for macros/viruses using VirusTotal API or ClamAV
        // if (hasMacros($value)) {
        //     $fail('File contains macros. Please remove macros before upload.');
        // }
    }
]

// For Submission Upload (Candidate)
'document' => [
    'required',
    'file',
    'mimes:doc,docx',
    'max:10240',
    'different:template_file' // Ensure it's been modified
]
```

### Anti-Cheat Measures:

1. **Timer Enforcement:**
   - Server-side validation saat submit
   - Block submission jika melebihi `expires_at`

2. **File Modification Check:**
   - Cek timestamp file modification
   - Cek file size (harus berubah dari template)

3. **IP & User Agent Tracking:**
   - Log IP dan device saat download & upload
   - Flag jika berbeda device

---

## 📊 Scoring Logic

### Automatic Calculation:

```php
// In TestSession model
public function calculateScoreFromEvaluation(): float
{
    if (!$this->evaluation_scores) {
        return 0;
    }
    
    $totalScore = collect($this->evaluation_scores['criteria_scores'])
        ->sum('score');
    
    $template = $this->testTemplate;
    $totalPossiblePoints = $template->getTotalEvaluationPoints();
    
    // Convert to percentage (0-100)
    $percentage = ($totalScore / $totalPossiblePoints) * 100;
    
    return round($percentage, 2);
}
```

### Pass/Fail Determination:

```php
// After evaluation submitted
$finalScore = $session->calculateScoreFromEvaluation();
$passed = $finalScore >= $session->testTemplate->passing_score;

$session->update([
    'score' => $finalScore,
    'passed' => $passed,
]);
```

---

## 🚀 Migration Plan

### Phase 1: Database (Week 1)
- [ ] Create migrations for new columns
- [ ] Run migrations on staging
- [ ] Test data integrity

### Phase 2: Backend (Week 2)
- [ ] Update models (TestTemplate, TestSession)
- [ ] Create DocumentEditingTestController
- [ ] Add routes
- [ ] Write unit tests

### Phase 3: Frontend (Week 3)
- [ ] Update test creation form
- [ ] Create candidate test interface
- [ ] Create evaluation interface
- [ ] Test file upload/download

### Phase 4: Testing (Week 4)
- [ ] End-to-end testing
- [ ] Security audit
- [ ] Performance testing
- [ ] User acceptance testing

---

## 📝 Example Use Cases

### Use Case 1: Admin Position Test
**Scenario:** Menguji kemampuan formatting dokumen untuk calon Admin

**Template File:** Resume berantakan dengan:
- Font inkonsisten (10pt, 12pt, 14pt mixed)
- Spacing tidak rapi
- Tidak ada bullet points
- Header tidak bold

**Instruksi:**
1. Perbaiki font menjadi Arial 11pt untuk semua body text
2. Heading 1 harus Bold 14pt
3. Tambahkan bullet points di section Experience
4. Rapikan spacing antar paragraf (12pt)
5. Pastikan margin 1 inch di semua sisi

**Kriteria Penilaian:**
- Font consistency (20 poin)
- Heading formatting (15 poin)
- Bullet points (15 poin)
- Spacing (20 poin)
- Margins (10 poin)
- Overall presentation (20 poin)

**Durasi:** 30 menit
**Passing Score:** 75%

### Use Case 2: Document Specialist Test
**Scenario:** Menguji kemampuan proofreading dan editing

**Template File:** Proposal dengan:
- 10 typo tersebar
- 5 grammatical errors
- Struktur paragraf tidak logis
- Numbering salah

**Instruksi:**
1. Perbaiki semua typo
2. Koreksi grammatical errors
3. Susun ulang paragraf agar logis
4. Perbaiki numbering sections

**Kriteria Penilaian:**
- Typo correction (30 poin)
- Grammar correction (25 poin)
- Structure reorganization (25 poin)
- Numbering fix (20 poin)

**Durasi:** 45 menit
**Passing Score:** 80%

---

## 🎯 Success Metrics

1. **Adoption Rate:** % test templates using document-editing type
2. **Completion Rate:** % candidates yang complete test
3. **Evaluation Time:** Average time untuk HR evaluate submission
4. **Score Distribution:** Bell curve analysis
5. **Pass Rate:** % candidates yang pass

---

## 🔄 Future Enhancements

### V2 Features:
1. **Auto-Scoring (AI):**
   - Integrate Microsoft Word API atau Apache POI
   - Auto-detect formatting issues
   - Compare dengan reference file
   - Reduce manual evaluation time

2. **Real-time Collaboration:**
   - Allow multiple evaluators
   - Consensus scoring
   - Comments & discussions

3. **Video Recording:**
   - Record screen saat kandidat edit
   - Review process, tidak hanya hasil akhir

4. **Template Library:**
   - Pre-built templates by position
   - Community-contributed templates

5. **Batch Evaluation:**
   - Evaluate multiple submissions at once
   - Comparative analysis

---

## 📚 Technical Dependencies

### Backend:
- Laravel Storage (file management)
- PostgreSQL JSON fields
- Laravel Validation
- Queue jobs (untuk file processing)

### Frontend:
- File upload UI component
- Timer/countdown component
- Drag & drop file upload (optional)

### Optional Libraries:
- **PHPWord** - Parse .docx programmatically
- **ClamAV** - Virus scanning
- **League/Flysystem** - Advanced file management

---

## 🎬 Conclusion

Implementasi Document Editing Test Type akan:
✅ Menambah variasi assessment untuk posisi non-teknis
✅ Lebih practical & relevant untuk job role
✅ Menghemat waktu interview (pre-screening)
✅ Objektif scoring dengan rubrik jelas

**Estimated Development Time:** 3-4 weeks
**Complexity:** Medium
**Impact:** High (especially for admin/secretary roles)

**Next Steps:**
1. Review & approval dari stakeholders
2. Create database migrations
3. Start backend implementation
4. Parallel frontend development
5. Testing & deployment

---

**Status:** 📋 Proposal - Waiting for Approval
**Last Updated:** {{ date('Y-m-d') }}
**Author:** Development Team
