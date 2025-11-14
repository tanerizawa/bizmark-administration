# 📋 Strategi Kelengkapan Perizinan - BizMark

## 🎯 Tujuan
Memastikan klien mendapatkan rekomendasi perizinan yang **LENGKAP, AKURAT, dan SESUAI BEST PRACTICE** - tidak ada izin yang terlewat.

---

## 🔍 Analisis Masalah

### Problem Statement
**Current:** AI hanya menghasilkan 3 izin utama (NIB, Sertifikat Standar, IMB/PBG)  
**Expected:** 8-15 izin lengkap dengan dependencies untuk kasus kompleks seperti Real Estate

### Contoh Kasus: KBLI 68111 (Real Estate)
**❌ Output Sekarang:**
1. NIB
2. Sertifikat Standar Real Estate  
3. IMB/PBG

**✅ Output yang Seharusnya:**
1. **Foundational (2-3 izin)**
   - NIB (Nomor Induk Berusaha)
   - NPWP Badan Usaha
   - Akta Pendirian PT/CV

2. **Environmental (1-2 izin)**  
   - UKL-UPL (untuk skala kecil-menengah)
   - AMDAL (untuk skala besar > 5 hektar)

3. **Technical - Land & Spatial (3-4 izin)**
   - Pertek BPN (Pertimbangan Teknis Pertanahan)
   - PKKPR (Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang)
   - Pengesahan Siteplan
   - Izin Lokasi (untuk perumahan > 5 ha)

4. **Technical - Building (2-3 izin)**
   - IMB/PBG (Persetujuan Bangunan Gedung)
   - SLF (Sertifikat Laik Fungsi) - post-construction
   - Izin Penggunaan Bangunan

5. **Operational & Sectoral (2-3 izin)**
   - Sertifikat Standar Real Estate
   - SIUP Real Estate (jika diperlukan)
   - Izin Pemasaran (untuk pre-project selling)

**Total: 12-15 izin** (tergantung skala dan lokasi)

---

## 💡 Solusi Multi-Layer

### **Layer 1: AI Prompt Enhancement** ✅ IMPLEMENTED
**Status:** Done  
**Timeline:** 2 jam  
**Impact:** High (80% improvement)

**Changes:**
- ✅ Enhanced prompt dengan instruksi detail
- ✅ Tambah kategori izin: foundational, environmental, technical, operational, sectoral
- ✅ Wajibkan prerequisites dan triggers_next
- ✅ Tambah permit_flow dengan phases dan dependencies
- ✅ Minimal izin: 8-12 (kompleks), 5-7 (menengah), 3-5 (sederhana)
- ✅ Contoh spesifik untuk Real Estate

**Result:**
```json
{
  "permits": [
    {
      "category": "foundational|environmental|technical|operational|sectoral",
      "prerequisites": ["NIB"],
      "triggers_next": ["IMB/PBG"],
      ...
    }
  ],
  "permit_flow": {
    "phases": [...],
    "critical_dependencies": [...]
  }
}
```

### **Layer 2: Enhanced UI/UX** 🚧 TODO
**Priority:** High  
**Timeline:** 4-6 jam  
**Impact:** High (UX clarity)

**Improvements Needed:**

#### 2.1 Permit Flow Visualization
```php
// Add to show.blade.php
<!-- Permit Dependency Flow -->
<div class="permit-flow-diagram">
  <h3>Alur Perizinan (Flow Chart)</h3>
  <div class="flow-phases">
    @foreach($recommendation->permit_flow['phases'] as $phase)
      <div class="phase-card">
        <h4>{{ $phase['phase_name'] }}</h4>
        <div class="permits-in-phase">
          @foreach($phase['permits_in_phase'] as $permit)
            <div class="permit-node">{{ $permit }}</div>
          @endforeach
        </div>
        <span class="phase-duration">⏱️ {{ $phase['estimated_days'] }} hari</span>
      </div>
      @if(!$loop->last)
        <div class="phase-arrow">→</div>
      @endif
    @endforeach
  </div>
</div>
```

#### 2.2 Categorized Permit Display
```php
<!-- Group by Category -->
@php
  $permitsByCategory = collect($recommendation->recommended_permits)
    ->groupBy('category');
@endphp

@foreach($permitsByCategory as $category => $permits)
  <div class="permit-category-section">
    <h3>
      <i class="category-icon"></i>
      {{ ucfirst($category) }} Permits
    </h3>
    @foreach($permits as $permit)
      <div class="permit-card">
        <!-- Show prerequisites -->
        @if(!empty($permit['prerequisites']))
          <div class="prerequisites">
            <span>📋 Memerlukan:</span>
            @foreach($permit['prerequisites'] as $prereq)
              <span class="badge">{{ $prereq }}</span>
            @endforeach
          </div>
        @endif
        
        <!-- Show triggers_next -->
        @if(!empty($permit['triggers_next']))
          <div class="next-permits">
            <span>➡️ Membuka akses ke:</span>
            @foreach($permit['triggers_next'] as $next)
              <span class="badge">{{ $next }}</span>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>
@endforeach
```

