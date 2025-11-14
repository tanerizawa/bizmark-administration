# Multi-Permit Selection Feature

## Overview
Fitur ini memungkinkan client untuk memilih beberapa izin sekaligus dari rekomendasi KBLI dengan opsi service type yang berbeda untuk setiap izin.

## User Flow

### 1. Dari Halaman Rekomendasi
```
https://bizmark.id/client/services/68111?scale=menengah&location=pedesaan
```
- User melihat rekomendasi izin berdasarkan KBLI
- Klik tombol "Ajukan Permohonan"
- Redirect ke halaman seleksi izin

### 2. Halaman Seleksi Izin (Multi-Permit Selection)
```
https://bizmark.id/client/applications/create?kbli_code=68111
```

**Features:**
- ✅ Checkbox untuk setiap izin yang direkomendasikan
- ✅ Auto-select semua izin (default)
- ✅ 3 opsi service type per izin:
  * **BizMark.ID** (default) - Kami bantu urus (berbayar)
  * **Sudah Ada** - Izin sudah dimiliki
  * **Dikerjakan Sendiri** - Client urus sendiri
- ✅ Live counter showing:
  * Total izin dipilih
  * Breakdown per service type
- ✅ Sticky bottom bar dengan tombol submit
- ✅ Bulk actions: Pilih Semua / Hapus Semua

### 3. Submission
- Form submit ke `route('client.applications.store-multiple')`
- POST ke `/client/applications/create-multiple`
- Handler: `ApplicationController@storeMultiple`

## Technical Implementation

### Frontend (Alpine.js Component)

**File:** `resources/views/client/applications/select-permit.blade.php`

```blade
<form x-data="permitSelection()" 
      action="{{ route('client.applications.store-multiple') }}" 
      method="POST">
  @csrf
  
  <!-- Permit Cards dengan Checkbox -->
  @foreach($recommendation->recommended_permits as $index => $permit)
  <div :class="selectedPermits.includes({{ $index }}) ? 'border-blue-400 bg-blue-50' : ''">
    
    <!-- Checkbox -->
    <input type="checkbox" 
           :checked="selectedPermits.includes({{ $index }})"
           @change="togglePermit({{ $index }})">
    
    <!-- Permit Info -->
    <h3>{{ $permit['name'] }}</h3>
    <p>{{ $permit['description'] ?? '-' }}</p>
    
    <!-- Service Type Radio Buttons (hanya muncul jika checked) -->
    <div x-show="selectedPermits.includes({{ $index }})" 
         x-transition>
      
      <label>
        <input type="radio" 
               :name="'permits[' + {{ $index }} + '][service_type]'"
               value="bizmark"
               :checked="getServiceType({{ $index }}) === 'bizmark'"
               @change="setServiceType({{ $index }}, 'bizmark')">
        BizMark.ID (Kami bantu urus)
      </label>
      
      <label>
        <input type="radio" 
               :name="'permits[' + {{ $index }} + '][service_type]'"
               value="owned"
               :checked="getServiceType({{ $index }}) === 'owned'"
               @change="setServiceType({{ $index }}, 'owned')">
        Sudah Ada
      </label>
      
      <label>
        <input type="radio" 
               :name="'permits[' + {{ $index }} + '][service_type]'"
               value="self"
               :checked="getServiceType({{ $index }}) === 'self'"
               @change="setServiceType({{ $index }}, 'self')">
        Dikerjakan Sendiri
      </label>
      
      <!-- Hidden form data untuk backend -->
      <input type="hidden" :name="'permits[' + {{ $index }} + '][name]'" 
             value="{{ $permit['name'] }}">
      <input type="hidden" :name="'permits[' + {{ $index }} + '][type]'" 
             value="{{ $permit['type'] ?? 'mandatory' }}">
      <input type="hidden" :name="'permits[' + {{ $index }} + '][category]'" 
             value="{{ $permit['category'] ?? '' }}">
      <!-- ... more hidden fields ... -->
    </div>
  </div>
  @endforeach
  
  <!-- Sticky Bottom Bar -->
  <div class="sticky bottom-0 bg-white border-t p-4">
    <div class="flex items-center justify-between">
      <div>
        <p><span x-text="selectedPermits.length"></span> izin dipilih</p>
        <p class="text-sm text-gray-600">
          <span x-text="countByServiceType('bizmark')"></span> BizMark.ID • 
          <span x-text="countByServiceType('owned')"></span> Sudah Ada • 
          <span x-text="countByServiceType('self')"></span> Dikerjakan Sendiri
        </p>
      </div>
      
      <button type="submit" 
              :disabled="selectedPermits.length === 0"
              class="btn btn-primary">
        Lanjutkan (<span x-text="selectedPermits.length"></span> izin)
      </button>
    </div>
  </div>
</form>

<script>
function permitSelection() {
  return {
    selectedPermits: [],
    serviceTypes: {},
    
    init() {
      // Auto-select semua izin dengan default 'bizmark'
      @foreach($recommendation->recommended_permits as $index => $permit)
      this.selectedPermits.push({{ $index }});
      this.serviceTypes[{{ $index }}] = 'bizmark';
      @endforeach
    },
    
    togglePermit(index) {
      const idx = this.selectedPermits.indexOf(index);
      if (idx > -1) {
        this.selectedPermits.splice(idx, 1);
        delete this.serviceTypes[index];
      } else {
        this.selectedPermits.push(index);
        this.serviceTypes[index] = 'bizmark'; // default
      }
    },
    
    selectAll() {
      this.selectedPermits = [];
      this.serviceTypes = {};
      @foreach($recommendation->recommended_permits as $index => $permit)
      this.selectedPermits.push({{ $index }});
      this.serviceTypes[{{ $index }}] = 'bizmark';
      @endforeach
    },
    
    deselectAll() {
      this.selectedPermits = [];
      this.serviceTypes = {};
    },
    
    setServiceType(index, type) {
      this.serviceTypes[index] = type;
    },
    
    getServiceType(index) {
      return this.serviceTypes[index] || 'bizmark';
    },
    
    countByServiceType(type) {
      return Object.values(this.serviceTypes).filter(t => t === type).length;
    }
  }
}
</script>
```

