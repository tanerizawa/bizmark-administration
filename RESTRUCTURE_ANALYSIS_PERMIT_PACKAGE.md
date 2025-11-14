# Analisis Restrukturisasi: Permit Package System

## Masalah Implementasi Saat Ini

### ❌ Yang Salah di Implementasi Multi-Permit Selection (baru dibuat):

1. **Struktur Data Salah:**
   - Membuat multiple `PermitApplication` records individual
   - Tidak ada relasi antar izin dalam satu paket
   - Tidak ada parent container untuk group izin

2. **Missing Form Data Proyek:**
   - Langsung submit dari checkbox selection
   - Tidak ada input: nama proyek, lokasi, luas, detail proyek
   - `form_data` tidak lengkap

3. **Tidak Memanfaatkan Sistem Existing:**
   - Sistem sudah punya `Project` model dengan `ProjectPermit`
   - Sistem sudah punya dependency tracking (`ProjectPermitDependency`)
   - Flow yang benar: `PermitApplication` → convert ke `Project` → manage via `ProjectPermit`

## Sistem Yang Sudah Ada & Benar

### ✅ Existing Models & Architecture:

```
PermitApplication (Entry Point)
├── client_id
├── permit_type_id (nullable untuk KBLI-based)
├── form_data (JSON: project details)
├── kbli_code
├── kbli_description
├── status: draft → submitted → quoted → accepted → converted
└── project_id (setelah converted)

Project (Paket Izin Container)
├── client_id
├── permit_application_id (source application)
├── project_name
├── project_location
├── project_description
├── project_budget
├── target_completion_date
└── status

ProjectPermit (Individual Izin dalam Paket)
├── project_id
├── permit_type_id (nullable)
├── custom_permit_name (untuk KBLI-based permits)
├── sequence_order
├── is_goal_permit
├── status (NOT_STARTED → IN_PROGRESS → APPROVED → EXISTING)
├── estimated_cost
├── actual_cost
└── dependencies

ProjectPermitDependency (Relasi Antar Izin)
├── project_permit_id (child)
├── depends_on_permit_id (parent)
├── dependency_type (MANDATORY, OPTIONAL, RECOMMENDED)
├── can_proceed_without
└── order_sequence
```

## Flow Yang Benar

### 📋 Correct User Journey:

```
1. User Browse KBLI
   └─> services/68111?scale=menengah&location=pedesaan

2. System Generate AI Recommendations
   └─> Display 8-15 recommended permits dengan dependencies

3. User Select Permits (Checkbox Selection) ✅ SUDAH DIBUAT
   └─> Pilih permits: BizMark.ID / Sudah Ada / Dikerjakan Sendiri
   
4. User Fill Project Form ❌ BELUM ADA - CRITICAL!
   Form fields:
   ├── Nama Proyek *
   ├── Lokasi Proyek (Alamat lengkap) *
   ├── Luas Tanah (m²) *
   ├── Luas Bangunan (m²)
   ├── Nilai Investasi *
   ├── Target Selesai
   ├── Deskripsi Proyek *
   ├── Catatan Khusus
   └── Upload: Dokumen Pendukung (optional)

5. System Create PermitApplication (SINGLE ENTRY)
   Data stored:
   ├── permit_type_id: NULL (KBLI-based)
   ├── kbli_code: 68111
   ├── kbli_description: "Real Estat..."
   ├── status: 'submitted'
   └── form_data: {
        "project_name": "...",
        "project_location": "...",
        "land_area": 500,
        "building_area": 300,
        "investment_value": 5000000000,
        "target_date": "2026-06-01",
        "description": "...",
        "selected_permits": [
           {
              "permit_name": "NIB",
              "service_type": "bizmark",
              "estimated_cost_min": 0,
              "estimated_cost_max": 0,
              "estimated_days": 1,
              "category": "foundational",
              "dependencies": []
           },
           {
              "permit_name": "IMB",
              "service_type": "bizmark",
              "estimated_cost_min": 1000000,
              "estimated_cost_max": 5000000,
              "estimated_days": 30,
              "category": "construction",
              "dependencies": ["NIB", "SPPL"]
           },
           {
              "permit_name": "PBG",
              "service_type": "owned",
              "status": "existing"
           }
        ]
     }

6. Admin Reviews Application
   └─> View permit package dengan dependencies
   └─> Create quotation untuk selected permits (service_type='bizmark')
   └─> Send quotation ke client

7. Client Accept Quotation & Pay
   └─> status: 'quotation_accepted' → 'payment_verified'

8. System Convert to Project (AUTOMATIC atau MANUAL)
   Creates:
   ├── Project record
   │   ├── project_name from form_data
   │   ├── permit_application_id (link back)
   │   └── status: 'active'
   │
   └── ProjectPermit records (foreach selected_permits where service_type='bizmark')
       ├── One record per permit
       ├── custom_permit_name (from AI recommendation)
       ├── sequence_order (based on dependencies)
       ├── status: 'NOT_STARTED'
       └── estimated_cost

9. ProjectPermitDependency records created
   └─> Based on AI recommendation prerequisites/triggers_next
   
10. Admin Manages Project
    └─> Track progress per permit
    └─> Upload documents per permit
    └─> Update status with dependency validation
    └─> Cannot start child permit until parent approved
```