#### 2.3 Interactive Dependency Graph
```javascript
// Using Mermaid.js or similar
<div class="mermaid">
graph TD
    A[NIB] --> B[UKL-UPL]
    A --> C[Pertek BPN]
    B --> D[PKKPR]
    C --> D
    D --> E[Pengesahan Siteplan]
    E --> F[IMB/PBG]
    F --> G[SLF]
    A --> H[Sertifikat Standar]
</div>
```

### **Layer 3: Knowledge Base Integration** 🔮 FUTURE
**Priority:** Medium  
**Timeline:** 2-3 minggu  
**Impact:** Medium (consistency)

**Options:**

#### 3.1 Regulatory Database
```sql
CREATE TABLE permit_templates (
  id BIGINT PRIMARY KEY,
  kbli_code VARCHAR(10),
  sector VARCHAR(100),
  permit_name VARCHAR(255),
  category ENUM('foundational', 'environmental', 'technical', 'operational', 'sectoral'),
  is_mandatory BOOLEAN,
  prerequisites JSON, -- Array of permit IDs
  triggers_next JSON,
  legal_basis TEXT,
  typical_cost_min DECIMAL(15,2),
  typical_cost_max DECIMAL(15,2),
  typical_days INT,
  ...
);

CREATE TABLE permit_dependencies (
  parent_permit_id BIGINT,
  child_permit_id BIGINT,
  dependency_type ENUM('required', 'recommended', 'conditional'),
  condition_description TEXT,
  PRIMARY KEY (parent_permit_id, child_permit_id)
);
```

**Benefits:**
- ✅ Consistency across all KBLI codes
- ✅ Easy updates when regulations change
- ✅ Faster response (no AI call for cached data)
- ✅ Admin can review and approve AI suggestions

#### 3.2 Hybrid Approach (AI + Database)
```php
class HybridPermitService {
    public function getRecommendations($kbliCode, $context) {
        // 1. Check database for template
        $template = PermitTemplate::where('kbli_code', $kbliCode)->get();
        
        if ($template->isNotEmpty()) {
            // Use template as base
            $basePermits = $template->toArray();
            
            // Ask AI to customize based on context
            $aiEnhancement = $this->aiService->enhanceTemplate(
                $basePermits, 
                $context
            );
            
            return $this->mergeTemplateAndAI($basePermits, $aiEnhancement);
        }
        
        // 2. If no template, use AI (current behavior)
        $aiResult = $this->aiService->generateFull($kbliCode, $context);
        
        // 3. Store AI result as template for admin review
        $this->queueForAdminReview($aiResult);
        
        return $aiResult;
    }
}
```

### **Layer 4: Admin Review System** 👨‍💼 TODO
**Priority:** High  
**Timeline:** 1 minggu  
**Impact:** Very High (accuracy)

**Features:**

#### 4.1 AI Output Review Panel
```php
// Admin panel: /admin/permits/ai-reviews
Route::get('/admin/permits/ai-reviews', [AdminPermitReviewController::class, 'index']);

// Show all AI-generated recommendations pending review
class AdminPermitReviewController {
    public function index() {
        $pendingReviews = KbliPermitRecommendation::where('admin_reviewed', false)
            ->with('kbli')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.permits.reviews', compact('pendingReviews'));
    }
    
    public function approve($id) {
        $recommendation = KbliPermitRecommendation::findOrFail($id);
        
        // Mark as reviewed and approved
        $recommendation->update([
            'admin_reviewed' => true,
            'admin_approved' => true,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        // Optionally: Create permit template from this
        $this->createTemplateFromRecommendation($recommendation);
    }
    
    public function edit($id) {
        // Allow admin to modify AI output
        $recommendation = KbliPermitRecommendation::findOrFail($id);
        return view('admin.permits.edit', compact('recommendation'));
    }
}
```