### Backend (Laravel Controller)

**File:** `app/Http/Controllers/Client/ApplicationController.php`

#### Method: `create()`
Handle dua skenario:
1. **Direct permit selection** (permit_type parameter)
2. **KBLI-based selection** (kbli_code parameter) → Show select-permit page

```php
public function create(Request $request)
{
    $permitTypeId = $request->get('permit_type');
    $kbliCode = $request->get('kbli_code');
    
    // Handle KBLI-based selection
    if ($kbliCode && !$permitTypeId) {
        $kbli = Kbli::where('code', $kbliCode)->first();
        
        if (!$kbli) {
            return redirect()->route('client.services.index')
                ->with('error', 'Kode KBLI tidak ditemukan.');
        }
        
        // Get cached recommendations
        $recommendation = KbliPermitRecommendation::where('kbli_code', $kbliCode)->first();
        
        if (!$recommendation) {
            return redirect()->route('client.services.show', $kbliCode)
                ->with('error', 'Silakan generate rekomendasi terlebih dahulu.');
        }
        
        // Load available permit types for reference
        $permitTypes = PermitType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('client.applications.select-permit', compact(
            'kbli',
            'recommendation',
            'permitTypes'
        ));
    }
    
    // Original flow: direct permit type selection
    // ... existing code ...
}
```

#### Method: `storeMultiple()` (NEW)
Process multiple permit submissions dengan service type:

```php
public function storeMultiple(Request $request)
{
    $validated = $request->validate([
        'kbli_code' => 'required|string|max:10',
        'kbli_description' => 'required|string',
        'permits' => 'required|array|min:1',
        'permits.*.name' => 'required|string',
        'permits.*.service_type' => 'required|in:bizmark,owned,self',
        'permits.*.type' => 'required|string',
        'permits.*.category' => 'nullable|string',
        'permits.*.estimated_cost_min' => 'nullable|numeric',
        'permits.*.estimated_cost_max' => 'nullable|numeric',
        'permits.*.estimated_days' => 'nullable|integer',
    ]);

    $client = auth('client')->user();
    $bizmarkApplications = [];
    $referenceData = [
        'owned' => [],
        'self' => []
    ];

    DB::beginTransaction();
    try {
        foreach ($validated['permits'] as $permitData) {
            if ($permitData['service_type'] === 'bizmark') {
                // Create PermitApplication hanya untuk izin yang dihandle BizMark.ID
                $application = PermitApplication::create([
                    'client_id' => $client->id,
                    'permit_type_id' => null, // Tidak terkait permit type spesifik
                    'status' => 'submitted',
                    'submitted_at' => now(),
                    'kbli_code' => $validated['kbli_code'],
                    'kbli_description' => $validated['kbli_description'],
                    'form_data' => [
                        'permit_name' => $permitData['name'],
                        'permit_type' => $permitData['type'],
                        'permit_category' => $permitData['category'] ?? null,
                        'estimated_cost_min' => $permitData['estimated_cost_min'] ?? 0,
                        'estimated_cost_max' => $permitData['estimated_cost_max'] ?? 0,
                        'estimated_days' => $permitData['estimated_days'] ?? 0,
                        'source' => 'kbli_recommendation',
                    ],
                    'notes' => "Permohonan izin {$permitData['name']} berdasarkan rekomendasi KBLI {$validated['kbli_code']}",
                ]);
                $bizmarkApplications[] = $application;
            } else {
                // Simpan reference untuk izin yang sudah ada atau dikerjakan sendiri
                $referenceData[$permitData['service_type']][] = [
                    'name' => $permitData['name'],
                    'type' => $permitData['type'],
                    'category' => $permitData['category'] ?? null,
                ];
            }
        }

        DB::commit();

        // Send notifications hanya jika ada aplikasi BizMark.ID
        if (count($bizmarkApplications) > 0) {
            $client->notify(new ApplicationSubmittedNotification($bizmarkApplications[0]));
            
            $admins = User::where('guard', 'web')->get();
            foreach ($bizmarkApplications as $app) {
                Notification::send($admins, new NewApplicationNotification($app));
            }
        }

        // Prepare success message
        $message = '';
        if (count($bizmarkApplications) > 0) {
            $message .= count($bizmarkApplications) . ' permohonan izin BizMark.ID berhasil dibuat! ';
        }
        if (count($referenceData['owned']) > 0) {
            $message .= count($referenceData['owned']) . ' izin sudah dimiliki. ';
        }
        if (count($referenceData['self']) > 0) {
            $message .= count($referenceData['self']) . ' izin dikerjakan sendiri. ';
        }

        if (count($bizmarkApplications) > 0) {
            return redirect()->route('client.applications.index')
                ->with('success', trim($message));
        } else {
            return redirect()->route('client.services.index')
                ->with('info', 'Semua izin sudah dimiliki atau dikerjakan sendiri. Tidak ada permohonan yang dibuat.');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

### Routes

**File:** `routes/web.php`

```php
Route::middleware(['auth:client'])->prefix('client')->name('client.')->group(function () {
    // ... other routes ...
    
    // Application Routes
    Route::get('/applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    Route::get('/applications/create', [ApplicationController::class, 'create'])
        ->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])
        ->name('applications.store');
    Route::post('/applications/create-multiple', [ApplicationController::class, 'storeMultiple'])
        ->name('applications.store-multiple'); // NEW ROUTE
    
    // ... other routes ...
});
```

## Data Structure

### Request Payload (Form Submission)
```json
{
  "kbli_code": "68111",
  "kbli_description": "Real estat yang dimiliki sendiri atau disewa",
  "permits": [
    {
      "name": "IMB (Izin Mendirikan Bangunan)",
      "service_type": "bizmark",
      "type": "mandatory",
      "category": "Bangunan & Konstruksi",
      "estimated_cost_min": 1000000,
      "estimated_cost_max": 5000000,
      "estimated_days": 30
    },
    {
      "name": "PBG (Persetujuan Bangunan Gedung)",
      "service_type": "owned",
      "type": "mandatory",
      "category": "Bangunan & Konstruksi",
      "estimated_cost_min": 2000000,
      "estimated_cost_max": 8000000,
      "estimated_days": 45
    },
    {
      "name": "SLF (Sertifikat Laik Fungsi)",
      "service_type": "self",
      "type": "mandatory",
      "category": "Bangunan & Konstruksi",
      "estimated_cost_min": 1500000,
      "estimated_cost_max": 6000000,
      "estimated_days": 21
    }
  ]
}
```

### Database Records Created

**For `service_type: 'bizmark'` only:**

Table: `permit_applications`

| Field | Value | Notes |
|-------|-------|-------|
| client_id | 123 | From auth |
| permit_type_id | NULL | Not linked to specific permit type |
| status | 'submitted' | Ready for admin review |
| submitted_at | 2025-11-14 16:00:00 | Current timestamp |
| kbli_code | '68111' | From KBLI recommendation |
| kbli_description | 'Real estat...' | From KBLI |
| form_data | {...} | JSON with permit details |
| notes | 'Permohonan izin...' | Auto-generated |

**form_data structure:**
```json
{
  "permit_name": "IMB (Izin Mendirikan Bangunan)",
  "permit_type": "mandatory",
  "permit_category": "Bangunan & Konstruksi",
  "estimated_cost_min": 1000000,
  "estimated_cost_max": 5000000,
  "estimated_days": 30,
  "source": "kbli_recommendation"
}
```

## Business Logic

### Service Type Behavior

| Service Type | Create Application? | Store Reference? | Send Notification? | Generate Quote? |
|--------------|---------------------|------------------|-------------------|-----------------|
| **bizmark** | ✅ Yes | ❌ No | ✅ Yes | ✅ Yes (by admin) |
| **owned** | ❌ No | ✅ Yes (future) | ❌ No | ❌ No |
| **self** | ❌ No | ✅ Yes (future) | ❌ No | ❌ No |

### Success Messages

**Scenario 1: All BizMark.ID**
```
"3 permohonan izin BizMark.ID berhasil dibuat!"
```

**Scenario 2: Mixed**
```
"2 permohonan izin BizMark.ID berhasil dibuat! 1 izin sudah dimiliki. 1 izin dikerjakan sendiri."
```

**Scenario 3: No BizMark.ID**
```
"Semua izin sudah dimiliki atau dikerjakan sendiri. Tidak ada permohonan yang dibuat."
```

## Testing Checklist

### Manual Testing

- [ ] **Page Load**
  - [ ] Access `/client/applications/create?kbli_code=68111`
  - [ ] Verify all permits loaded from cache
  - [ ] Verify all checkboxes pre-checked
  - [ ] Verify all service types default to 'bizmark'

- [ ] **Checkbox Interaction**
  - [ ] Uncheck a permit → service type options disappear
  - [ ] Check a permit → service type options appear
  - [ ] Visual feedback (border color change)

- [ ] **Service Type Selection**
  - [ ] Change to "Sudah Ada" → counter updates
  - [ ] Change to "Dikerjakan Sendiri" → counter updates
  - [ ] Switch back to "BizMark.ID" → counter updates

- [ ] **Live Counter**
  - [ ] Total selected count accurate
  - [ ] BizMark.ID count accurate
  - [ ] Sudah Ada count accurate
  - [ ] Dikerjakan Sendiri count accurate

- [ ] **Bulk Actions**
  - [ ] "Pilih Semua" → all checked with 'bizmark' default
  - [ ] "Hapus Semua" → all unchecked, submit button disabled

- [ ] **Form Submission**
  - [ ] Submit with all BizMark.ID → applications created
  - [ ] Submit with mixed service types → correct applications created
  - [ ] Submit with no BizMark.ID → no applications, info message
  - [ ] Verify notifications sent
  - [ ] Verify redirect to applications index

- [ ] **Error Handling**
  - [ ] Submit with no permits selected → validation error
  - [ ] Invalid kbli_code → error message
  - [ ] Database error → rollback, error message

### Automated Testing

```php
// tests/Feature/MultiPermitSelectionTest.php