## Data Structure Comparison

### ❌ Current Wrong Implementation:

```json
// Multiple PermitApplication records (3 izin = 3 records)
[
  {
    "id": 1,
    "application_number": "APP-2025-001",
    "client_id": 4,
    "permit_type_id": null,
    "kbli_code": "68111",
    "form_data": {
      "permit_name": "NIB",
      "permit_type": "mandatory",
      "source": "kbli_recommendation"
    },
    "status": "submitted"
  },
  {
    "id": 2,
    "application_number": "APP-2025-002",
    "client_id": 4,
    "permit_type_id": null,
    "kbli_code": "68111",
    "form_data": {
      "permit_name": "IMB",
      "permit_type": "mandatory",
      "source": "kbli_recommendation"
    },
    "status": "submitted"
  },
  {
    "id": 3,
    "application_number": "APP-2025-003",
    "client_id": 4,
    "permit_type_id": null,
    "kbli_code": "68111",
    "form_data": {
      "permit_name": "PBG",
      "permit_type": "mandatory",
      "source": "kbli_recommendation"
    },
    "status": "submitted"
  }
]
```

**Problems:**
- 3 separate applications = 3 quotations?
- No connection between permits
- No project context
- Admin sees 3 unrelated applications

### ✅ Correct Implementation:

```json
// Single PermitApplication (Paket)
{
  "id": 1,
  "application_number": "APP-2025-001",
  "client_id": 4,
  "permit_type_id": null,
  "kbli_code": "68111",
  "kbli_description": "Real Estat Yang Dimiliki Sendiri Atau Disewa",
  "form_data": {
    // PROJECT DETAILS
    "project_name": "Pembangunan Gedung Kantor PT ABC",
    "project_location": "Jl. Sudirman No. 123, Jakarta Pusat",
    "land_area": 500,
    "building_area": 300,
    "building_floors": 3,
    "investment_value": 5000000000,
    "target_date": "2026-06-01",
    "description": "Pembangunan gedung kantor 3 lantai untuk operasional perusahaan",
    
    // SELECTED PERMITS FROM AI RECOMMENDATION
    "selected_permits": [
      {
        "permit_name": "Nomor Induk Berusaha (NIB)",
        "service_type": "bizmark",
        "type": "mandatory",
        "category": "foundational",
        "estimated_cost_min": 0,
        "estimated_cost_max": 0,
        "estimated_days": 1,
        "dependencies": []
      },
      {
        "permit_name": "SPPL (Surat Pernyataan Kesanggupan Pengelolaan Lingkungan)",
        "service_type": "bizmark",
        "type": "mandatory",
        "category": "environmental",
        "estimated_cost_min": 2000000,
        "estimated_cost_max": 5000000,
        "estimated_days": 14,
        "dependencies": ["NIB"]
      },
      {
        "permit_name": "IMB (Izin Mendirikan Bangunan)",
        "service_type": "bizmark",
        "type": "mandatory",
        "category": "construction",
        "estimated_cost_min": 1000000,
        "estimated_cost_max": 5000000,
        "estimated_days": 30,
        "dependencies": ["NIB", "SPPL"]
      },
      {
        "permit_name": "PBG (Persetujuan Bangunan Gedung)",
        "service_type": "owned",
        "type": "mandatory",
        "category": "construction",
        "status": "existing",
        "notes": "Sudah dimiliki, masa berlaku hingga 2026"
      },
      {
        "permit_name": "SLF (Sertifikat Laik Fungsi)",
        "service_type": "self",
        "type": "mandatory",
        "category": "construction",
        "notes": "Akan diurus sendiri setelah bangunan selesai"
      }
    ],
    
    // SUMMARY
    "total_permits": 5,
    "permits_by_service": {
      "bizmark": 3,
      "owned": 1,
      "self": 1
    },
    "estimated_total_cost_min": 3000000,
    "estimated_total_cost_max": 10000000,
    "estimated_total_days": 45
  },
  "status": "submitted",
  "submitted_at": "2025-11-14 17:00:00"
}

// After admin quotes and client accepts:
{
  "status": "payment_verified",
  "quoted_price": 8500000,
  "project_id": 1  // ← CONVERTED TO PROJECT
}

// Project record created:
{
  "id": 1,
  "client_id": 4,
  "permit_application_id": 1,
  "project_name": "Pembangunan Gedung Kantor PT ABC",
  "project_location": "Jl. Sudirman No. 123, Jakarta Pusat",
  "project_description": "Pembangunan gedung kantor 3 lantai...",
  "project_budget": 8500000,
  "target_completion_date": "2026-06-01",
  "status": "active"
}

// ProjectPermit records (only for service_type='bizmark'):
[
  {
    "id": 1,
    "project_id": 1,
    "custom_permit_name": "Nomor Induk Berusaha (NIB)",
    "sequence_order": 1,
    "is_goal_permit": false,
    "status": "NOT_STARTED",
    "estimated_cost": 0
  },
  {
    "id": 2,
    "project_id": 1,
    "custom_permit_name": "SPPL",
    "sequence_order": 2,
    "is_goal_permit": false,
    "status": "NOT_STARTED",
    "estimated_cost": 3500000
  },
  {
    "id": 3,
    "project_id": 1,
    "custom_permit_name": "IMB",
    "sequence_order": 3,
    "is_goal_permit": true,
    "status": "NOT_STARTED",
    "estimated_cost": 3000000
  }
]

// ProjectPermitDependency records:
[
  {
    "project_permit_id": 2,  // SPPL
    "depends_on_permit_id": 1,  // depends on NIB
    "dependency_type": "MANDATORY",
    "can_proceed_without": false
  },
  {
    "project_permit_id": 3,  // IMB
    "depends_on_permit_id": 1,  // depends on NIB
    "dependency_type": "MANDATORY",
    "can_proceed_without": false
  },
  {
    "project_permit_id": 3,  // IMB
    "depends_on_permit_id": 2,  // depends on SPPL
    "dependency_type": "MANDATORY",
    "can_proceed_without": false
  }
]
```

**Benefits:**
- 1 application number untuk 1 paket izin
- 1 quotation untuk semua permits
- Clear project context
- Dependency management
- Progress tracking per permit
- Admin lihat as cohesive package

## Required Changes

### 1. Database - ALREADY EXISTS ✅