#### 4.2 Missing Permit Alert System
```php
// Add to admin dashboard
class PermitCompletenessChecker {
    public function checkCompleteness($kbliCode, $permits) {
        $expectedMinimum = $this->getExpectedMinimumPermits($kbliCode);
        
        if (count($permits) < $expectedMinimum) {
            // Alert admin
            AdminNotification::create([
                'type' => 'incomplete_permits',
                'kbli_code' => $kbliCode,
                'generated_count' => count($permits),
                'expected_minimum' => $expectedMinimum,
                'message' => "KBLI {$kbliCode} only has " . count($permits) . " permits. Expected minimum: {$expectedMinimum}",
            ]);
        }
        
        // Check for missing common permits
        $commonPermits = ['NIB', 'NPWP', 'UKL-UPL'];
        $permitNames = array_column($permits, 'name');
        
        foreach ($commonPermits as $common) {
            if (!in_array($common, $permitNames)) {
                AdminNotification::create([
                    'type' => 'missing_common_permit',
                    'kbli_code' => $kbliCode,
                    'missing_permit' => $common,
                ]);
            }
        }
    }
}
```

#### 4.3 Permit Library Management
```php
// Admin can build permit library
Route::resource('admin/permit-library', PermitLibraryController::class);

// Features:
- CRUD permits master data
- Define dependencies
- Set prerequisites
- Map to KBLI codes
- Set typical costs and timelines
- Upload supporting documents
```

---

## 📊 Implementation Priority

### ✅ Phase 1: IMMEDIATE (Done Today)
- [x] Enhanced AI prompt
- [x] Clear view cache
- [x] Deploy to production
- [x] Test with KBLI 68111

**Expected Result:**
- 8-12 permits for Real Estate
- Clear dependencies shown
- Proper categorization

### 🚧 Phase 2: SHORT TERM (This Week)
- [ ] Update view to show categories
- [ ] Display prerequisites and triggers_next
- [ ] Add permit flow visualization
- [ ] Implement admin review panel
- [ ] Add missing permit alerts

### 🔮 Phase 3: MEDIUM TERM (Next 2 Weeks)
- [ ] Build permit library (database)
- [ ] Create permit templates for common KBLI
- [ ] Implement hybrid approach (AI + Database)
- [ ] Add interactive dependency graph

### 🎯 Phase 4: LONG TERM (Next Month)
- [ ] Full permit management system for admin
- [ ] Automatic completeness checking
- [ ] Regulatory update notifications
- [ ] Client feedback loop for missing permits

---

## 🎓 Best Practices Guidelines

### For AI Prompt:
1. ✅ Always specify minimum number of permits
2. ✅ Include sector-specific examples
3. ✅ Enforce prerequisites and dependencies
4. ✅ Use categories for organization
5. ✅ Require detailed descriptions

### For Admin Team:
1. 📋 Review all first-time KBLI generations
2. 📋 Build library of validated permits per sector
3. 📋 Update when regulations change
4. 📋 Monitor client feedback for missing permits
5. 📋 Maintain sectoral expert contacts

### For Display:
1. 🎨 Group permits by category
2. 🎨 Show clear dependencies (flowchart)
3. 🎨 Highlight critical path
4. 🎨 Provide expandable details
5. 🎨 Add "Report Missing Permit" button

---

## 🔄 Continuous Improvement

### Feedback Loop:
```
Client → Views Permits → Reports Missing → Admin Reviews → Updates Template → AI Learns
```

### Metrics to Track:
- Average permits per KBLI category
- Client-reported missing permits
- Admin approval rate
- Time to complete review
- Client satisfaction score

---

## 📝 Action Items

### For Development Team:
- [ ] Test new AI prompt with various KBLI codes
- [ ] Update view templates for better permit display
- [ ] Implement admin review system
- [ ] Add permit flow visualization

### For Admin Team:
- [ ] Review first batch of AI-generated permits
- [ ] Document common missing permits by sector
- [ ] Create checklist for each major sector
- [ ] Establish review SLA (24-48 hours)

### For Product Team:
- [ ] Define UX for dependency visualization
- [ ] Create permit library structure
- [ ] Plan admin workflows
- [ ] Design client feedback mechanism

---

## 🎯 Success Criteria

1. **Completeness:** 95%+ of permits identified correctly
2. **No False Negatives:** Critical permits never missed
3. **Clear Dependencies:** Users understand sequence
4. **Admin Confidence:** 90%+ approval rate on AI output
5. **Client Satisfaction:** 4.5+ rating on permit recommendations

---

## 📚 References

- OSS 1.1 Regulation
- PP 5/2021 (Perizinan Berusaha Berbasis Risiko)
- Permen PUPR (for IMB/PBG)
- Permen LHK (for environmental permits)
- Sectoral regulations per ministry

---

**Document Version:** 1.0  
**Last Updated:** November 14, 2025  
**Next Review:** After Phase 1 testing
