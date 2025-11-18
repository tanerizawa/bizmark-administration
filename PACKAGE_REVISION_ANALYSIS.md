# Analisis & Rekomendasi: Fitur Revisi/Penyesuaian Paket Izin

## 📋 **KONDISI SAAT INI**

### Fitur Yang Ada:
1. **`requestDocumentRevision()`**
   - Hanya mengubah status menjadi `document_incomplete`
   - Hanya menambahkan notes
   - **TIDAK** bisa mengubah isi paket, biaya, atau jumlah izin

### Data Yang Tersimpan:
```php
permit_applications table:
- application_number
- client_id
- permit_type_id (nullable untuk package)
- form_data (JSON) → berisi:
  ├── package_type: 'multi_permit'
  ├── project_name
  ├── selected_permits[] → array of permits
  ├── permits_by_service (bizmark/owned/self count)
  ├── project_location
  ├── land_area, building_area
  └── investment_value
- quoted_price (total)
- quotation_notes
```

### Alur Saat Ini:
```
Client Submit → Admin Review → Quote Price → Client Pay → Convert to Project
                     ↓
            (hanya bisa request doc revision)
```

---

## ❌ **GAP ANALYSIS - Apa Yang Kurang**

### 1. **Tidak Ada Fitur Re-Quote / Package Adjustment**
   - ❌ Tidak bisa mengubah daftar izin setelah submit
   - ❌ Tidak bisa menambah/kurangi izin berdasarkan kajian teknis
   - ❌ Tidak bisa update biaya per izin
   - ❌ Tidak ada history perubahan paket

### 2. **Data Wilayah Tidak Lengkap**
   - ❌ Hanya ada `project_location` (text field)
   - ❌ Tidak ada breakdown: Provinsi, Kabupaten/Kota, Kecamatan
   - ❌ Tidak ada koordinat GPS
   - ❌ Tidak ada zona/kawasan (industri, komersial, dll)

### 3. **Data Legalitas Awal Tidak Tercatat**
   - ❌ Tidak ada field untuk dokumen legalitas existing:
     - Status kepemilikan tanah (HGB, HGU, Girik, dll)
     - Sertifikat tanah
     - IMB existing (jika ada)
     - NPWP Perusahaan
     - Akta Pendirian
     - NIB (Nomor Induk Berusaha)
     - Surat Kuasa (jika diwakilkan)

### 4. **Biaya Tidak Detail**
   - ❌ Hanya ada `quoted_price` total
   - ❌ Tidak ada breakdown biaya per izin
   - ❌ Tidak ada biaya konsultasi, survei, pengurusan terpisah
   - ❌ Tidak ada estimasi waktu per izin

### 5. **Tidak Ada Versioning**
   - ❌ Tidak ada tracking revisi paket (v1, v2, v3)
   - ❌ Client tidak tahu apa yang berubah
   - ❌ Tidak ada approval workflow untuk perubahan

---

## ✅ **REKOMENDASI IMPROVEMENT**

### **FASE 1: Database Enhancement**