No schema changes needed! System already supports:
- `PermitApplication.form_data` (JSON) - untuk store project details + selected permits
- `PermitApplication.project_id` - link to converted project
- `Project` model - container untuk paket izin
- `ProjectPermit` - individual permits dalam project
- `ProjectPermitDependency` - relasi antar permits

### 2. View: Add Project Form (NEW FILE NEEDED)

**File:** `resources/views/client/applications/create-package.blade.php`

Form fields:
```blade
<!-- Bagian 1: Info Proyek -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h2>Informasi Proyek</h2>
    
    <input name="project_name" required placeholder="Contoh: Pembangunan Gedung Kantor PT ABC">
    <textarea name="project_location" required placeholder="Alamat lengkap proyek"></textarea>
    <input type="number" name="land_area" placeholder="Luas tanah (m²)">
    <input type="number" name="building_area" placeholder="Luas bangunan (m²)">
    <input type="number" name="building_floors" placeholder="Jumlah lantai">
    <input type="number" name="investment_value" required placeholder="Nilai investasi (Rp)">
    <input type="date" name="target_completion_date">
    <textarea name="project_description" required></textarea>
</div>

<!-- Bagian 2: Review Selected Permits (Read-only dari session) -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h2>Izin yang Dipilih</h2>
    
    @foreach($selectedPermits as $permit)
    <div class="permit-card">
        <h3>{{ $permit['name'] }}</h3>
        <span class="badge">{{ $permit['service_type'] }}</span>
        <p>Estimasi: {{ $permit['estimated_days'] }} hari</p>
        @if($permit['dependencies'])
        <p>Memerlukan: {{ implode(', ', $permit['dependencies']) }}</p>
        @endif
    </div>
    @endforeach
    
    <div class="summary">
        <p>Total: {{ count($selectedPermits) }} izin</p>
        <p>BizMark.ID: {{ $bizmarkCount }} izin</p>
        <p>Sudah Ada: {{ $ownedCount }} izin</p>
        <p>Dikerjakan Sendiri: {{ $selfCount }} izin</p>
    </div>
</div>

<!-- Bagian 3: Upload Dokumen Pendukung (Optional) -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h2>Dokumen Pendukung (Opsional)</h2>
    <input type="file" name="supporting_documents[]" multiple>
</div>

<button type="submit">Ajukan Permohonan</button>
```

### 3. Controller Logic Update

**File:** `app/Http/Controllers/Client/ApplicationController.php`

#### Update `storeMultiple()` method:

```php
public function storeMultiple(Request $request)
{
    $validated = $request->validate([
        'kbli_code' => 'required|string|max:10',
        'kbli_description' => 'required|string',
        
        // PROJECT DETAILS (NEW - REQUIRED)
        'project_name' => 'required|string|max:255',
        'project_location' => 'required|string',
        'land_area' => 'nullable|numeric',
        'building_area' => 'nullable|numeric',
        'building_floors' => 'nullable|integer',
        'investment_value' => 'required|numeric',
        'target_completion_date' => 'nullable|date',
        'project_description' => 'required|string',
        
        // PERMITS (from checkbox selection)
        'permits' => 'required|array|min:1',
        'permits.*.name' => 'required|string',
        'permits.*.service_type' => 'required|in:bizmark,owned,self',
        'permits.*.type' => 'required|string',
        'permits.*.category' => 'nullable|string',
        'permits.*.estimated_cost_min' => 'nullable|numeric',
        'permits.*.estimated_cost_max' => 'nullable|numeric',
        'permits.*.estimated_days' => 'nullable|integer',
        'permits.*.dependencies' => 'nullable|array',
    ]);

    $client = auth('client')->user();

    DB::beginTransaction();
    try {
        // Calculate totals
        $bizmarkPermits = array_filter($validated['permits'], 
            fn($p) => $p['service_type'] === 'bizmark');
        $totalCostMin = array_sum(array_column($bizmarkPermits, 'estimated_cost_min'));
        $totalCostMax = array_sum(array_column($bizmarkPermits, 'estimated_cost_max'));
        $totalDays = max(array_column($bizmarkPermits, 'estimated_days'));
        
        // Create SINGLE PermitApplication (Package)
        $application = PermitApplication::create([
            'client_id' => $client->id,
            'permit_type_id' => null, // KBLI-based package
            'status' => 'submitted',
            'submitted_at' => now(),
            'kbli_code' => $validated['kbli_code'],
            'kbli_description' => $validated['kbli_description'],
            'form_data' => [
                // Project details
                'project_name' => $validated['project_name'],
                'project_location' => $validated['project_location'],
                'land_area' => $validated['land_area'],
                'building_area' => $validated['building_area'],
                'building_floors' => $validated['building_floors'],
                'investment_value' => $validated['investment_value'],
                'target_completion_date' => $validated['target_completion_date'],
                'project_description' => $validated['project_description'],
                
                // Selected permits
                'selected_permits' => $validated['permits'],
                
                // Summary
                'total_permits' => count($validated['permits']),
                'permits_by_service' => [
                    'bizmark' => count($bizmarkPermits),
                    'owned' => count(array_filter($validated['permits'], fn($p) => $p['service_type'] === 'owned')),
                    'self' => count(array_filter($validated['permits'], fn($p) => $p['service_type'] === 'self')),
                ],
                'estimated_total_cost_min' => $totalCostMin,
                'estimated_total_cost_max' => $totalCostMax,
                'estimated_total_days' => $totalDays,
                'source' => 'kbli_recommendation_package',
            ],
            'notes' => "Paket perizinan {$validated['project_name']} - " 
                      . count($bizmarkPermits) . " izin dikelola BizMark.ID",
        ]);

        DB::commit();

        // Send notification (single email for the package)
        $client->notify(new ApplicationSubmittedNotification($application));
        
        $admins = User::where('is_active', true)->get();
        Notification::send($admins, new NewApplicationNotification($application));

        return redirect()->route('client.applications.show', $application->id)
            ->with('success', 'Permohonan paket izin berhasil diajukan! '
                            . count($bizmarkPermits) . ' izin akan dikelola oleh tim kami.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

### 4. Flow Update in `select-permit.blade.php`

**Current:** Form submits to `applications/create-multiple`

**Change to:** 
1. Store selection in session
2. Redirect to project form
3. Project form submits with all data

```php
// In ApplicationController::selectPermits() - NEW METHOD
public function selectPermits(Request $request)
{
    // Store selected permits in session
    session([
        'permit_selection' => [
            'kbli_code' => $request->kbli_code,
            'kbli_description' => $request->kbli_description,
            'permits' => $request->permits,
        ]
    ]);
    
    // Redirect to project form
    return redirect()->route('client.applications.create-package');
}

// In routes/web.php - ADD NEW ROUTES
Route::post('/applications/select-permits', [ApplicationController::class, 'selectPermits'])
    ->name('applications.select-permits');
Route::get('/applications/create-package', [ApplicationController::class, 'createPackage'])
    ->name('applications.create-package');
Route::post('/applications/store-package', [ApplicationController::class, 'storeMultiple'])
    ->name('applications.store-package');
```

### 5. Admin Panel - View Package

**File:** `resources/views/admin/applications/show.blade.php`

Display should show:
```blade
<!-- Package Overview -->
<div class="package-header">
    <h1>{{ $application->form_data['project_name'] }}</h1>
    <p>{{ $application->form_data['project_location'] }}</p>
    <p>Nilai Investasi: Rp {{ number_format($application->form_data['investment_value']) }}</p>
</div>

