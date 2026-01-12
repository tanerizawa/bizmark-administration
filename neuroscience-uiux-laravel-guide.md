# Panduan Analisis Teknis Neuroscience UI/UX untuk Laravel Frontend
## Dokumentasi Lengkap untuk Agent AI

---

## 📋 DAFTAR ISI

1. [Pengantar Neuroscience UI/UX](#pengantar)
2. [Prinsip Neuroscience dalam UI/UX](#prinsip-neuroscience)
3. [Arsitektur Laravel untuk Neuroscience UI/UX](#arsitektur-laravel)
4. [Implementasi Teknis](#implementasi-teknis)
5. [Pattern Recognition untuk Agent AI](#pattern-recognition)
6. [Code Examples](#code-examples)
7. [Performance Optimization](#performance-optimization)
8. [Testing & Validation](#testing-validation)
9. [Best Practices](#best-practices)
10. [Troubleshooting Guide](#troubleshooting)

---

## 1. PENGANTAR NEUROSCIENCE UI/UX {#pengantar}

### 1.1 Definisi
Neuroscience UI/UX adalah pendekatan desain antarmuka yang berbasis pada pemahaman mendalam tentang bagaimana otak manusia memproses informasi visual, membuat keputusan, dan berinteraksi dengan sistem digital.

### 1.2 Tujuan Panduan untuk Agent AI
Panduan ini dirancang untuk membantu Agent AI:
- Mengidentifikasi pola neuroscience dalam desain
- Mengimplementasikan prinsip kognitif dalam kode
- Mengoptimalkan pengalaman pengguna berbasis sains
- Membuat keputusan desain yang data-driven

### 1.3 Fondasi Ilmiah
```
COGNITIVE LOAD THEORY
├── Working Memory: 7±2 items (Miller's Law)
├── Processing Speed: 200-300ms untuk keputusan visual
├── Attention Span: 8-12 detik untuk first impression
└── Decision Fatigue: Berkurang setelah 3-5 pilihan
```

---

## 2. PRINSIP NEUROSCIENCE DALAM UI/UX {#prinsip-neuroscience}

### 2.1 Visual Attention & Eye Tracking

#### F-Pattern Reading
```
Pola membaca natural manusia:
┌─────────────────────┐
│ ████████████        │  ← First horizontal scan
│ █████               │  ← Second horizontal scan
│ █                   │  
│ █                   │  ← Vertical scan
│ █                   │
└─────────────────────┘
```

**Implementasi di Laravel:**
- Hero section di kiri atas
- Call-to-action sejajar dengan pola F
- Konten penting di zona prioritas tinggi

#### Z-Pattern Layout
```
Untuk halaman dengan konten minimal:
┌─────────────────────┐
│ ███████████████████ │  ← Horizontal top
│         ╱           │
│       ╱             │  ← Diagonal
│     ╱               │
│ ███████████████████ │  ← Horizontal bottom
└─────────────────────┘
```

### 2.2 Cognitive Load Management

#### Hick's Law
**Formula:** RT = a + b log₂(n)
- RT = Reaction Time
- n = number of choices
- Semakin banyak pilihan → semakin lama keputusan

**Aplikasi Laravel:**
```php
// BAD: Terlalu banyak pilihan
<nav>
    @foreach($menuItems as $item) // 15+ items
        <a href="{{ $item->url }}">{{ $item->name }}</a>
    @endforeach
</nav>

// GOOD: Grup logis dengan dropdown
<nav>
    @foreach($menuGroups as $group) // 5-7 groups
        <div class="dropdown">
            <button>{{ $group->name }}</button>
            <div class="dropdown-content">
                @foreach($group->items as $item)
                    <a href="{{ $item->url }}">{{ $item->name }}</a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
```

### 2.3 Miller's Law (7±2 Rule)

Working memory capacity terbatas:
- **Optimal:** 5-9 item per grup
- **Ideal:** 7 item
- **Maximum:** Tidak lebih dari 9

**Implementasi:**
```php
// Controller
public function dashboard()
{
    $widgets = Widget::priority()
                     ->limit(7) // Miller's Law
                     ->get();
    
    return view('dashboard', compact('widgets'));
}
```

### 2.4 Gestalt Principles

#### Prinsip Proximity (Kedekatan)
```
Elemen yang berdekatan dianggap terkait:

[Button] [Button]     ← Grup 1
         
[Button] [Button]     ← Grup 2
```

#### Prinsip Similarity (Kesamaan)
```css
/* Elemen serupa = fungsi serupa */
.primary-action {
    background: #3b82f6;
    color: white;
    /* Semua primary action konsisten */
}

.secondary-action {
    background: transparent;
    border: 2px solid #3b82f6;
    /* Semua secondary action konsisten */
}
```

#### Prinsip Closure (Penutupan)
```
Otak melengkapi pola yang tidak lengkap:
○○○○○
○    ○   ← Dilihat sebagai kotak
○    ○      meski tidak lengkap
○○○○○
```

### 2.5 Color Psychology & Neural Response

#### Respon Neurologis terhadap Warna

**Merah (#FF0000)**
- Meningkatkan heart rate
- Memicu urgency & attention
- Gunakan untuk: Error, warning, CTA urgent

**Biru (#0066CC)**
- Menurunkan blood pressure
- Meningkatkan trust & calm
- Gunakan untuk: Corporate, healthcare, finance

**Hijau (#00AA00)**
- Relaxation response
- Positive reinforcement
- Gunakan untuk: Success, eco-friendly, growth

**Kuning (#FFCC00)**
- Meningkatkan alertness
- Stimulasi mental
- Gunakan untuk: Highlights, caution

**Implementation:**
```php
// config/neuroscience.php
return [
    'color_psychology' => [
        'primary_action' => '#3b82f6',  // Blue - Trust
        'success' => '#10b981',         // Green - Positive
        'warning' => '#f59e0b',         // Yellow - Caution
        'danger' => '#ef4444',          // Red - Urgent
        'neutral' => '#6b7280',         // Gray - Stable
    ],
    
    'cognitive_load' => [
        'max_menu_items' => 7,
        'max_form_fields_visible' => 5,
        'ideal_paragraph_length' => 3, // sentences
    ],
];
```

### 2.6 Temporal Neuroscience (Timing & Speed)

#### Critical Response Times

```
NEURAL RESPONSE THRESHOLDS:
├── 0-100ms: Instant (seamless)
├── 100-300ms: Acceptable (smooth)
├── 300-1000ms: Noticeable delay (needs feedback)
└── 1000ms+: User frustration begins
```

**Laravel Implementation:**
```php
// Middleware untuk monitoring response time
namespace App\Http\Middleware;

class NeuralResponseTime
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = (microtime(true) - $start) * 1000; // ms
        
        // Neuroscience threshold check
        if ($duration > 300) {
            \Log::warning('Slow response detected', [
                'url' => $request->url(),
                'duration' => $duration,
                'threshold' => 'exceeded_neural_comfort'
            ]);
        }
        
        $response->headers->set('X-Response-Time', $duration);
        return $response;
    }
}
```

---

## 3. ARSITEKTUR LARAVEL UNTUK NEUROSCIENCE UI/UX {#arsitektur-laravel}

### 3.1 Struktur Folder Optimal

```
laravel-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── UX/
│   │   │       ├── CognitiveLoadController.php
│   │   │       ├── VisualHierarchyController.php
│   │   │       └── UserBehaviorController.php
│   │   └── Middleware/
│   │       ├── NeuralResponseTime.php
│   │       └── CognitiveLoadLimiter.php
│   ├── Services/
│   │   ├── NeuroscienceService.php
│   │   ├── AttentionAnalyzer.php
│   │   └── DecisionSimplifier.php
│   └── View/
│       └── Components/
│           ├── CognitiveCard.php
│           ├── AttentionButton.php
│           └── ProgressiveDisclosure.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── neuro-base.blade.php
│   │   │   └── f-pattern.blade.php
│   │   └── components/
│   │       ├── cognitive-load/
│   │       ├── visual-hierarchy/
│   │       └── attention-elements/
│   ├── css/
│   │   ├── neuroscience/
│   │   │   ├── cognitive.css
│   │   │   ├── visual-weight.css
│   │   │   └── attention.css
│   └── js/
│       ├── neuroscience/
│       │   ├── attention-tracker.js
│       │   ├── cognitive-analyzer.js
│       │   └── decision-helper.js
├── config/
│   └── neuroscience.php
└── database/
    └── migrations/
        └── xxxx_create_user_behavior_analytics.php
```

### 3.2 Service Layer Architecture

```php
// app/Services/NeuroscienceService.php
<?php

namespace App\Services;

class NeuroscienceService
{
    /**
     * Analisis cognitive load pada halaman
     */
    public function analyzeCognitiveLoad($pageElements): array
    {
        $complexity = 0;
        
        // Hitung berdasarkan jumlah elemen
        $complexity += count($pageElements['buttons']) * 2;
        $complexity += count($pageElements['links']) * 1;
        $complexity += count($pageElements['forms']) * 5;
        $complexity += count($pageElements['images']) * 1.5;
        
        // Evaluasi berdasarkan threshold neuroscience
        $status = match(true) {
            $complexity <= 20 => 'optimal',
            $complexity <= 40 => 'acceptable',
            $complexity <= 60 => 'high',
            default => 'overload'
        };
        
        return [
            'complexity_score' => $complexity,
            'status' => $status,
            'recommendation' => $this->getRecommendation($status),
            'neural_comfort_level' => $this->calculateComfort($complexity)
        ];
    }
    
    /**
     * Optimalkan urutan elemen untuk attention flow
     */
    public function optimizeAttentionFlow(array $elements): array
    {
        // Sort berdasarkan visual weight dan importance
        usort($elements, function($a, $b) {
            $weightA = $this->calculateVisualWeight($a);
            $weightB = $this->calculateVisualWeight($b);
            
            return $weightB <=> $weightA;
        });
        
        // Apply F-pattern positioning
        return $this->applyFPattern($elements);
    }
    
    /**
     * Hitung visual weight berdasarkan prinsip neuroscience
     */
    private function calculateVisualWeight($element): float
    {
        $weight = 0;
        
        // Size matters untuk attention
        $weight += ($element['width'] * $element['height']) / 1000;
        
        // Color contrast affects neural attention
        $weight += $this->getColorContrastWeight($element['color']);
        
        // Position dalam viewport
        $weight += $this->getPositionWeight($element['position']);
        
        // Movement attracts attention
        if ($element['animated']) {
            $weight += 10;
        }
        
        return $weight;
    }
    
    /**
     * Simplifikasi decision tree
     */
    public function simplifyDecisions(array $options): array
    {
        // Aplikasi Hick's Law
        if (count($options) > 7) {
            return $this->groupOptionsByCategory($options);
        }
        
        return $options;
    }
    
    private function groupOptionsByCategory(array $options): array
    {
        $grouped = [];
        
        foreach ($options as $option) {
            $category = $option['category'] ?? 'other';
            $grouped[$category][] = $option;
        }
        
        return $grouped;
    }
}
```

### 3.3 Blade Components dengan Neuroscience Principles

```php
// app/View/Components/CognitiveCard.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CognitiveCard extends Component
{
    public $title;
    public $priority; // high, medium, low
    public $cognitiveWeight;
    
    public function __construct($title, $priority = 'medium')
    {
        $this->title = $title;
        $this->priority = $priority;
        $this->cognitiveWeight = $this->calculateCognitiveWeight();
    }
    
    private function calculateCognitiveWeight(): string
    {
        // Berdasarkan priority, tentukan visual prominence
        return match($this->priority) {
            'high' => 'font-bold text-xl border-4 border-blue-500',
            'medium' => 'font-medium text-lg border-2 border-gray-300',
            'low' => 'font-normal text-base border border-gray-200',
        };
    }
    
    public function render()
    {
        return view('components.cognitive-card');
    }
}
```

```blade
<!-- resources/views/components/cognitive-card.blade.php -->
<div class="cognitive-card {{ $cognitiveWeight }}" 
     data-priority="{{ $priority }}"
     data-neural-weight="{{ $priority === 'high' ? '10' : ($priority === 'medium' ? '5' : '2') }}">
    
    <!-- Visual Hierarchy berdasarkan Neural Processing -->
    <div class="card-header">
        <h3 class="title">{{ $title }}</h3>
    </div>
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    <!-- Progressive Disclosure untuk mengurangi cognitive load -->
    <div class="card-footer hidden" data-progressive-disclosure>
        <button class="expand-details" 
                onclick="this.parentElement.classList.toggle('hidden')">
            Lihat Detail
        </button>
    </div>
</div>

<style>
/* Visual weight berdasarkan priority */
.cognitive-card[data-priority="high"] {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    /* Menonjol untuk menarik attention neural */
}

.cognitive-card[data-priority="medium"] {
    transform: scale(1.0);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.cognitive-card[data-priority="low"] {
    opacity: 0.85;
    /* Subtle untuk mengurangi distraction */
}
</style>
```

---

## 4. IMPLEMENTASI TEKNIS {#implementasi-teknis}

### 4.1 Layout dengan F-Pattern

```blade
<!-- resources/views/layouts/neuro-base.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Neuroscience Optimized</title>
    
    <!-- Preload critical resources untuk neural comfort -->
    <link rel="preload" href="{{ asset('css/critical-above-fold.css') }}" as="style">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="f-pattern-layout">
    
    <!-- ZONA 1: Top-Left (Primary Attention) -->
    <header class="f-zone-1">
        <div class="container">
            <!-- Logo & Brand (Max 3 detik untuk recognition) -->
            <div class="brand-identity">
                <img src="{{ asset('logo.svg') }}" 
                     alt="Logo" 
                     width="120" 
                     height="40"
                     loading="eager"> <!-- Critical for first impression -->
            </div>
            
            <!-- Primary Navigation (Max 7 items per Miller's Law) -->
            <nav class="primary-nav" data-cognitive-load="low">
                @foreach($mainMenuItems->take(7) as $item)
                    <a href="{{ $item->url }}" 
                       class="nav-item"
                       data-neural-priority="{{ $item->priority }}">
                        {{ $item->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>
    
    <!-- ZONA 2: First Horizontal Scan Line -->
    <section class="f-zone-2 hero-section">
        <div class="container">
            <!-- Main Value Proposition (Dalam 8 detik first impression) -->
            <h1 class="headline" data-readability-optimal="6-8-words">
                @yield('hero-headline')
            </h1>
            
            <!-- Primary CTA (Singular focus = reduces decision fatigue) -->
            <div class="cta-primary">
                @yield('primary-cta')
            </div>
        </div>
    </section>
    
    <!-- ZONA 3: Vertical Scan (Left Side Content) -->
    <main class="f-zone-3" role="main">
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                
                <!-- Left Column: Primary Content -->
                <div class="col-span-8">
                    @yield('primary-content')
                </div>
                
                <!-- Right Column: Supporting Info -->
                <aside class="col-span-4" data-cognitive-load="minimal">
                    @yield('sidebar')
                </aside>
                
            </div>
        </div>
    </main>
    
    <!-- ZONA 4: Bottom Horizontal Scan -->
    <footer class="f-zone-4">
        <div class="container">
            <!-- Secondary CTAs atau Important Links -->
            @yield('footer-cta')
        </div>
    </footer>
    
    <!-- Attention Tracking Script -->
    <script>
        // Track attention patterns untuk optimization
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Log attention data
                        const element = entry.target;
                        const zone = element.dataset.fZone;
                        const timestamp = Date.now();
                        
                        sendAttentionData({
                            zone: zone,
                            element: element.className,
                            timestamp: timestamp,
                            duration: entry.intersectionRatio
                        });
                    }
                });
            }, {
                threshold: [0.5, 0.75, 1.0]
            });
            
            // Observe F-pattern zones
            document.querySelectorAll('[class*="f-zone-"]').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
```

### 4.2 Form Design dengan Cognitive Load Management

```php
// app/Http/Controllers/UX/FormController.php
<?php

namespace App\Http\Controllers\UX;

use App\Http\Controllers\Controller;
use App\Services\NeuroscienceService;

class FormController extends Controller
{
    protected $neuroService;
    
    public function __construct(NeuroscienceService $neuroService)
    {
        $this->neuroService = $neuroService;
    }
    
    public function showRegistrationForm()
    {
        // Progressive Disclosure: Tampilkan hanya essential fields
        $essentialFields = [
            'name' => ['type' => 'text', 'priority' => 'high'],
            'email' => ['type' => 'email', 'priority' => 'high'],
            'password' => ['type' => 'password', 'priority' => 'high'],
        ];
        
        // Optional fields disembunyikan untuk reduce cognitive load
        $optionalFields = [
            'phone' => ['type' => 'tel', 'priority' => 'low'],
            'address' => ['type' => 'textarea', 'priority' => 'low'],
            'preferences' => ['type' => 'checkboxes', 'priority' => 'low'],
        ];
        
        return view('forms.registration', [
            'essentialFields' => $essentialFields,
            'optionalFields' => $optionalFields,
            'cognitiveLoad' => $this->neuroService->analyzeCognitiveLoad([
                'forms' => 1,
                'fields' => count($essentialFields)
            ])
        ]);
    }
}
```

```blade
<!-- resources/views/forms/registration.blade.php -->
<form method="POST" 
      action="{{ route('register') }}" 
      class="neuro-form"
      data-cognitive-load="{{ $cognitiveLoad['status'] }}">
    
    @csrf
    
    <!-- Step Indicator (Reduces uncertainty, improves completion) -->
    <div class="progress-indicator" role="progressbar" aria-valuenow="1" aria-valuemax="3">
        <div class="step active" data-step="1">Informasi Dasar</div>
        <div class="step" data-step="2">Detail Akun</div>
        <div class="step" data-step="3">Selesai</div>
    </div>
    
    <!-- Step 1: Essential Fields Only (Miller's Law: Max 7) -->
    <div class="form-step active" data-step="1">
        <h2 class="step-title">Informasi Dasar</h2>
        
        @foreach($essentialFields as $name => $field)
            <div class="form-group" data-priority="{{ $field['priority'] }}">
                <label for="{{ $name }}" class="form-label">
                    {{ ucfirst($name) }}
                    @if($field['priority'] === 'high')
                        <span class="required-indicator" aria-label="required">*</span>
                    @endif
                </label>
                
                <input type="{{ $field['type'] }}" 
                       id="{{ $name }}" 
                       name="{{ $name }}"
                       class="form-input"
                       aria-required="{{ $field['priority'] === 'high' ? 'true' : 'false' }}"
                       @if($field['priority'] === 'high') required @endif>
                
                <!-- Real-time Validation (Immediate feedback reduces cognitive load) -->
                <span class="validation-feedback" role="alert"></span>
            </div>
        @endforeach
        
        <button type="button" 
                class="btn-next" 
                onclick="nextStep()"
                data-neural-action="progress">
            Lanjutkan
        </button>
    </div>
    
    <!-- Progressive Disclosure: Optional Fields -->
    <div class="form-step" data-step="2" style="display: none;">
        <h2 class="step-title">Detail Tambahan (Opsional)</h2>
        
        <p class="helper-text">
            Informasi ini dapat diisi nanti di pengaturan profil
        </p>
        
        @foreach($optionalFields as $name => $field)
            <div class="form-group optional" data-priority="{{ $field['priority'] }}">
                <label for="{{ $name }}" class="form-label">
                    {{ ucfirst($name) }}
                </label>
                
                @if($field['type'] === 'textarea')
                    <textarea id="{{ $name }}" 
                              name="{{ $name }}"
                              class="form-input"
                              rows="3"></textarea>
                @else
                    <input type="{{ $field['type'] }}" 
                           id="{{ $name }}" 
                           name="{{ $name }}"
                           class="form-input">
                @endif
            </div>
        @endforeach
        
        <div class="form-actions">
            <button type="button" class="btn-back" onclick="prevStep()">
                Kembali
            </button>
            <button type="button" class="btn-next" onclick="nextStep()">
                Lanjutkan
            </button>
        </div>
    </div>
    
    <!-- Step 3: Confirmation (Reduces anxiety) -->
    <div class="form-step" data-step="3" style="display: none;">
        <h2 class="step-title">Konfirmasi</h2>
        
        <div class="confirmation-summary">
            <!-- Show summary to reduce cognitive verification load -->
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn-back" onclick="prevStep()">
                Kembali
            </button>
            <button type="submit" 
                    class="btn-submit"
                    data-neural-action="complete">
                Selesai
            </button>
        </div>
    </div>
</form>

<script>
// Multi-step form logic dengan neuroscience timing
let currentStep = 1;
const totalSteps = 3;

function nextStep() {
    if (validateCurrentStep()) {
        // Hide current step
        document.querySelector(`.form-step[data-step="${currentStep}"]`).style.display = 'none';
        
        // Show next step with smooth transition (reduces jarring neural response)
        currentStep++;
        const nextStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        nextStepEl.style.display = 'block';
        nextStepEl.style.opacity = '0';
        
        // Fade in (300ms = optimal neural comfort)
        setTimeout(() => {
            nextStepEl.style.transition = 'opacity 300ms ease-in-out';
            nextStepEl.style.opacity = '1';
        }, 10);
        
        updateProgressIndicator();
        
        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    document.querySelector(`.form-step[data-step="${currentStep}"]`).style.display = 'none';
    currentStep--;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).style.display = 'block';
    updateProgressIndicator();
}

function validateCurrentStep() {
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    const requiredFields = currentStepEl.querySelectorAll('[required]');
    
    let isValid = true;
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            // Show validation error with color psychology (red = attention)
            const feedback = field.nextElementSibling;
            feedback.textContent = 'Field ini wajib diisi';
            feedback.classList.add('error');
            field.classList.add('invalid');
            isValid = false;
        }
    });
    
    return isValid;
}

function updateProgressIndicator() {
    document.querySelectorAll('.progress-indicator .step').forEach((step, index) => {
        if (index + 1 <= currentStep) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

// Real-time validation untuk reduce cognitive load
document.querySelectorAll('.form-input').forEach(input => {
    input.addEventListener('blur', function() {
        // Validate after 200ms (optimal neural processing time)
        setTimeout(() => {
            if (this.required && !this.value.trim()) {
                const feedback = this.nextElementSibling;
                feedback.textContent = 'Field ini wajib diisi';
                feedback.classList.add('error');
                this.classList.add('invalid');
            } else {
                const feedback = this.nextElementSibling;
                feedback.textContent = '';
                feedback.classList.remove('error');
                this.classList.remove('invalid');
            }
        }, 200);
    });
});
</script>

<style>
/* Neuroscience-based form styling */
.neuro-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

/* Progress indicator untuk reduce uncertainty */
.progress-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.progress-indicator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e5e7eb;
    z-index: -1;
}

.progress-indicator .step {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6b7280;
    transition: all 300ms ease; /* Neural comfort timing */
}

.progress-indicator .step.active {
    background: #3b82f6; /* Blue = trust & progress */
    border-color: #3b82f6;
    color: white;
}

/* Form groups dengan visual hierarchy */
.form-group[data-priority="high"] {
    margin-bottom: 1.5rem;
}

.form-group[data-priority="low"] {
    margin-bottom: 1rem;
    opacity: 0.9; /* Subtle untuk reduce visual noise */
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.required-indicator {
    color: #ef4444; /* Red = attention untuk required */
    margin-left: 0.25rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: border-color 200ms ease; /* Quick neural feedback */
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6; /* Blue = focused attention */
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.invalid {
    border-color: #ef4444; /* Red = error attention */
}

.validation-feedback {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    min-height: 1.25rem; /* Prevent layout shift */
}

.validation-feedback.error {
    color: #ef4444;
}

/* Buttons dengan action hierarchy */
.btn-next,
.btn-submit {
    background: #3b82f6; /* Primary blue */
    color: white;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 200ms ease;
}

.btn-next:hover,
.btn-submit:hover {
    background: #2563eb;
    transform: translateY(-2px); /* Subtle lift = clickable */
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
}

.btn-back {
    background: transparent;
    color: #6b7280;
    padding: 0.75rem 2rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    margin-right: 1rem;
}

/* Helper text untuk reduce confusion */
.helper-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    font-style: italic;
}
</style>
```

### 4.3 Navigation dengan Cognitive Load Optimization

```php
// app/Http/Controllers/NavigationController.php
<?php

namespace App\Http\Controllers;

use App\Services\NeuroscienceService;

class NavigationController extends Controller
{
    public function getOptimizedMenu()
    {
        $allMenuItems = MenuItem::all();
        
        // Group menu items untuk reduce cognitive overload
        $neuroService = app(NeuroscienceService::class);
        $optimizedMenu = $neuroService->simplifyDecisions($allMenuItems->toArray());
        
        // Apply visual weight
        foreach ($optimizedMenu as &$item) {
            $item['visual_weight'] = $this->calculateVisualWeight($item);
        }
        
        return response()->json([
            'menu' => $optimizedMenu,
            'cognitive_analysis' => $neuroService->analyzeCognitiveLoad([
                'links' => count($allMenuItems)
            ])
        ]);
    }
    
    private function calculateVisualWeight($item): int
    {
        $weight = 0;
        
        // Priority items get more visual weight
        if ($item['is_primary']) {
            $weight += 5;
        }
        
        // Frequent access = more prominence
        $weight += min($item['click_count'] / 100, 3);
        
        return $weight;
    }
}
```

```blade
<!-- resources/views/components/neuro-navigation.blade.php -->
<nav class="neuro-nav" 
     data-cognitive-load="optimized"
     data-max-items="7">
    
    <!-- Primary Navigation (Max 7 items untuk Miller's Law) -->
    <ul class="primary-menu">
        @foreach($primaryMenuItems as $item)
            <li class="menu-item" 
                data-visual-weight="{{ $item['visual_weight'] }}"
                style="order: {{ $item['visual_weight'] }};">
                
                <a href="{{ $item['url'] }}" 
                   class="menu-link"
                   @if($item['is_current']) aria-current="page" @endif>
                    
                    <!-- Icon untuk quick recognition (visual processing faster) -->
                    @if($item['icon'])
                        <span class="menu-icon">
                            <i class="{{ $item['icon'] }}"></i>
                        </span>
                    @endif
                    
                    <span class="menu-text">{{ $item['name'] }}</span>
                    
                    <!-- Badge untuk urgent attention -->
                    @if($item['has_notification'])
                        <span class="notification-badge" 
                              style="background-color: #ef4444;"> <!-- Red = urgent -->
                            {{ $item['notification_count'] }}
                        </span>
                    @endif
                </a>
                
                <!-- Mega menu dengan progressive disclosure -->
                @if($item['has_submenu'])
                    <div class="mega-menu" data-submenu="{{ $item['id'] }}">
                        <div class="mega-menu-grid">
                            @foreach($item['submenu_groups'] as $group)
                                <div class="menu-group">
                                    <h4 class="group-title">{{ $group['name'] }}</h4>
                                    <ul class="group-items">
                                        @foreach($group['items']->take(5) as $subItem)
                                            <li>
                                                <a href="{{ $subItem['url'] }}">
                                                    {{ $subItem['name'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
    
    <!-- Overflow menu untuk items beyond 7 -->
    @if($overflowMenuItems->isNotEmpty())
        <div class="overflow-menu">
            <button class="more-button" 
                    aria-label="More menu items"
                    onclick="toggleOverflowMenu()">
                <span>More</span>
                <i class="icon-chevron-down"></i>
            </button>
            
            <div class="overflow-dropdown" hidden>
                <ul>
                    @foreach($overflowMenuItems as $item)
                        <li>
                            <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</nav>

<script>
// Attention tracking pada navigation
const nav = document.querySelector('.neuro-nav');
const menuItems = nav.querySelectorAll('.menu-item');

// Track hover patterns untuk optimization
menuItems.forEach(item => {
    let hoverStart;
    
    item.addEventListener('mouseenter', function() {
        hoverStart = Date.now();
        
        // Preload submenu content jika ada
        const submenu = this.querySelector('.mega-menu');
        if (submenu) {
            // Delay 200ms = optimal untuk prevent accidental triggers
            setTimeout(() => {
                submenu.style.display = 'block';
                submenu.style.opacity = '0';
                setTimeout(() => {
                    submenu.style.transition = 'opacity 200ms ease';
                    submenu.style.opacity = '1';
                }, 10);
            }, 200);
        }
    });
    
    item.addEventListener('mouseleave', function() {
        const hoverDuration = Date.now() - hoverStart;
        
        // Log engagement data
        if (hoverDuration > 500) { // Significant interest
            sendEngagementData({
                item: this.querySelector('.menu-text').textContent,
                duration: hoverDuration,
                timestamp: Date.now()
            });
        }
        
        // Hide submenu
        const submenu = this.querySelector('.mega-menu');
        if (submenu) {
            submenu.style.opacity = '0';
            setTimeout(() => {
                submenu.style.display = 'none';
            }, 200);
        }
    });
});

function toggleOverflowMenu() {
    const dropdown = document.querySelector('.overflow-dropdown');
    const isHidden = dropdown.hasAttribute('hidden');
    
    if (isHidden) {
        dropdown.removeAttribute('hidden');
        dropdown.style.opacity = '0';
        setTimeout(() => {
            dropdown.style.transition = 'opacity 200ms ease';
            dropdown.style.opacity = '1';
        }, 10);
    } else {
        dropdown.style.opacity = '0';
        setTimeout(() => {
            dropdown.setAttribute('hidden', '');
        }, 200);
    }
}
</script>

<style>
/* Navigation dengan visual hierarchy */
.neuro-nav {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 0;
}

.primary-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.menu-item {
    position: relative;
}

/* Visual weight affects prominence */
.menu-item[data-visual-weight="8"],
.menu-item[data-visual-weight="9"],
.menu-item[data-visual-weight="10"] {
    order: -1; /* Appear first */
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    color: #374151;
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 200ms ease; /* Neural comfort timing */
}

.menu-link:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.menu-link[aria-current="page"] {
    background: #eff6ff; /* Light blue = current context */
    color: #2563eb;
    font-weight: 600;
}

/* Icon untuk faster recognition */
.menu-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Notification badge = urgent attention */
.notification-badge {
    background: #ef4444; /* Red = urgent */
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    min-width: 20px;
    text-align: center;
}

/* Mega menu dengan progressive disclosure */
.mega-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    min-width: 600px;
    display: none;
    z-index: 1000;
}

.mega-menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.menu-group .group-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.menu-group .group-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-group .group-items li {
    margin-bottom: 0.5rem;
}

.menu-group .group-items a {
    color: #6b7280;
    text-decoration: none;
    font-size: 0.9375rem;
    transition: color 150ms ease;
}

.menu-group .group-items a:hover {
    color: #2563eb;
}
</style>
```

---

## 5. PATTERN RECOGNITION UNTUK AGENT AI {#pattern-recognition}

### 5.1 Decision Tree untuk Agent AI

```
NEUROSCIENCE UI/UX DECISION TREE
│
├── Apakah halaman memiliki > 7 elemen interaktif?
│   ├── YES → Apply grouping/categorization (Hick's Law)
│   └── NO → Display direct
│
├── Apakah form memiliki > 5 fields?
│   ├── YES → Implement multi-step/progressive disclosure
│   └── NO → Single-step form OK
│
├── Apakah ada data prioritas tinggi?
│   ├── YES → Position di F-pattern zone 1 (top-left)
│   └── NO → Standard hierarchy
│
├── Apakah response time > 300ms?
│   ├── YES → Add loading indicator + skeleton screen
│   └── NO → Direct render
│
├── Apakah ada pilihan > 3 options?
│   ├── YES → Visual differentiation required
│   └── NO → Simple layout OK
│
└── Apakah target audience technical/non-technical?
    ├── Technical → More info, dense layout acceptable
    └── Non-technical → Minimal, guided experience
```

### 5.2 Pattern Recognition Algorithms

```php
// app/Services/AttentionAnalyzer.php
<?php

namespace App\Services;

class AttentionAnalyzer
{
    /**
     * Analisis attention pattern dari user behavior
     */
    public function analyzeAttentionPattern(array $heatmapData): array
    {
        $patterns = [];
        
        // Deteksi F-pattern
        if ($this->detectFPattern($heatmapData)) {
            $patterns[] = 'f-pattern';
        }
        
        // Deteksi Z-pattern
        if ($this->detectZPattern($heatmapData)) {
            $patterns[] = 'z-pattern';
        }
        
        // Deteksi scattered attention (cognitive overload indicator)
        if ($this->detectScatteredAttention($heatmapData)) {
            $patterns[] = 'scattered';
            $recommendation = 'Reduce cognitive load - too many attention points';
        }
        
        return [
            'detected_patterns' => $patterns,
            'dominant_pattern' => $patterns[0] ?? 'undefined',
            'attention_efficiency' => $this->calculateAttentionEfficiency($heatmapData),
            'recommendation' => $recommendation ?? 'Pattern optimal'
        ];
    }
    
    private function detectFPattern(array $data): bool
    {
        // Check untuk concentration di:
        // 1. Top horizontal (first 100px)
        // 2. Second horizontal (around 300px)
        // 3. Left vertical line
        
        $topConcentration = $this->getConcentrationInArea($data, [
            'y' => [0, 100],
            'x' => [0, '100%']
        ]);
        
        $leftConcentration = $this->getConcentrationInArea($data, [
            'x' => [0, 200],
            'y' => [0, '100%']
        ]);
        
        return ($topConcentration > 0.3 && $leftConcentration > 0.4);
    }
    
    private function detectZPattern(array $data): bool
    {
        // Z-pattern: top-left → top-right → bottom-left → bottom-right
        $topLeft = $this->getConcentrationInQuadrant($data, 'top-left');
        $topRight = $this->getConcentrationInQuadrant($data, 'top-right');
        $bottomLeft = $this->getConcentrationInQuadrant($data, 'bottom-left');
        $bottomRight = $this->getConcentrationInQuadrant($data, 'bottom-right');
        
        return (
            $topLeft > 0.2 &&
            $topRight > 0.2 &&
            $bottomRight > 0.15
        );
    }
    
    private function detectScatteredAttention(array $data): bool
    {
        // Banyak fixation points tanpa flow yang jelas = cognitive overload
        $fixationPoints = count($data);
        $avgDuration = array_sum(array_column($data, 'duration')) / $fixationPoints;
        
        // Jika > 20 fixation points dengan avg duration < 500ms = scattered
        return ($fixationPoints > 20 && $avgDuration < 500);
    }
    
    private function calculateAttentionEfficiency(array $data): float
    {
        // Efficiency = seberapa cepat user menemukan target
        // Lower score = more efficient (less cognitive work)
        
        $totalFixations = count($data);
        $totalDuration = array_sum(array_column($data, 'duration'));
        $pathLength = $this->calculatePathLength($data);
        
        // Normalize to 0-100 scale
        $efficiency = 100 - (($totalFixations * 2) + ($pathLength / 100));
        
        return max(0, min(100, $efficiency));
    }
    
    private function calculatePathLength(array $data): float
    {
        $length = 0;
        
        for ($i = 1; $i < count($data); $i++) {
            $prev = $data[$i - 1];
            $curr = $data[$i];
            
            // Euclidean distance
            $length += sqrt(
                pow($curr['x'] - $prev['x'], 2) +
                pow($curr['y'] - $prev['y'], 2)
            );
        }
        
        return $length;
    }
}
```

### 5.3 AI Agent Instructions for Implementation

```markdown
## INSTRUCTIONS FOR AI AGENT: IMPLEMENTING NEUROSCIENCE UI/UX

### PRIORITY CHECKLIST

When generating Laravel frontend code, ALWAYS:

1. **CHECK COGNITIVE LOAD**
   ```
   IF elements_count > 7 THEN
       Apply grouping/categorization
   END IF
   ```

2. **VERIFY VISUAL HIERARCHY**
   ```
   - Primary action = Largest, Highest contrast
   - Secondary action = Medium prominence
   - Tertiary = Subtle
   ```

3. **APPLY COLOR PSYCHOLOGY**
   ```
   - CTA buttons = Blue (#3b82f6) for trust
   - Success states = Green (#10b981)
   - Warnings = Yellow (#f59e0b)
   - Errors = Red (#ef4444)
   - Cancel/Back = Gray (#6b7280)
   ```

4. **IMPLEMENT RESPONSE TIME STANDARDS**
   ```
   IF response_time > 300ms THEN
       Add loading indicator
       Use skeleton screen
   END IF
   ```

5. **PROGRESSIVE DISCLOSURE**
   ```
   IF form_fields > 5 THEN
       Split into steps
       Show essential fields first
   END IF
   ```

### CODE GENERATION RULES

#### Rule 1: Always Add Data Attributes for Neural Analysis
```blade
<button class="btn-primary" 
        data-neural-priority="high"
        data-cognitive-weight="8"
        data-action-type="primary">
    Submit
</button>
```

#### Rule 2: Include Timing Transitions
```css
/* All transitions should be 200-300ms for neural comfort */
.element {
    transition: all 250ms ease-in-out;
}
```

#### Rule 3: Implement Visual Feedback
```javascript
// Every user action needs immediate feedback (<100ms)
button.addEventListener('click', function() {
    this.classList.add('active'); // Instant visual response
    
    // Then process action
    setTimeout(() => {
        processAction();
    }, 0);
});
```

#### Rule 4: Limit Choices
```php
// Good: Grouped menu
$menuGroups = MenuItem::all()->groupBy('category')->take(7);

// Bad: Flat list
$allItems = MenuItem::all(); // Could be 50+ items
```

#### Rule 5: F-Pattern Layout Structure
```blade
<div class="page-layout">
    <!-- Zone 1: Top-Left (Primary attention) -->
    <header class="f-zone-1">
        <h1>Primary Headline</h1>
        <button class="cta-primary">Main Action</button>
    </header>
    
    <!-- Zone 2: Horizontal scan -->
    <section class="f-zone-2">
        <p>Key value proposition</p>
    </section>
    
    <!-- Zone 3: Left vertical -->
    <main class="f-zone-3">
        <div class="primary-content">
            <!-- Main content -->
        </div>
        <aside class="secondary-content">
            <!-- Supporting info -->
        </aside>
    </main>
</div>
```

### ERROR PATTERNS TO AVOID

❌ **DON'T:**
- Create forms with 10+ fields on one screen
- Use more than 3 different font sizes on one page
- Implement buttons without clear visual hierarchy
- Use animations longer than 400ms
- Create navigation with 15+ top-level items
- Use pure black text on pure white (#000 on #FFF)

✅ **DO:**
- Break long forms into steps
- Limit font sizes to 3 max (heading, body, caption)
- Make primary button visually dominant
- Keep animations to 200-300ms
- Limit navigation to 7 items, group the rest
- Use #374151 on #FFFFFF for better readability

### VALIDATION CHECKLIST BEFORE CODE COMPLETION

Before submitting generated code, verify:

- [ ] Cognitive load score < 40
- [ ] Menu items ≤ 7 per level
- [ ] Form fields ≤ 5 per step
- [ ] All transitions 200-300ms
- [ ] Primary CTA is visually dominant
- [ ] Color contrast ratio ≥ 4.5:1
- [ ] Loading states for >300ms operations
- [ ] Data attributes for analytics
- [ ] Progressive disclosure implemented
- [ ] F-pattern or Z-pattern structure

### EXAMPLE: COMPLETE PAGE GENERATION

When asked to create a dashboard page:

```php
// Controller
public function dashboard()
{
    $neuroService = app(NeuroscienceService::class);
    
    // Limit widgets to 7 (Miller's Law)
    $widgets = Widget::priority()->limit(7)->get();
    
    // Analyze cognitive load
    $cognitiveLoad = $neuroService->analyzeCognitiveLoad([
        'widgets' => count($widgets),
        'buttons' => 3,
        'links' => 5
    ]);
    
    return view('dashboard', compact('widgets', 'cognitiveLoad'));
}
```

```blade
<!-- View -->
@extends('layouts.neuro-base')

@section('content')
<div class="dashboard-layout f-pattern">
    
    <!-- F-Zone 1: Primary attention -->
    <header class="dashboard-header">
        <h1 class="text-3xl font-bold">Welcome Back, {{ auth()->user()->name }}</h1>
        <button class="btn-primary" data-neural-priority="high">
            New Project
        </button>
    </header>
    
    <!-- F-Zone 2: Key metrics -->
    <section class="metrics-row">
        @foreach($widgets->take(3) as $widget)
            <x-cognitive-card 
                :title="$widget->title"
                :priority="$widget->priority">
                {{ $widget->content }}
            </x-cognitive-card>
        @endforeach
    </section>
    
    <!-- F-Zone 3: Detailed content -->
    <main class="content-area">
        <div class="primary-content">
            <!-- Main content with progressive disclosure -->
        </div>
        
        <aside class="secondary-content">
            <!-- Less important info -->
        </aside>
    </main>
    
</div>

<!-- Cognitive Load Indicator (for debugging) -->
@if(config('app.debug'))
    <div class="cognitive-indicator" 
         data-status="{{ $cognitiveLoad['status'] }}"
         data-score="{{ $cognitiveLoad['complexity_score'] }}">
        Cognitive Load: {{ $cognitiveLoad['status'] }}
    </div>
@endif
@endsection
```

### RESPONSE TO COMMON SCENARIOS

**Scenario 1: "Create a registration form"**
→ Generate multi-step form with max 5 fields per step
→ Include progress indicator
→ Add real-time validation
→ Use appropriate color psychology

**Scenario 2: "Design a product listing page"**
→ Implement F-pattern layout
→ Limit to 12-16 products per page
→ Add filters with max 7 top-level categories
→ Include visual hierarchy for featured products

**Scenario 3: "Build a navigation menu"**
→ Max 7 top-level items
→ Group additional items in "More" dropdown
→ Implement mega-menu for complex structures
→ Add visual weight based on priority

**Scenario 4: "Create a dashboard"**
→ Limit widgets to 7
→ Position highest priority in F-zone 1
→ Use progressive disclosure for details
→ Include cognitive load analysis

```

---

## 6. CODE EXAMPLES {#code-examples}

### 6.1 Complete Dashboard Example

```php
// routes/web.php
Route::middleware(['auth', 'neural.response.time'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// app/Http/Controllers/DashboardController.php
<?php

namespace App\Http\Controllers;

use App\Services\NeuroscienceService;
use App\Models\Widget;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $neuroService;
    
    public function __construct(NeuroscienceService $neuroService)
    {
        $this->neuroService = $neuroService;
    }
    
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get personalized widgets dengan cognitive load optimization
        $widgets = Widget::forUser($user)
                        ->priority()
                        ->limit(config('neuroscience.cognitive_load.max_widgets', 7))
                        ->get();
        
        // Analyze layout cognitive load
        $cognitiveAnalysis = $this->neuroService->analyzeCognitiveLoad([
            'widgets' => count($widgets),
            'buttons' => 4,
            'links' => 8,
            'forms' => 0
        ]);
        
        // Get attention-optimized layout
        $optimizedLayout = $this->neuroService->optimizeAttentionFlow(
            $widgets->toArray()
        );
        
        // Recent activities (limit 5 for quick scan)
        $recentActivities = $user->activities()
                                 ->latest()
                                 ->limit(5)
                                 ->get();
        
        return view('dashboard.index', [
            'widgets' => $optimizedLayout,
            'recentActivities' => $recentActivities,
            'cognitiveLoad' => $cognitiveAnalysis,
            'userName' => $user->name,
            'userRole' => $user->role
        ]);
    }
}

// resources/views/dashboard/index.blade.php
@extends('layouts.neuro-base')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container" 
     data-cognitive-load="{{ $cognitiveLoad['status'] }}"
     data-user-role="{{ $userRole }}">
    
    <!-- ZONA F-1: Top-Left Primary Attention -->
    <header class="dashboard-header f-zone-1">
        <div class="header-content">
            <!-- Personal greeting (increases engagement) -->
            <h1 class="headline" data-readability="optimal">
                Welcome back, <span class="user-name">{{ $userName }}</span>
            </h1>
            
            <!-- Primary CTA (Singular focus) -->
            <button class="btn-cta-primary" 
                    data-neural-priority="highest"
                    onclick="startNewProject()">
                <span class="icon">+</span>
                <span class="text">New Project</span>
            </button>
        </div>
        
        <!-- Quick stats (Max 3 for easy scan) -->
        <div class="quick-stats">
            <div class="stat-item" data-priority="high">
                <span class="stat-value">{{ $user->activeProjects->count() }}</span>
                <span class="stat-label">Active Projects</span>
            </div>
            <div class="stat-item" data-priority="medium">
                <span class="stat-value">{{ $user->pendingTasks->count() }}</span>
                <span class="stat-label">Pending Tasks</span>
            </div>
            <div class="stat-item" data-priority="low">
                <span class="stat-value">{{ $user->completedThisWeek->count() }}</span>
                <span class="stat-label">Completed This Week</span>
            </div>
        </div>
    </header>
    
    <!-- ZONA F-2: Horizontal Scan Area -->
    <section class="widgets-grid f-zone-2">
        @foreach($widgets as $widget)
            <x-cognitive-card 
                :title="$widget['title']"
                :priority="$widget['priority']"
                :data="$widget['data']"
                class="widget-card">
                
                @switch($widget['type'])
                    @case('chart')
                        <x-chart-widget :data="$widget['data']" />
                        @break
                    
                    @case('list')
                        <x-list-widget :items="$widget['data']" />
                        @break
                    
                    @case('metric')
                        <x-metric-widget :value="$widget['data']" />
                        @break
                @endswitch
                
            </x-cognitive-card>
        @endforeach
    </section>
    
    <!-- ZONA F-3: Left Vertical Content -->
    <div class="dashboard-main f-zone-3">
        
        <!-- Primary content area -->
        <main class="primary-content">
            <section class="recent-activity">
                <h2 class="section-title">Recent Activity</h2>
                
                <div class="activity-list" data-cognitive-limit="5">
                    @foreach($recentActivities as $activity)
                        <div class="activity-item" 
                             data-timestamp="{{ $activity->created_at->timestamp }}">
                            
                            <!-- Icon untuk quick recognition -->
                            <div class="activity-icon" 
                                 style="background-color: {{ $activity->iconColor }};">
                                <i class="{{ $activity->icon }}"></i>
                            </div>
                            
                            <div class="activity-content">
                                <p class="activity-text">{{ $activity->description }}</p>
                                <time class="activity-time" 
                                      datetime="{{ $activity->created_at->toISOString() }}">
                                    {{ $activity->created_at->diffForHumans() }}
                                </time>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Progressive disclosure untuk more activities -->
                <button class="load-more-activities" 
                        data-progressive-disclosure
                        onclick="loadMoreActivities()">
                    View All Activity
                </button>
            </section>
        </main>
        
        <!-- Supporting content sidebar -->
        <aside class="sidebar-content">
            
            <!-- Quick actions (Max 5) -->
            <section class="quick-actions">
                <h3 class="section-subtitle">Quick Actions</h3>
                <nav class="action-list">
                    <a href="{{ route('projects.create') }}" class="action-link">
                        <span class="action-icon">📁</span>
                        <span class="action-text">Create Project</span>
                    </a>
                    <a href="{{ route('tasks.create') }}" class="action-link">
                        <span class="action-icon">✓</span>
                        <span class="action-text">Add Task</span>
                    </a>
                    <a href="{{ route('team.invite') }}" class="action-link">
                        <span class="action-icon">👥</span>
                        <span class="action-text">Invite Team Member</span>
                    </a>
                </nav>
            </section>
            
            <!-- Notifications (Progressive disclosure) -->
            <section class="notifications">
                <h3 class="section-subtitle">
                    Notifications
                    @if($user->unreadNotifications->count() > 0)
                        <span class="notification-badge">
                            {{ $user->unreadNotifications->count() }}
                        </span>
                    @endif
                </h3>
                
                <div class="notification-list">
                    @forelse($user->unreadNotifications->take(3) as $notification)
                        <div class="notification-item">
                            <p class="notification-text">{{ $notification->data['message'] }}</p>
                            <time class="notification-time">
                                {{ $notification->created_at->diffForHumans() }}
                            </time>
                        </div>
                    @empty
                        <p class="empty-state">No new notifications</p>
                    @endforelse
                </div>
            </section>
            
        </aside>
    </div>
    
</div>

<!-- Cognitive Load Debug Panel (Only in development) -->
@if(config('app.debug'))
    <div class="cognitive-debug-panel">
        <h4>Neuroscience Analysis</h4>
        <dl>
            <dt>Cognitive Load:</dt>
            <dd class="status-{{ $cognitiveLoad['status'] }}">
                {{ $cognitiveLoad['status'] }} ({{ $cognitiveLoad['complexity_score'] }})
            </dd>
            
            <dt>Neural Comfort:</dt>
            <dd>{{ $cognitiveLoad['neural_comfort_level'] }}%</dd>
            
            <dt>Recommendation:</dt>
            <dd>{{ $cognitiveLoad['recommendation'] }}</dd>
        </dl>
    </div>
@endif
@endsection

@push('scripts')
<script>
// Attention tracking & analytics
document.addEventListener('DOMContentLoaded', function() {
    // Track time spent on dashboard
    const startTime = Date.now();
    
    // Track widget interactions
    document.querySelectorAll('.widget-card').forEach(widget => {
        widget.addEventListener('click', function() {
            trackInteraction({
                type: 'widget_click',
                widget_id: this.dataset.widgetId,
                timestamp: Date.now(),
                time_on_page: Date.now() - startTime
            });
        });
    });
    
    // Track scroll depth (attention indicator)
    let maxScroll = 0;
    window.addEventListener('scroll', debounce(function() {
        const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
        if (scrollPercent > maxScroll) {
            maxScroll = scrollPercent;
        }
    }, 100));
    
    // Send analytics on page unload
    window.addEventListener('beforeunload', function() {
        trackInteraction({
            type: 'session_end',
            duration: Date.now() - startTime,
            max_scroll_depth: maxScroll,
            cognitive_load: '{{ $cognitiveLoad["status"] }}'
        });
    });
});

function startNewProject() {
    // Immediate visual feedback (<100ms)
    event.target.classList.add('loading');
    
    // Navigate after feedback
    setTimeout(() => {
        window.location.href = '{{ route("projects.create") }}';
    }, 150);
}

function loadMoreActivities() {
    const button = event.target;
    button.textContent = 'Loading...';
    button.disabled = true;
    
    // Fetch more activities
    fetch('/api/activities?offset=5')
        .then(response => response.json())
        .then(data => {
            appendActivities(data.activities);
            button.remove(); // Remove after loading
        });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush

@push('styles')
<style>
/* Dashboard Neuroscience Styles */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* F-Zone 1: Primary Attention Area */
.dashboard-header {
    margin-bottom: 3rem;
}

.dashboard-header .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.headline {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.user-name {
    color: #3b82f6; /* Blue untuk positive recognition */
}

/* Primary CTA - Maximum visual weight */
.btn-cta-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 0.75rem;
    font-size: 1.125rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    transition: all 250ms ease; /* Neural comfort timing */
}

.btn-cta-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
}

.btn-cta-primary:active {
    transform: translateY(0);
}

/* Quick stats - Easy scan layout */
.quick-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-item[data-priority="high"] .stat-value {
    color: #3b82f6;
    font-size: 2.5rem;
    font-weight: 700;
}

.stat-item[data-priority="medium"] .stat-value {
    color: #6b7280;
    font-size: 2rem;
    font-weight: 600;
}

.stat-item[data-priority="low"] .stat-value {
    color: #9ca3af;
    font-size: 1.75rem;
    font-weight: 500;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* F-Zone 2: Widgets Grid */
.widgets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

/* F-Zone 3: Main Content Area */
.dashboard-main {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.primary-content {
    /* Main focus area */
}

.sidebar-content {
    /* Supporting information - reduced visual weight */
    opacity: 0.95;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    transition: background 200ms ease;
}

.activity-item:hover {
    background: #f3f4f6;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.activity-content {
    flex: 1;
}

.activity-text {
    font-size: 0.9375rem;
    color: #374151;
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.8125rem;
    color: #6b7280;
}

/* Quick Actions - Easy access */
.action-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.action-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: #374151;
    text-decoration: none;
    transition: all 200ms ease;
}

.action-link:hover {
    background: #eff6ff;
    color: #2563eb;
}

.action-icon {
    font-size: 1.25rem;
}

/* Cognitive Debug Panel */
.cognitive-debug-panel {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    max-width: 300px;
}

.cognitive-debug-panel h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.cognitive-debug-panel dl {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.5rem;
}

.cognitive-debug-panel dt {
    font-weight: 600;
}

.cognitive-debug-panel dd {
    margin: 0;
}

.status-optimal {
    color: #10b981;
}

.status-acceptable {
    color: #f59e0b;
}

.status-high,
.status-overload {
    color: #ef4444;
}
</style>
@endpush
```

### 6.2 Button Component dengan Neural Hierarchy

```php
// app/View/Components/NeuralButton.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NeuralButton extends Component
{
    public $type; // primary, secondary, tertiary, danger
    public $size; // small, medium, large
    public $loading;
    public $disabled;
    public $visualWeight;
    
    public function __construct(
        $type = 'primary',
        $size = 'medium',
        $loading = false,
        $disabled = false
    ) {
        $this->type = $type;
        $this->size = $size;
        $this->loading = $loading;
        $this->disabled = $disabled;
        $this->visualWeight = $this->calculateVisualWeight();
    }
    
    private function calculateVisualWeight(): int
    {
        // Visual weight berdasarkan type
        $weights = [
            'primary' => 10,
            'secondary' => 7,
            'tertiary' => 4,
            'danger' => 9
        ];
        
        return $weights[$this->type] ?? 5;
    }
    
    public function render()
    {
        return view('components.neural-button');
    }
}
```

```blade
<!-- resources/views/components/neural-button.blade.php -->
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'neural-button neural-button-' . $type . ' neural-button-' . $size,
    'data-visual-weight' => $visualWeight,
    'data-neural-type' => $type,
    'disabled' => $disabled || $loading
]) }}>
    
    @if($loading)
        <!-- Loading state dengan spinner -->
        <span class="button-spinner" aria-label="Loading">
            <svg class="spinner-icon" viewBox="0 0 24 24">
                <circle class="spinner-circle" cx="12" cy="12" r="10" 
                        fill="none" stroke="currentColor" stroke-width="3"/>
            </svg>
        </span>
    @endif
    
    <span class="button-content" style="{{ $loading ? 'opacity: 0.5;' : '' }}">
        {{ $slot }}
    </span>
    
</button>

<style>
/* Base button styles */
.neural-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 250ms ease; /* Neural comfort timing */
    user-select: none;
}

.neural-button:focus {
    outline: none;
    ring: 3px;
    ring-color: currentColor;
    ring-opacity: 0.3;
}

/* Size variants */
.neural-button-small {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.neural-button-medium {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.neural-button-large {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

/* Type variants dengan neuroscience color psychology */

/* Primary: Blue = Trust, Action */
.neural-button-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

.neural-button-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
}

.neural-button-primary:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 4px -1px rgba(59, 130, 246, 0.3);
}

/* Secondary: Border style for less emphasis */
.neural-button-secondary {
    background: transparent;
    color: #3b82f6;
    border: 2px solid #3b82f6;
}

.neural-button-secondary:hover:not(:disabled) {
    background: #eff6ff;
}

/* Tertiary: Minimal style for lowest priority */
.neural-button-tertiary {
    background: transparent;
    color: #6b7280;
}

.neural-button-tertiary:hover:not(:disabled) {
    background: #f3f4f6;
    color: #374151;
}

/* Danger: Red = Urgent attention, Warning */
.neural-button-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
}

.neural-button-danger:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.4);
}

/* Disabled state */
.neural-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
}

/* Loading spinner */
.button-spinner {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-icon {
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
}

.spinner-circle {
    stroke-dasharray: 31.4; /* 2πr where r=10 */
    stroke-dashoffset: 25;
    animation: dash 1.5s ease-in-out infinite;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}

@keyframes dash {
    0% {
        stroke-dashoffset: 31.4;
    }
    50% {
        stroke-dashoffset: 7.85;
    }
    100% {
        stroke-dashoffset: 31.4;
    }
}
</style>
```

**Usage:**
```blade
<!-- Primary action -->
<x-neural-button type="primary" size="large">
    Create Project
</x-neural-button>

<!-- Secondary action -->
<x-neural-button type="secondary">
    Cancel
</x-neural-button>

<!-- Loading state -->
<x-neural-button type="primary" :loading="true">
    Saving...
</x-neural-button>

<!-- Danger action -->
<x-neural-button type="danger" onclick="confirmDelete()">
    Delete Account
</x-neural-button>
```

---

## 7. PERFORMANCE OPTIMIZATION {#performance-optimization}

### 7.1 Response Time Monitoring

```php
// app/Http/Middleware/NeuralResponseTime.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class NeuralResponseTime
{
    // Neural comfort thresholds (ms)
    const INSTANT = 100;
    const SMOOTH = 300;
    const NOTICEABLE = 1000;
    
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = (microtime(true) - $start) * 1000;
        
        // Add response time header
        $response->headers->set('X-Neural-Response-Time', number_format($duration, 2));
        
        // Classify response
        $classification = $this->classifyResponse($duration);
        $response->headers->set('X-Neural-Classification', $classification);
        
        // Log slow responses
        if ($classification !== 'instant' && $classification !== 'smooth') {
            Log::warning('Slow neural response detected', [
                'url' => $request->url(),
                'method' => $request->method(),
                'duration_ms' => $duration,
                'classification' => $classification,
                'threshold_exceeded' => $duration - self::SMOOTH,
                'user_impact' => $this->getUserImpact($classification)
            ]);
        }
        
        return $response;
    }
    
    private function classifyResponse(float $duration): string
    {
        return match(true) {
            $duration < self::INSTANT => 'instant',      // <100ms: Seamless
            $duration < self::SMOOTH => 'smooth',        // <300ms: Good
            $duration < self::NOTICEABLE => 'noticeable', // <1000ms: Acceptable
            default => 'frustrating'                     // >1000ms: Poor
        };
    }
    
    private function getUserImpact(string $classification): string
    {
        return match($classification) {
            'instant' => 'No perceptible delay - optimal neural comfort',
            'smooth' => 'Minor delay - still within comfort zone',
            'noticeable' => 'User perceives delay - needs loading indicator',
            'frustrating' => 'Significant delay - user frustration likely',
            default => 'Unknown'
        };
    }
}
```

### 7.2 Lazy Loading dengan Intersection Observer

```javascript
// resources/js/neuroscience/lazy-loader.js

/**
 * Neural-optimized lazy loading
 * Load content when user shows interest (approaching element)
 */

class NeuralLazyLoader {
    constructor(options = {}) {
        this.options = {
            rootMargin: '100px', // Preload before user reaches element
            threshold: 0.01,
            ...options
        };
        
        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            this.options
        );
        
        this.init();
    }
    
    init() {
        // Find all lazy-load elements
        const elements = document.querySelectorAll('[data-lazy-load]');
        elements.forEach(el => this.observer.observe(el));
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                this.loadElement(entry.target);
                this.observer.unobserve(entry.target);
            }
        });
    }
    
    loadElement(element) {
        const type = element.dataset.lazyType || 'content';
        
        switch(type) {
            case 'image':
                this.loadImage(element);
                break;
            case 'widget':
                this.loadWidget(element);
                break;
            case 'content':
                this.loadContent(element);
                break;
        }
    }
    
    loadImage(img) {
        const src = img.dataset.src;
        if (!src) return;
        
        // Show skeleton while loading
        img.classList.add('loading');
        
        // Create temp image to preload
        const tempImg = new Image();
        tempImg.onload = () => {
            img.src = src;
            img.classList.remove('loading');
            img.classList.add('loaded');
            
            // Fade in smoothly (neural comfort)
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.transition = 'opacity 300ms ease-in-out';
                img.style.opacity = '1';
            }, 10);
        };
        tempImg.src = src;
    }
    
    async loadWidget(widget) {
        const widgetId = widget.dataset.widgetId;
        const endpoint = widget.dataset.endpoint;
        
        widget.classList.add('loading');
        
        try {
            const response = await fetch(endpoint);
            const html = await response.text();
            
            widget.innerHTML = html;
            widget.classList.remove('loading');
            widget.classList.add('loaded');
        } catch (error) {
            widget.innerHTML = '<p class="error">Failed to load widget</p>';
            widget.classList.add('error');
        }
    }
    
    async loadContent(element) {
        const url = element.dataset.url;
        
        element.classList.add('loading');
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            
            element.innerHTML = data.html;
            element.classList.remove('loading');
            element.classList.add('loaded');
        } catch (error) {
            console.error('Failed to load content:', error);
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    new NeuralLazyLoader();
});
```

**Usage in Blade:**
```blade
<!-- Lazy load image -->
<img data-lazy-load
     data-lazy-type="image"
     data-src="{{ $imageUrl }}"
     alt="{{ $altText }}"
     class="lazy-image">

<!-- Lazy load widget -->
<div data-lazy-load
     data-lazy-type="widget"
     data-widget-id="{{ $widget->id }}"
     data-endpoint="/api/widgets/{{ $widget->id }}">
    <!-- Skeleton placeholder -->
    <div class="skeleton-widget"></div>
</div>
```

### 7.3 Skeleton Screens

```css
/* resources/css/neuroscience/skeleton.css */

/**
 * Skeleton screens untuk reduce perceived loading time
 * Studies show skeletons reduce perceived wait by 30%
 */

.skeleton {
    background: linear-gradient(
        90deg,
        #f0f0f0 25%,
        #e0e0e0 50%,
        #f0f0f0 75%
    );
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s ease-in-out infinite;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Common skeleton shapes */
.skeleton-text {
    height: 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
}

.skeleton-text-short {
    width: 60%;
}

.skeleton-text-long {
    width: 100%;
}

.skeleton-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.skeleton-card {
    border-radius: 0.5rem;
    height: 200px;
}

.skeleton-button {
    height: 40px;
    width: 120px;
    border-radius: 0.5rem;
}

/* Component-specific skeletons */
.skeleton-widget {
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
}

.skeleton-widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.skeleton-widget-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
```

```blade
<!-- resources/views/components/skeleton-card.blade.php -->
<div class="skeleton-widget">
    <div class="skeleton-widget-header">
        <div class="skeleton skeleton-text" style="width: 40%;"></div>
        <div class="skeleton skeleton-circle"></div>
    </div>
    
    <div class="skeleton-widget-content">
        <div class="skeleton skeleton-text-long"></div>
        <div class="skeleton skeleton-text-long"></div>
        <div class="skeleton skeleton-text-short"></div>
    </div>
    
    <div class="skeleton-widget-footer" style="margin-top: 1rem;">
        <div class="skeleton skeleton-button"></div>
    </div>
</div>
```

---

## 8. TESTING & VALIDATION {#testing-validation}

### 8.1 Cognitive Load Tests

```php
// tests/Feature/NeuroscienceTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\NeuroscienceService;

class NeuroscienceTest extends TestCase
{
    protected $neuroService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->neuroService = app(NeuroscienceService::class);
    }
    
    /** @test */
    public function dashboard_cognitive_load_is_optimal()
    {
        $response = $this->get('/dashboard');
        
        // Extract page elements
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getContent());
        
        $elements = [
            'buttons' => $dom->getElementsByTagName('button')->length,
            'links' => $dom->getElementsByTagName('a')->length,
            'forms' => $dom->getElementsByTagName('form')->length,
            'images' => $dom->getElementsByTagName('img')->length
        ];
        
        $analysis = $this->neuroService->analyzeCognitiveLoad($elements);
        
        $this->assertLessThanOrEqual(
            40,
            $analysis['complexity_score'],
            'Dashboard cognitive load exceeds acceptable threshold'
        );
        
        $this->assertContains(
            $analysis['status'],
            ['optimal', 'acceptable'],
            'Dashboard cognitive load status is not optimal'
        );
    }
    
    /** @test */
    public function navigation_respects_millers_law()
    {
        $response = $this->get('/');
        
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getContent());
        
        $xpath = new \DOMXPath($dom);
        $navItems = $xpath->query("//nav[@class='primary-menu']//li");
        
        $this->assertLessThanOrEqual(
            7,
            $navItems->length,
            "Navigation has {$navItems->length} items, exceeds Miller's Law limit of 7"
        );
    }
    
    /** @test */
    public function response_time_is_within_neural_comfort()
    {
        $start = microtime(true);
        
        $response = $this->get('/dashboard');
        
        $duration = (microtime(true) - $start) * 1000;
        
        $this->assertLessThan(
            300,
            $duration,
            "Response time {$duration}ms exceeds neural comfort threshold of 300ms"
        );
        
        $response->assertHeader('X-Neural-Response-Time');
        $response->assertHeader('X-Neural-Classification', 'smooth');
    }
    
    /** @test */
    public function form_uses_progressive_disclosure()
    {
        $response = $this->get('/register');
        
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getContent());
        
        $xpath = new \DOMXPath($dom);
        
        // Check for step indicators
        $steps = $xpath->query("//div[@class='form-step']");
        $this->assertGreaterThan(0, $steps->length, 'Multi-step form not implemented');
        
        // Check that only first step is visible
        $visibleSteps = 0;
        foreach ($steps as $step) {
            if (!str_contains($step->getAttribute('style'), 'display: none')) {
                $visibleSteps++;
            }
        }
        
        $this->assertEquals(
            1,
            $visibleSteps,
            'Progressive disclosure not properly implemented - multiple steps visible'
        );
    }
    
    /** @test */
    public function colors_follow_psychology_principles()
    {
        $response = $this->get('/');
        
        $content = $response->getContent();
        
        // Check for proper color usage
        $this->assertStringContainsString(
            '#3b82f6', // Blue for primary actions
            $content,
            'Primary action color (blue) not found'
        );
        
        $this->assertStringContainsString(
            '#ef4444', // Red for errors/danger
            $content,
            'Danger/error color (red) not found'
        );
        
        $this->assertStringContainsString(
            '#10b981', // Green for success
            $content,
            'Success color (green) not found'
        );
    }
}
```

### 8.2 Visual Hierarchy Validation

```php
// tests/Feature/VisualHierarchyTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisualHierarchyTest extends TestCase
{
    /** @test */
    public function primary_cta_has_highest_visual_weight()
    {
        $response = $this->get('/');
        
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getContent());
        
        $xpath = new \DOMXPath($dom);
        $buttons = $xpath->query("//button");
        
        $weights = [];
        foreach ($buttons as $button) {
            $weight = $button->getAttribute('data-visual-weight');
            if ($weight) {
                $weights[] = (int) $weight;
            }
        }
        
        $maxWeight = max($weights);
        
        $primaryButton = $xpath->query("//button[@data-neural-type='primary']")->item(0);
        $primaryWeight = (int) $primaryButton->getAttribute('data-visual-weight');
        
        $this->assertEquals(
            $maxWeight,
            $primaryWeight,
            'Primary CTA does not have the highest visual weight'
        );
    }
    
    /** @test */
    public function headings_follow_hierarchical_order()
    {
        $response = $this->get('/dashboard');
        
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getContent());
        
        $xpath = new \DOMXPath($dom);
        
        // Should have exactly one H1
        $h1Count = $xpath->query("//h1")->length;
        $this->assertEquals(1, $h1Count, 'Page should have exactly one H1');
        
        // H2 should come after H1
        $headings = $xpath->query("//h1 | //h2 | //h3");
        $previousLevel = 0;
        
        foreach ($headings as $heading) {
            $currentLevel = (int) substr($heading->nodeName, 1);
            
            // Level should not skip (e.g., H1 → H3 is wrong)
            $this->assertLessThanOrEqual(
                $previousLevel + 1,
                $currentLevel,
                "Heading hierarchy broken: jumped from H{$previousLevel} to H{$currentLevel}"
            );
            
            $previousLevel = $currentLevel;
        }
    }
}
```

### 8.3 User Behavior Analytics

```javascript
// resources/js/neuroscience/analytics.js

/**
 * Neuroscience-based user behavior analytics
 * Track patterns that indicate cognitive load and user frustration
 */

class NeuralAnalytics {
    constructor() {
        this.session = {
            startTime: Date.now(),
            interactions: [],
            attentionData: [],
            frustrationIndicators: []
        };
        
        this.init();
    }
    
    init() {
        this.trackPageMetrics();
        this.trackInteractions();
        this.trackAttention();
        this.trackFrustration();
        this.trackScrollDepth();
        
        // Send data before page unload
        window.addEventListener('beforeunload', () => this.sendAnalytics());
    }
    
    trackPageMetrics() {
        // Time to Interactive (TTI)
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.name === 'first-contentful-paint') {
                        this.session.fcp = entry.startTime;
                    }
                }
            });
            
            observer.observe({ entryTypes: ['paint'] });
        }
        
        // Track page load time
        window.addEventListener('load', () => {
            const perfData = performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            
            this.session.pageLoadTime = pageLoadTime;
            
            // Classify based on neural thresholds
            this.session.loadClassification = this.classifyLoadTime(pageLoadTime);
        });
    }
    
    classifyLoadTime(time) {
        if (time < 1000) return 'instant';
        if (time < 3000) return 'fast';
        if (time < 5000) return 'acceptable';
        return 'slow';
    }
    
    trackInteractions() {
        // Track all clicks
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-neural-priority], button, a');
            if (!target) return;
            
            this.session.interactions.push({
                type: 'click',
                element: target.tagName,
                priority: target.dataset.neuralPriority,
                timestamp: Date.now(),
                coordinates: { x: e.clientX, y: e.clientY }
            });
        });
        
        // Track form interactions
        document.querySelectorAll('input, textarea, select').forEach(field => {
            let focusTime;
            
            field.addEventListener('focus', () => {
                focusTime = Date.now();
            });
            
            field.addEventListener('blur', () => {
                const duration = Date.now() - focusTime;
                
                this.session.interactions.push({
                    type: 'form_field',
                    fieldName: field.name,
                    duration: duration,
                    timestamp: Date.now()
                });
            });
        });
    }
    
    trackAttention() {
        // Intersection Observer untuk track visible elements
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.session.attentionData.push({
                        element: entry.target.className,
                        timestamp: Date.now(),
                        viewportPosition: entry.boundingClientRect.top,
                        visibilityRatio: entry.intersectionRatio
                    });
                }
            });
        }, {
            threshold: [0.25, 0.5, 0.75, 1.0]
        });
        
        // Observe important elements
        document.querySelectorAll('[data-neural-priority="high"]').forEach(el => {
            observer.observe(el);
        });
    }
    
    trackFrustration() {
        // Rapid clicks = frustration
        let clickCount = 0;
        let clickTimer;
        
        document.addEventListener('click', () => {
            clickCount++;
            clearTimeout(clickTimer);
            
            clickTimer = setTimeout(() => {
                if (clickCount > 3) { // 3+ rapid clicks in 2 seconds
                    this.session.frustrationIndicators.push({
                        type: 'rapid_clicks',
                        count: clickCount,
                        timestamp: Date.now()
                    });
                }
                clickCount = 0;
            }, 2000);
        });
        
        // Mouse thrashing = frustration/confusion
        let mouseMovements = 0;
        let mouseTimer;
        
        document.addEventListener('mousemove', () => {
            mouseMovements++;
            clearTimeout(mouseTimer);
            
            mouseTimer = setTimeout(() => {
                if (mouseMovements > 50) { // Excessive movement
                    this.session.frustrationIndicators.push({
                        type: 'mouse_thrashing',
                        movements: mouseMovements,
                        timestamp: Date.now()
                    });
                }
                mouseMovements = 0;
            }, 3000);
        });
        
        // Back button usage = dissatisfaction
        window.addEventListener('popstate', () => {
            this.session.frustrationIndicators.push({
                type: 'back_button',
                timestamp: Date.now(),
                timeOnPage: Date.now() - this.session.startTime
            });
        });
    }
    
    trackScrollDepth() {
        let maxScroll = 0;
        
        window.addEventListener('scroll', () => {
            const scrollPercent = (window.scrollY / 
                (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                
                // Track milestones
                if (scrollPercent >= 25 && !this.session.scroll25) {
                    this.session.scroll25 = Date.now();
                }
                if (scrollPercent >= 50 && !this.session.scroll50) {
                    this.session.scroll50 = Date.now();
                }
                if (scrollPercent >= 75 && !this.session.scroll75) {
                    this.session.scroll75 = Date.now();
                }
                if (scrollPercent >= 90 && !this.session.scroll90) {
                    this.session.scroll90 = Date.now();
                }
            }
        });
    }
    
    async sendAnalytics() {
        const sessionData = {
            ...this.session,
            sessionDuration: Date.now() - this.session.startTime,
            url: window.location.href,
            userAgent: navigator.userAgent,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            }
        };
        
        // Use sendBeacon for reliable delivery even on page unload
        if (navigator.sendBeacon) {
            navigator.sendBeacon(
                '/api/analytics/neural',
                JSON.stringify(sessionData)
            );
        } else {
            // Fallback for older browsers
            try {
                await fetch('/api/analytics/neural', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(sessionData),
                    keepalive: true
                });
            } catch (error) {
                console.error('Analytics send failed:', error);
            }
        }
    }
}

// Initialize analytics
document.addEventListener('DOMContentLoaded', () => {
    window.neuralAnalytics = new NeuralAnalytics();
});
```

---

## 9. BEST PRACTICES {#best-practices}

### 9.1 Checklist untuk Setiap Halaman

```markdown
# NEUROSCIENCE UI/UX CHECKLIST

## ✅ Cognitive Load
- [ ] Total interactive elements ≤ 15
- [ ] Navigation items per level ≤ 7 (Miller's Law)
- [ ] Form fields per screen ≤ 5
- [ ] Visual hierarchy clear (3 levels max)
- [ ] Information grouping logical

## ✅ Visual Design
- [ ] F-pattern or Z-pattern layout implemented
- [ ] Primary CTA has highest visual weight
- [ ] Color psychology applied correctly:
  - Blue for trust/primary actions
  - Green for success
  - Red for errors/danger
  - Yellow for warnings
- [ ] Contrast ratio ≥ 4.5:1 (WCAG AA)
- [ ] Consistent spacing system

## ✅ Performance
- [ ] Response time < 300ms for interactions
- [ ] Page load < 3 seconds
- [ ] Loading indicators for >300ms operations
- [ ] Lazy loading implemented for below-fold content
- [ ] Skeleton screens for progressive loading

## ✅ Interaction Design
- [ ] Immediate feedback for all actions (<100ms)
- [ ] Transitions 200-300ms (neural comfort)
- [ ] Progressive disclosure for complex forms
- [ ] Clear error messages with solutions
- [ ] Confirmation for destructive actions

## ✅ Typography
- [ ] Max 3 font sizes per page
- [ ] Line height 1.5-1.8 for body text
- [ ] Paragraph width 50-75 characters
- [ ] Adequate white space around text
- [ ] Sans-serif for UI, optional serif for long-form

## ✅ Accessibility
- [ ] Semantic HTML elements used
- [ ] ARIA labels where needed
- [ ] Keyboard navigation functional
- [ ] Focus indicators visible
- [ ] Screen reader tested

## ✅ Analytics
- [ ] Attention tracking implemented
- [ ] Interaction logging active
- [ ] Cognitive load monitoring enabled
- [ ] Frustration indicators tracked
```

### 9.2 Common Anti-Patterns to Avoid

```markdown
# NEUROSCIENCE UI/UX ANTI-PATTERNS

## 🚫 COGNITIVE OVERLOAD PATTERNS

### 1. Wall of Text
**Problem:** Large blocks of unbroken text
**Neural Impact:** Increases cognitive load, reduces comprehension
**Solution:** Break into chunks, use headings, add white space

### 2. Too Many Choices
**Problem:** Presenting 10+ options simultaneously
**Neural Impact:** Decision paralysis (Hick's Law)
**Solution:** Group choices, use progressive disclosure

### 3. Inconsistent Patterns
**Problem:** Buttons/actions look different across pages
**Neural Impact:** Breaks learned patterns, increases processing time
**Solution:** Design system with consistent components

### 4. Hidden Affordances
**Problem:** Clickable elements don't look clickable
**Neural Impact:** Confusion, trial-and-error behavior
**Solution:** Clear visual cues (buttons look like buttons)

## 🚫 ATTENTION ANTI-PATTERNS

### 5. Banner Blindness Triggers
**Problem:** Important info styled like ads
**Neural Impact:** Users unconsciously ignore
**Solution:** Use native UI patterns for key content

### 6. Animation Overload
**Problem:** Everything moves/bounces
**Neural Impact:** Distraction, cognitive fatigue
**Solution:** Reserve animation for feedback and transitions

### 7. Color Misuse
**Problem:** Red for positive actions, green for errors
**Neural Impact:** Conflicts with learned associations
**Solution:** Follow color psychology principles

## 🚫 PERFORMANCE ANTI-PATTERNS

### 8. No Loading Feedback
**Problem:** Blank screen during load
**Neural Impact:** Uncertainty, perceived as "broken"
**Solution:** Skeleton screens, progress indicators

### 9. Abrupt Transitions
**Problem:** Instant content changes without animation
**Neural Impact:** Jarring, disorienting
**Solution:** 200-300ms ease-in-out transitions

### 10. Blocking Operations
**Problem:** UI freezes during processing
**Neural Impact:** Frustration, perception of poor quality
**Solution:** Async operations, optimistic UI updates
```

### 9.3 Implementation Priorities

```markdown
# IMPLEMENTATION PRIORITY MATRIX

## 🔴 CRITICAL (Implement First)
1. Response time < 300ms
2. Primary CTA clearly distinguished
3. Navigation ≤ 7 items
4. Form validation feedback immediate
5. Color psychology basics

## 🟡 HIGH PRIORITY (Week 1)
1. F-pattern layout structure
2. Progressive disclosure for complex forms
3. Loading indicators
4. Skeleton screens
5. Consistent button hierarchy

## 🟢 MEDIUM PRIORITY (Week 2-3)
1. Advanced attention tracking
2. Cognitive load monitoring
3. Micro-interactions and animations
4. Advanced lazy loading
5. A/B testing setup

## 🔵 LOW PRIORITY (Ongoing)
1. Advanced analytics
2. Heat mapping
3. Eye-tracking integration
4. Machine learning personalization
5. Advanced optimization
```

---

## 10. TROUBLESHOOTING GUIDE {#troubleshooting}

### 10.1 Common Issues & Solutions

```markdown
# TROUBLESHOOTING: NEUROSCIENCE UI/UX

## Issue: High Bounce Rate

### Diagnosis Checklist:
- [ ] Check page load time (should be < 3s)
- [ ] Verify cognitive load score (should be < 40)
- [ ] Review F-pattern adherence
- [ ] Check mobile responsiveness
- [ ] Verify CTA visibility

### Solutions:
```php
// 1. Measure actual load time
Route::get('/diagnose', function() {
    $start = microtime(true);
    
    // Your page logic
    
    $loadTime = (microtime(true) - $start) * 1000;
    
    return response()->json([
        'load_time_ms' => $loadTime,
        'status' => $loadTime < 1000 ? 'good' : 'needs_optimization',
        'recommendation' => $loadTime > 1000 
            ? 'Implement caching and lazy loading' 
            : 'Load time is optimal'
    ]);
});

// 2. Reduce cognitive load
$cognitiveScore = app(NeuroscienceService::class)->analyzeCognitiveLoad($elements);

if ($cognitiveScore['complexity_score'] > 40) {
    // Apply progressive disclosure
    // Group similar elements
    // Reduce visible options
}
```

## Issue: Low Conversion Rate

### Diagnosis:
```javascript
// Track user attention on CTA
const ctaButton = document.querySelector('[data-neural-priority="high"]');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            console.log('CTA is visible');
            
            // Check how long until click
            let viewTime = Date.now();
            ctaButton.addEventListener('click', () => {
                const timeToClick = Date.now() - viewTime;
                console.log(`Time to click: ${timeToClick}ms`);
                
                if (timeToClick > 5000) {
                    console.warn('CTA not compelling enough - long decision time');
                }
            }, { once: true });
        }
    });
});

observer.observe(ctaButton);
```

### Solutions:
1. **Increase Visual Weight**
```css
.cta-primary {
    /* Make it IMPOSSIBLE to miss */
    transform: scale(1.1);
    box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4);
    animation: subtle-pulse 2s ease-in-out infinite;
}

@keyframes subtle-pulse {
    0%, 100% { transform: scale(1.1); }
    50% { transform: scale(1.12); }
}
```

2. **Reduce Cognitive Distance**
```blade
<!-- BAD: CTA far from value proposition -->
<h1>Amazing Product</h1>
<p>Long description...</p>
<!-- Many sections... -->
<button>Buy Now</button>

<!-- GOOD: CTA near value prop -->
<h1>Amazing Product</h1>
<p>Key benefit in one sentence.</p>
<button>Buy Now</button>
```

## Issue: High Form Abandonment

### Diagnosis:
```php
// Track field completion rates
DB::table('form_analytics')->insert([
    'form_id' => 'registration',
    'field_name' => $fieldName,
    'completion_rate' => $completionRate,
    'avg_time_spent' => $avgTime,
    'abandonment_point' => $abandonmentRate > 50 ? true : false
]);
```

### Solutions:
```blade
<!-- Implement multi-step with progress -->
<form class="multi-step-form">
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
    </div>
    
    <!-- Only show 3-5 fields per step -->
    <div class="form-step active">
        @for($i = 0; $i < 5; $i++)
            <!-- Max 5 fields -->
        @endfor
        
        <button type="button" onclick="nextStep()">Continue</button>
    </div>
</form>
```

## Issue: Slow Perceived Performance

### Solutions:
```javascript
// Optimistic UI updates
async function saveData(data) {
    // 1. Update UI immediately (optimistic)
    updateUIOptimistically(data);
    
    // 2. Send request in background
    try {
        const response = await fetch('/api/save', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            // Rollback if failed
            rollbackUI();
        }
    } catch (error) {
        rollbackUI();
    }
}

// Skeleton screens
function showSkeletonScreen() {
    container.innerHTML = `
        <div class="skeleton-card">
            <div class="skeleton skeleton-text-long"></div>
            <div class="skeleton skeleton-text-short"></div>
        </div>
    `;
}
```

## Issue: Users Can't Find Key Features

### Diagnosis:
```javascript
// Heat map simulation
const elementsClicked = {};

document.addEventListener('click', (e) => {
    const el = e.target;
    const selector = getElementPath(el);
    
    elementsClicked[selector] = (elementsClicked[selector] || 0) + 1;
});

// After session
console.table(elementsClicked);
// Check if important features have low click counts
```

### Solutions:
1. **Apply Visual Hierarchy**
```css
/* High priority features */
.feature-important {
    /* Larger, brighter, more prominent */
    font-size: 1.25rem;
    color: #1f2937;
    font-weight: 600;
}

/* Medium priority */
.feature-medium {
    font-size: 1rem;
    color: #4b5563;
}

/* Low priority */
.feature-low {
    font-size: 0.875rem;
    color: #6b7280;
}
```

2. **Use Onboarding Tooltips**
```javascript
// Progressive onboarding
if (isNewUser()) {
    showTooltip('#key-feature', 'This is how you...');
}
```
```

---

## CONCLUSION

Panduan ini menyediakan framework lengkap untuk mengimplementasikan prinsip neuroscience dalam Laravel UI/UX. Dengan mengikuti panduan ini, Agent AI dapat:

1. **Membuat keputusan desain berbasis sains**
2. **Mengoptimalkan cognitive load pengguna**
3. **Meningkatkan konversi dan engagement**
4. **Mengurangi frustasi pengguna**
5. **Membangun antarmuka yang intuitif**

### Key Takeaways:

- **Miller's Law (7±2)**: Batasi pilihan dan elemen per grup
- **Hick's Law**: Semakin banyak pilihan, semakin lama keputusan
- **F-Pattern**: Layout natural untuk reading flow
- **Color Psychology**: Warna memicu respon neural spesifik
- **Response Time**: <300ms untuk neural comfort
- **Progressive Disclosure**: Tampilkan informasi bertahap
- **Visual Hierarchy**: Buat prioritas jelas untuk attention

### Next Steps:

1. Implement cognitive load analyzer
2. Setup A/B testing untuk validasi
3. Monitor user behavior analytics
4. Iterate based on data
5. Continuous optimization

**Remember**: Neuroscience UI/UX bukan tentang membuat interface "cantik", tapi tentang membuat interface yang **BEKERJA SELARAS** dengan cara otak manusia memproses informasi.