#### 1.1. Tabel Baru: `application_revisions`
```sql
CREATE TABLE application_revisions (
    id BIGSERIAL PRIMARY KEY,
    application_id BIGINT REFERENCES permit_applications(id) ON DELETE CASCADE,
    revision_number INT NOT NULL, -- 1, 2, 3...
    
    -- Reason for revision
    revision_type ENUM('technical_adjustment', 'client_request', 'document_incomplete', 'cost_update'),
    revision_reason TEXT,
    revised_by_id BIGINT REFERENCES users(id),
    
    -- Snapshot of data at this revision
    permits_data JSONB, -- [{permit_id, permit_name, service_type, estimated_cost, estimated_days}]
    project_data JSONB, -- {location, land_area, building_area, investment_value, zone_type}
    total_cost DECIMAL(15,2),
    
    -- Status
    status ENUM('draft', 'pending_client_approval', 'approved', 'rejected'),
    client_approved_at TIMESTAMP,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 1.2. Tabel Baru: `application_location_details`
```sql
CREATE TABLE application_location_details (
    id BIGSERIAL PRIMARY KEY,
    application_id BIGINT UNIQUE REFERENCES permit_applications(id) ON DELETE CASCADE,
    
    -- Address breakdown
    province VARCHAR(100),
    city_regency VARCHAR(100),
    district VARCHAR(100),
    sub_district VARCHAR(100),
    full_address TEXT,
    postal_code VARCHAR(10),
    
    -- Coordinates
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Zone classification
    zone_type ENUM('industrial', 'commercial', 'residential', 'mixed', 'special_economic_zone'),
    land_status ENUM('HGB', 'HGU', 'Hak_Milik', 'Girik', 'Sewa', 'Other'),
    land_certificate_number VARCHAR(100),
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 1.3. Tabel Baru: `application_legality_documents`
```sql
CREATE TABLE application_legality_documents (
    id BIGSERIAL PRIMARY KEY,
    application_id BIGINT REFERENCES permit_applications(id) ON DELETE CASCADE,
    
    -- Document type
    document_category ENUM(
        'land_ownership', -- Sertifikat Tanah
        'company_legal', -- Akta, NPWP, NIB
        'existing_permits', -- IMB existing, dll
        'power_of_attorney', -- Surat Kuasa
        'technical', -- Site Plan, Gambar Teknis
        'other'
    ),
    document_name VARCHAR(200),
    
    -- Status
    is_available BOOLEAN DEFAULT FALSE,
    document_number VARCHAR(100),
    issued_date DATE,
    expiry_date DATE,
    
    -- File attachment
    file_path VARCHAR(500),
    
    -- Notes
    notes TEXT,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 1.4. Tabel Baru: `quotation_items`
```sql
CREATE TABLE quotation_items (
    id BIGSERIAL PRIMARY KEY,
    application_id BIGINT REFERENCES permit_applications(id) ON DELETE CASCADE,
    revision_id BIGINT REFERENCES application_revisions(id) ON DELETE CASCADE,
    
    -- Item details
    item_type ENUM('permit', 'consultation', 'survey', 'processing', 'other'),
    permit_type_id BIGINT REFERENCES permit_types(id) ON DELETE SET NULL,
    
    item_name VARCHAR(200),
    description TEXT,
    
    -- Pricing
    unit_price DECIMAL(15,2),
    quantity INT DEFAULT 1,
    subtotal DECIMAL(15,2),
    
    -- Service type
    service_type ENUM('bizmark', 'owned', 'self'),
    
    -- Time estimation
    estimated_days INT,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### **FASE 2: Controller & Logic Enhancement**

#### 2.1. Controller Baru: `PackageRevisionController.php`
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\ApplicationRevision;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageRevisionController extends Controller
{
    /**
     * Show form untuk revisi paket
     */
    public function create($applicationId)
    {
        $application = PermitApplication::with(['client', 'revisions'])
            ->findOrFail($applicationId);
        
        // Get current package data
        $currentPackage = $this->getCurrentPackageData($application);
        
        // Get available permit types
        $permitTypes = PermitType::active()->orderBy('name')->get();
        
        // Get previous revisions
        $revisions = $application->revisions()
            ->orderBy('revision_number', 'desc')
            ->get();
        
        return view('admin.permit-applications.revise', compact(
            'application',
            'currentPackage',
            'permitTypes',
            'revisions'
        ));
    }
    
    /**
     * Store revision baru
     */
    public function store(Request $request, $applicationId)
    {
        $validated = $request->validate([
            'revision_type' => 'required|in:technical_adjustment,client_request,cost_update',
            'revision_reason' => 'required|string|max:1000',
            'permits' => 'required|array|min:1',
            'permits.*.permit_type_id' => 'required|exists:permit_types,id',
            'permits.*.service_type' => 'required|in:bizmark,owned,self',
            'permits.*.unit_price' => 'required|numeric|min:0',
            'permits.*.estimated_days' => 'required|integer|min:1',
            
            // Location details
            'location.province' => 'required|string',
            'location.city_regency' => 'required|string',
            'location.full_address' => 'required|string',
            'location.zone_type' => 'required|in:industrial,commercial,residential,mixed',
            'location.land_status' => 'required|in:HGB,HGU,Hak_Milik,Girik,Sewa',
            
            // Legality documents
            'legality_documents' => 'nullable|array',
        ]);
        
        $application = PermitApplication::findOrFail($applicationId);
        
        DB::beginTransaction();
        try {
            // Create revision
            $revisionNumber = $application->revisions()->max('revision_number') + 1;
            
            $revision = ApplicationRevision::create([
                'application_id' => $application->id,
                'revision_number' => $revisionNumber,
                'revision_type' => $validated['revision_type'],
                'revision_reason' => $validated['revision_reason'],
                'revised_by_id' => auth()->id(),
                'permits_data' => $validated['permits'],
                'project_data' => [
                    'location' => $validated['location'],
                    'land_area' => $request->land_area,
                    'building_area' => $request->building_area,
                    'investment_value' => $request->investment_value,
                ],
                'total_cost' => collect($validated['permits'])->sum('unit_price'),
                'status' => 'pending_client_approval',
            ]);
            
            // Create quotation items
            foreach ($validated['permits'] as $permit) {
                QuotationItem::create([
                    'application_id' => $application->id,
                    'revision_id' => $revision->id,
                    'item_type' => 'permit',
                    'permit_type_id' => $permit['permit_type_id'],
                    'item_name' => PermitType::find($permit['permit_type_id'])->name,
                    'unit_price' => $permit['unit_price'],
                    'quantity' => 1,
                    'subtotal' => $permit['unit_price'],
                    'service_type' => $permit['service_type'],
                    'estimated_days' => $permit['estimated_days'],
                ]);
            }
            
            // Update location details
            $this->updateLocationDetails($application->id, $validated['location']);
            
            // Update legality documents status
            if (!empty($validated['legality_documents'])) {
                $this->updateLegalityDocuments($application->id, $validated['legality_documents']);
            }
            
            // Send notification to client
            $application->client->notify(new PackageRevisionCreated($revision));
            
            DB::commit();
            
            return redirect()
                ->route('admin.permit-applications.show', $application->id)
                ->with('success', 'Revisi paket berhasil dibuat dan menunggu persetujuan client');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat revisi: ' . $e->getMessage());
        }
    }
    
    /**
     * Client approve revision
     */
    public function approve($applicationId, $revisionId)
    {
        $revision = ApplicationRevision::where('application_id', $applicationId)
            ->where('id', $revisionId)
            ->where('status', 'pending_client_approval')
            ->firstOrFail();
        
        DB::beginTransaction();
        try {
            // Approve revision
            $revision->update([
                'status' => 'approved',
                'client_approved_at' => now(),
            ]);
            
            // Update application with new data
            $application = $revision->application;
            $application->update([
                'form_data' => array_merge(
                    $application->form_data ?? [],
                    [
                        'selected_permits' => $revision->permits_data,
                        'project_data' => $revision->project_data,
                    ]
                ),
                'quoted_price' => $revision->total_cost,
                'status' => 'quotation_accepted',
            ]);
            
            // Create status log
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => 'quotation_accepted',
                'notes' => "Client menyetujui revisi paket #{$revision->revision_number}",
                'changed_by_type' => 'client',
                'changed_by_id' => $application->client_id,
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Revisi paket disetujui');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal approve: ' . $e->getMessage());
        }
    }
    
    private function getCurrentPackageData($application)
    {
        // Get latest approved revision or original data
        $latestRevision = $application->revisions()
            ->where('status', 'approved')
            ->latest()
            ->first();
        
        if ($latestRevision) {
            return [
                'permits' => $latestRevision->permits_data,
                'project_data' => $latestRevision->project_data,
                'total_cost' => $latestRevision->total_cost,
            ];
        }
        
        // Return original form_data
        $formData = is_string($application->form_data) 
            ? json_decode($application->form_data, true) 
            : $application->form_data;
        
        return [
            'permits' => $formData['selected_permits'] ?? [],
            'project_data' => [
                'location' => $formData['project_location'] ?? '',
                'land_area' => $formData['land_area'] ?? 0,
                'building_area' => $formData['building_area'] ?? 0,
                'investment_value' => $formData['investment_value'] ?? 0,
            ],
            'total_cost' => $application->quoted_price ?? 0,
        ];
    }
    
    private function updateLocationDetails($applicationId, $locationData)
    {
        ApplicationLocationDetail::updateOrCreate(
            ['application_id' => $applicationId],
            $locationData
        );
    }
    
    private function updateLegalityDocuments($applicationId, $documents)
    {
        foreach ($documents as $doc) {
            ApplicationLegalityDocument::updateOrCreate(
                [
                    'application_id' => $applicationId,
                    'document_category' => $doc['category'],
                ],
                [
                    'document_name' => $doc['name'],
                    'is_available' => $doc['is_available'] ?? false,
                    'document_number' => $doc['number'] ?? null,
                    'notes' => $doc['notes'] ?? null,
                ]
            );
        }
    }
}
```

---

### **FASE 3: View Enhancement**

#### 3.1. Form Revisi Paket: `resources/views/admin/permit-applications/revise.blade.php`

**Fitur Form:**
1. **Section 1: Alasan Revisi**
   - Dropdown: Technical Adjustment / Client Request / Cost Update
   - Textarea: Penjelasan detail
   
2. **Section 2: Daftar Izin (Dynamic)**
   - Bisa tambah/hapus izin
   - Per izin:
     - Pilih jenis izin (dropdown)
     - Service type (BizMark / Owned / Self)
     - Biaya (input number)
     - Estimasi waktu (input number + "hari")
   - Auto calculate total

3. **Section 3: Data Lokasi Lengkap**
   - Provinsi (dropdown/autocomplete)
   - Kabupaten/Kota (dependent dropdown)
   - Kecamatan (dependent dropdown)
   - Kelurahan
   - Alamat lengkap (textarea)
   - Kode pos
   - GPS Coordinates (optional - bisa dari map picker)
   - Zona/Kawasan (dropdown)
   - Status Lahan (dropdown: HGB, HGU, dll)
   - Nomor Sertifikat

4. **Section 4: Checklist Dokumen Legalitas**
   ```
   ☐ Sertifikat Tanah (HGB/HGU/Hak Milik)
     └─ Nomor: ______  Tanggal Terbit: ______
   ☐ Akta Pendirian Perusahaan
   ☐ NPWP Perusahaan
   ☐ NIB (Nomor Induk Berusaha)
   ☐ IMB Existing (jika ada)
   ☐ Surat Kuasa (jika diwakilkan)
   ☐ Site Plan / Gambar Lokasi
   ```

5. **Section 5: Breakdown Biaya**
   - Tabel breakdown per izin
   - Biaya konsultasi (optional)
   - Biaya survei (optional)
   - Total keseluruhan

6. **Section 6: Comparison View**
   - Side-by-side comparison:
     - Original Package vs Revised Package
     - Highlight changes (tambahan hijau, pengurangan merah)

---

### **FASE 4: Client Portal View**

#### 4.1. Halaman Review Revisi: `client/applications/revisions/{id}`

**Tampilan:**
- Alert notification: "Admin telah mengusulkan revisi paket Anda"
- Comparison table:
  - Kolom 1: Paket Original
  - Kolom 2: Paket Revisi (dengan highlight changes)
  - Kolom 3: Alasan perubahan
- Detail perubahan:
  - Izin yang ditambah (+)
  - Izin yang dikurangi (-)
  - Perubahan biaya
  - Perubahan estimasi waktu
- Action buttons:
  - ✅ Setuju (hijau)
  - ❌ Ajukan Perubahan (kuning)
  - 💬 Diskusi dengan Admin (biru)

---

## 📊 **WORKFLOW BARU**

```
Client Submit Package
         ↓
[Admin Review + Technical Analysis]
         ↓
    ┌─────────────────┐
    │ Need Revision?  │
    └────┬────────┬───┘
         │        │
        YES      NO
         │        │
         ↓        ↓
   [Create Revision] [Quote Original]
         ↓              ↓
   [Client Review]     │
         │              │
    ┌────┴────┐        │
    │ Action  │        │
    └─┬───┬───┘        │
      │   │            │
   Approve Reject     │
      │   │            │
      ↓   ↓            ↓
   [Updated Package]   │
         │              │
         └──────┬───────┘
                ↓
          [Quotation]
                ↓
         [Client Payment]
                ↓
       [Convert to Project]
```

---

## 🎯 **PRIORITAS IMPLEMENTASI**

### **HIGH PRIORITY** (Harus ada):
1. ✅ Database tables (revisions, location_details, legality_docs, quotation_items)
2. ✅ Controller untuk create/approve revision
3. ✅ Form revisi dengan dynamic permit list
4. ✅ Comparison view untuk client
5. ✅ Approval workflow

### **MEDIUM PRIORITY** (Penting tapi bisa fase 2):
1. ⚠️ Location details dengan GPS picker
2. ⚠️ Legality documents checklist & upload
3. ⚠️ Email notifications
4. ⚠️ History tracking semua revisi

### **LOW PRIORITY** (Nice to have):
1. 💡 Export comparison PDF
2. 💡 Auto-suggest pricing based on AI/historical data
3. 💡 Mobile app notification
4. 💡 Real-time chat untuk diskusi revisi

---

## 🔐 **PERMISSION & AUTHORIZATION**

```php
// gates/permissions yang perlu ditambahkan
Gate::define('create-package-revision', function ($user) {
    return $user->hasRole(['admin', 'manager', 'consultant']);
});

Gate::define('approve-package-revision', function ($user, $application) {
    return $user->id === $application->client_id;
});

Gate::define('view-quotation-breakdown', function ($user, $application) {
    return $user->hasRole('admin') || $user->id === $application->client_id;
});
```

---

## 📝 **NEXT STEPS**

1. **Create Migration Files**
   ```bash
   php artisan make:migration create_application_revisions_table
   php artisan make:migration create_application_location_details_table
   php artisan make:migration create_application_legality_documents_table
   php artisan make:migration create_quotation_items_table
   ```

2. **Create Models**
   ```bash
   php artisan make:model ApplicationRevision
   php artisan make:model ApplicationLocationDetail
   php artisan make:model ApplicationLegalityDocument
   php artisan make:model QuotationItem
   ```

3. **Create Controller**
   ```bash
   php artisan make:controller Admin/PackageRevisionController
   ```

4. **Create Views**
   - `admin/permit-applications/revise.blade.php`
   - `admin/permit-applications/partials/revision-form.blade.php`
   - `admin/permit-applications/partials/comparison-view.blade.php`
   - `client/applications/revisions/show.blade.php`

5. **Add Routes**
   ```php
   Route::prefix('admin')->group(function() {
       Route::get('permit-applications/{id}/revise', [PackageRevisionController::class, 'create'])
           ->name('admin.permit-applications.revise');
       Route::post('permit-applications/{id}/revisions', [PackageRevisionController::class, 'store'])
           ->name('admin.permit-applications.revisions.store');
   });
   
   Route::prefix('client')->group(function() {
       Route::get('applications/{application}/revisions/{revision}', [ClientRevisionController::class, 'show'])
           ->name('client.applications.revisions.show');
       Route::post('applications/{application}/revisions/{revision}/approve', [ClientRevisionController::class, 'approve'])
           ->name('client.applications.revisions.approve');
   });
   ```

---

## ✨ **EXPECTED BENEFITS**

1. **Untuk Admin/Konsultan:**
   - ✅ Fleksibilitas menyesuaikan paket setelah kajian teknis
   - ✅ Transparansi perubahan ke client
   - ✅ History tracking lengkap
   - ✅ Data lokasi dan legalitas terstruktur

2. **Untuk Client:**
   - ✅ Visibilitas jelas apa yang berubah
   - ✅ Bisa approve/reject perubahan
   - ✅ Breakdown biaya transparan
   - ✅ Estimasi waktu per izin

3. **Untuk Bisnis:**
   - ✅ Mengurangi dispute
   - ✅ Meningkatkan kepercayaan client
   - ✅ Proses lebih terstruktur
   - ✅ Data untuk reporting & analytics

---

**Apakah ingin saya mulai implementasi FASE 1 (Database Migration)?**