<!-- Selected Permits with Dependencies -->
<div class="permits-grid">
    @foreach($application->form_data['selected_permits'] as $permit)
    <div class="permit-card {{ $permit['service_type'] }}">
        <h3>{{ $permit['name'] }}</h3>
        <span class="badge">
            @if($permit['service_type'] === 'bizmark')
                <i class="fas fa-handshake"></i> BizMark.ID
            @elseif($permit['service_type'] === 'owned')
                <i class="fas fa-check-circle"></i> Sudah Ada
            @else
                <i class="fas fa-user-check"></i> Dikerjakan Sendiri
            @endif
        </span>
        
        @if(!empty($permit['dependencies']))
        <div class="dependencies">
            <strong>Memerlukan:</strong>
            @foreach($permit['dependencies'] as $dep)
            <span class="dep-badge">{{ $dep }}</span>
            @endforeach
        </div>
        @endif
        
        @if($permit['service_type'] === 'bizmark')
        <div class="cost">
            Estimasi: Rp {{ number_format($permit['estimated_cost_min']) }} 
            - Rp {{ number_format($permit['estimated_cost_max']) }}
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Quotation for BizMark.ID permits only -->
<div class="quotation-section">
    <h2>Quotation ({{ $bizmarkCount }} izin)</h2>
    <form action="{{ route('admin.applications.quote', $application->id) }}" method="POST">
        @csrf
        <input type="number" name="quoted_price" required>
        <textarea name="quotation_notes"></textarea>
        <button type="submit">Kirim Quotation</button>
    </form>