public function test_can_view_permit_selection_page()
{
    $client = Client::factory()->create();
    $kbli = Kbli::factory()->create(['code' => '68111']);
    $recommendation = KbliPermitRecommendation::factory()->create([
        'kbli_code' => '68111',
        'recommended_permits' => [/* ... */]
    ]);
    
    $response = $this->actingAs($client, 'client')
        ->get('/client/applications/create?kbli_code=68111');
    
    $response->assertOk();
    $response->assertViewHas('recommendation');
    $response->assertSee($kbli->description);
}

public function test_can_create_multiple_applications()
{
    $client = Client::factory()->create();
    
    $response = $this->actingAs($client, 'client')
        ->post('/client/applications/create-multiple', [
            'kbli_code' => '68111',
            'kbli_description' => 'Real estat',
            'permits' => [
                [
                    'name' => 'IMB',
                    'service_type' => 'bizmark',
                    'type' => 'mandatory',
                    // ...
                ],
                [
                    'name' => 'PBG',
                    'service_type' => 'owned',
                    'type' => 'mandatory',
                    // ...
                ]
            ]
        ]);
    
    $response->assertRedirect(route('client.applications.index'));
    $this->assertDatabaseHas('permit_applications', [
        'client_id' => $client->id,
        'kbli_code' => '68111',
        // Only bizmark application created
    ]);
    $this->assertDatabaseCount('permit_applications', 1); // Only 1 created
}
```

## Future Enhancements

### Phase 1 (Current) ✅
- [x] Checkbox selection with service types
- [x] Create applications for BizMark.ID permits
- [x] Store reference data for owned/self permits
- [x] Live counter
- [x] Auto-selection

### Phase 2 (Planned)
- [ ] Store reference data in separate table (`client_permit_references`)
- [ ] Show owned/self permits in client dashboard for tracking
- [ ] Export owned/self permits to PDF for client records
- [ ] Add notes field for each permit selection
- [ ] Add file upload for owned permits (proof of ownership)

### Phase 3 (Planned)
- [ ] Dependency validation (e.g., IMB required before PBG)
- [ ] Cost estimation summary before submission
- [ ] Timeline visualization
- [ ] Batch quotation for selected permits
- [ ] Priority ordering for permits

### Phase 4 (Advanced)
- [ ] AI-powered permit recommendations based on client history
- [ ] Compare owned permits with current requirements
- [ ] Renewal reminders for expiring permits
- [ ] Integration with government permit verification APIs

## Troubleshooting

### Issue: Checkboxes not pre-checked
**Cause:** Alpine.js `init()` not running or permits array empty

**Solution:**
```javascript
// Check browser console for errors
// Verify $recommendation->recommended_permits has data
// Ensure Alpine.js is loaded before component initialization
```

### Issue: Service type not changing
**Cause:** Radio button `name` attribute conflict or Alpine.js binding issue

**Solution:**
```blade
<!-- Ensure unique name per permit -->
:name="'permits[' + {{ $index }} + '][service_type]'"

<!-- Verify @change event -->
@change="setServiceType({{ $index }}, 'bizmark')"
```

### Issue: Form not submitting
**Cause:** CSRF token missing or validation error

**Solution:**
```blade
<!-- Ensure CSRF token present -->
@csrf

<!-- Check validation rules in controller -->
// permits.*.service_type must be in: bizmark,owned,self
```

### Issue: Applications not created
**Cause:** All permits marked as 'owned' or 'self'

**Solution:**
```php
// Controller redirects with info message, this is expected behavior
if (count($bizmarkApplications) === 0) {
    return redirect()->route('client.services.index')
        ->with('info', 'Semua izin sudah dimiliki...');
}
```

## Commits

- `7ccc26f` - feat: Implement multi-permit checkbox selection with service type options
- `b4df3c6` - fix: Change permit_types query from 'status' to 'is_active' column
- `0fc396e` - feat: Add select-permit page for KBLI-based application flow

## Related Files

- `resources/views/client/applications/select-permit.blade.php` - Main view
- `app/Http/Controllers/Client/ApplicationController.php` - Controller
- `routes/web.php` - Routes
- `app/Models/PermitApplication.php` - Model
- `app/Models/KbliPermitRecommendation.php` - Recommendation model