</div>
```

### 6. Conversion to Project (After Payment)

**File:** `app/Http/Controllers/Admin/ApplicationController.php`

```php
public function convertToProject(PermitApplication $application)
{
    DB::beginTransaction();
    try {
        $formData = $application->form_data;
        
        // Create Project
        $project = Project::create([
            'client_id' => $application->client_id,
            'permit_application_id' => $application->id,
            'project_name' => $formData['project_name'],
            'project_location' => $formData['project_location'],
            'project_description' => $formData['project_description'],
            'project_budget' => $application->quoted_price,
            'target_completion_date' => $formData['target_completion_date'],
            'status' => 'active',
        ]);
        
        // Create ProjectPermit for each bizmark permit
        $sequenceOrder = 1;
        $permitIdMap = [];
        
        foreach ($formData['selected_permits'] as $permitData) {
            if ($permitData['service_type'] !== 'bizmark') {
                continue; // Skip owned/self permits
            }
            
            $projectPermit = ProjectPermit::create([
                'project_id' => $project->id,
                'permit_type_id' => null,
                'custom_permit_name' => $permitData['name'],
                'sequence_order' => $sequenceOrder++,
                'is_goal_permit' => false, // Can set based on logic
                'status' => ProjectPermit::STATUS_NOT_STARTED,
                'estimated_cost' => ($permitData['estimated_cost_min'] + $permitData['estimated_cost_max']) / 2,
                'target_date' => now()->addDays($permitData['estimated_days']),
            ]);
            
            $permitIdMap[$permitData['name']] = $projectPermit->id;
        }
        
        // Create Dependencies
        foreach ($formData['selected_permits'] as $permitData) {
            if ($permitData['service_type'] !== 'bizmark' || empty($permitData['dependencies'])) {
                continue;
            }
            
            $childId = $permitIdMap[$permitData['name']];
            
            foreach ($permitData['dependencies'] as $depName) {
                if (isset($permitIdMap[$depName])) {
                    ProjectPermitDependency::create([
                        'project_permit_id' => $childId,
                        'depends_on_permit_id' => $permitIdMap[$depName],
                        'dependency_type' => 'MANDATORY',
                        'can_proceed_without' => false,
                    ]);
                }
            }
        }
        
        // Update application
        $application->update([
            'status' => 'completed',
            'project_id' => $project->id,
            'converted_at' => now(),
        ]);
        
        DB::commit();
        
        return redirect()->route('admin.projects.show', $project->id)
            ->with('success', 'Application berhasil dikonversi ke Project!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Conversion failed: ' . $e->getMessage());
    }
}
```

## Implementation Priority

### Phase 1: Critical (Hari ini) 🔴

1. **Revert Current storeMultiple()** - jangan create multiple applications
2. **Add Project Form View** - create-package.blade.php
3. **Update Flow** - checkbox → session → project form → submit package
4. **Update storeMultiple()** - create single application dengan complete form_data

### Phase 2: Important (Besok) 🟡

5. **Update Admin View** - show package properly
6. **Update Quotation Logic** - untuk paket, bukan per izin
7. **Update Notifications** - mention package dengan N permits

### Phase 3: Enhancement (Minggu depan) 🟢

8. **Create Conversion Method** - Application → Project
9. **Test Dependencies** - ensure ProjectPermit dependencies work
10. **Client Dashboard** - show packages not individual permits

## Testing Scenarios

### Scenario 1: KBLI Package dengan 5 Permits

**Input:**
- Project: "Pembangunan Gedung Kantor"
- Permits: 3 BizMark.ID, 1 Owned, 1 Self

**Expected:**
- 1 PermitApplication created
- form_data contains project details + 5 permits
- Admin sees 1 application dengan 3 permits yang perlu di-quote
- After conversion: 1 Project dengan 3 ProjectPermit + dependencies

### Scenario 2: Semua Permits "Sudah Ada"

**Input:**
- Project: "Renovasi Bangunan"
- Permits: 0 BizMark.ID, 5 Owned, 0 Self

**Expected:**
- 1 PermitApplication created (for record keeping)
- status: 'completed' (no quotation needed)
- No Project created (no active work)
- Client notified: "Semua izin sudah dimiliki, tidak ada yang perlu diproses"

### Scenario 3: Complex Dependencies

**Input:**
- NIB (no deps) → SPPL (needs NIB) → IMB (needs NIB + SPPL) → SLF (needs IMB)

**Expected:**
- Dependencies created correctly
- NIB can start immediately
- SPPL blocked until NIB approved
- IMB blocked until NIB AND SPPL approved
- SLF blocked until IMB approved

## File Changes Summary

### Files to Create:
1. `resources/views/client/applications/create-package.blade.php` (NEW)

### Files to Modify:
1. `app/Http/Controllers/Client/ApplicationController.php`
   - Add `selectPermits()` method
   - Add `createPackage()` method  
   - Modify `storeMultiple()` method
   
2. `resources/views/client/applications/select-permit.blade.php`
   - Change form action to `select-permits` (session storage)
   
3. `routes/web.php`
   - Add routes for select-permits, create-package, store-package
   
4. `resources/views/admin/applications/show.blade.php`
   - Update to show package structure
   
5. `app/Http/Controllers/Admin/ApplicationController.php`
   - Add `convertToProject()` method

### Files to Check (May need updates):
1. `app/Notifications/NewApplicationNotification.php` ✅ ALREADY FIXED
2. `resources/views/client/applications/index.blade.php` ✅ ALREADY FIXED
3. `resources/views/client/applications/show.blade.php` (need to check)

## Conclusion

Implementasi saat ini **salah** karena:
- ❌ Create multiple individual applications (1 per permit)
- ❌ No project context (missing form data)
- ❌ Tidak memanfaatkan sistem paket yang sudah ada
- ❌ Admin tidak bisa lihat as cohesive package
- ❌ No dependency tracking

Implementasi yang **benar**:
- ✅ Create 1 application = 1 permit package
- ✅ Include complete project details
- ✅ Use existing Project + ProjectPermit system
- ✅ Admin sees package dengan dependencies
- ✅ Convert to Project after payment
- ✅ Track progress per permit dengan dependency validation

**Next Steps:**
1. Review & approve this analysis
2. Implement Phase 1 changes
3. Test complete flow
4. Deploy and monitor
