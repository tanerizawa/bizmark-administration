# Improved Permit Templates - Documentation

## 📋 Overview
Perbaikan sistem Template Izin agar lebih sesuai dengan logika sistem, best practice perizinan di Indonesia, dan kebutuhan aktual project.

## 🎯 Masalah Sebelumnya

### 1. **Template Tidak Realistis**
- Template lama tidak mencerminkan alur perizinan yang sebenarnya
- Tidak ada sertifikat tanah sebagai prasyarat awal
- Tidak ada PKKPR (kesesuaian tata ruang) yang wajib
- Urutan izin tidak logis

### 2. **Kurang Dependencies**
- Template tidak memiliki dependencies yang jelas
- Tidak ada logical flow yang terstruktur
- User bingung izin mana yang harus diurus dulu

### 3. **Estimasi Tidak Akurat**
- `estimated_days` kosong/null
- Tidak ada estimasi biaya per item
- Sulit untuk project planning

### 4. **Tidak Ada Goal Permit**
- Tidak jelas izin mana yang menjadi tujuan akhir
- Semua izin terlihat sama pentingnya

## ✅ Solusi yang Diterapkan

### **Template 1: UKL-UPL Pabrik/Industri Lengkap**

**Use Case**: Pembangunan pabrik manufaktur, industri pengolahan, warehouse besar (> 1 hektar)

**Alur Lengkap (9 Izin)**:
```
1. Sertifikat Tanah (60 hari, Rp 10jt)
   ↓
2. Pertek BPN (14 hari, Rp 5jt) ←── depends on #1
   ↓
3. PKKPR (14 hari, Rp 5jt) ←── depends on #1
   ↓
4. Siteplan (14 hari, Rp 6jt) ←── depends on #3
   ↓
5. Andalalin (30 hari, Rp 30jt) ←── parallel dengan #4
   ↓
6. PBG (30 hari, Rp 30jt) ←── depends on #4, optional on #5
   ↓
7. SLF (14 hari, Rp 12jt) ←── depends on #6
   ↓
8. UKL-UPL [GOAL] (14 hari, Rp 10jt) ←── depends on #7
   ↓
9. Izin Operasional (14 hari, Rp 8jt) ←── depends on #8

Total Estimasi: 204 hari, Rp 116 juta
```

**Dependencies Logic**:
- **Mandatory**: Sertifikat → PKKPR → Siteplan → PBG → SLF → UKL-UPL → Operasional
- **Optional**: Andalalin (bisa parallel, tergantung lokasi)

---

### **Template 2: AMDAL Proyek Strategis Nasional**

**Use Case**: Proyek berdampak lingkungan signifikan (pembangkit listrik, pabrik kimia, infrastruktur strategis)

**Alur Lengkap (7 Izin)**:
```
1. Sertifikat Tanah (60 hari, Rp 15jt)
   ↓
2. PKKPR (14 hari, Rp 8jt) ←── depends on #1
   ↓
3. AMDAL [GOAL] (75 hari, Rp 125jt) ←── depends on #2
   ↓
4. Izin Lingkungan (30 hari, Rp 8jt) ←── depends on #3
   ↓
5. PBG (30 hari, Rp 40jt) ←── depends on #4
   ↓
6. SLF (14 hari, Rp 15jt) ←── depends on #5
   ↓
7. Izin Operasional (14 hari, Rp 12jt) ←── depends on #6

Total Estimasi: 237 hari, Rp 223 juta
```

**Key Points**:
- AMDAL adalah GOAL karena paling kritikal
- AMDAL memakan waktu terlama (75 hari)
- Biaya AMDAL paling besar (Rp 125jt)
- Setelah AMDAL disetujui, proses lanjutan lebih cepat

---

### **Template 3: Startup Bisnis Lengkap (NIB + Office)**

**Use Case**: Startup, UMKM berkembang, bisnis baru dengan kantor fisik

**Alur Lengkap (6 Izin)**:
```
1. NIB (1 hari, Rp 500rb)
   ↓
2. TDI (7 hari, Rp 3jt) ←── depends on #1 (jika industri)
   ↓
3. PBG (30 hari, Rp 15jt) ←── parallel (jika ada renovasi kantor)
   ↓
4. SLF (14 hari, Rp 8jt) ←── depends on #3
   ↓
5. SPPL (7 hari, Rp 1jt) ←── parallel (untuk UMKM)
   ↓
6. Izin Operasional [GOAL] (14 hari, Rp 5jt) ←── depends on #4 & #5

Total Estimasi: 73 hari, Rp 32,5 juta
```

**Key Points**:
- NIB wajib pertama (identitas usaha)
- PBG dan SPPL bisa paralel
- Cocok untuk startup yang menyewa office space
- Biaya paling terjangkau

---

### **Template 4: Bangunan Komersial (Mall/Hotel/Apartemen)**

**Use Case**: Properti komersial, mixed-use building, high-rise

**Alur Lengkap (7 Izin)**:
```
1. Sertifikat Tanah (60 hari, Rp 12jt)
   ↓
2. PKKPR (14 hari, Rp 6jt) ←── depends on #1
   ↓
3. Siteplan (14 hari, Rp 8jt) ←── depends on #2
   ↓
4. Andalalin (30 hari, Rp 35jt) ←── parallel dengan #3
5. UKL-UPL (14 hari, Rp 10jt) ←── parallel dengan #4
   ↓
6. PBG (30 hari, Rp 35jt) ←── depends on #3, #4 (optional), #5
   ↓
7. SLF [GOAL] (14 hari, Rp 15jt) ←── depends on #6

Total Estimasi: 176 hari, Rp 121 juta
```

**Key Points**:
- SLF adalah GOAL (izin untuk operasional gedung)
- Andalalin wajib untuk bangunan komersial (traffic impact)
- UKL-UPL wajib sebelum PBG
- Cocok untuk developer properti

---

## 🏗️ Struktur Dependencies

### **Jenis Dependencies**

1. **MANDATORY** (`dependency_type = 'MANDATORY'`)
   - Prasyarat WAJIB diselesaikan
   - Tidak bisa proceed tanpa ini
   - Contoh: PBG depends on Siteplan

2. **OPTIONAL** (`dependency_type = 'OPTIONAL'`)
   - Direkomendasikan tapi bisa dilewati
   - Atau bisa dikerjakan paralel
   - Contoh: Andalalin bisa parallel dengan Siteplan

### **Logical Flow Rules**

```
1. Land Prerequisites (MANDATORY)
   Sertifikat Tanah → Pertek BPN / PKKPR
   
2. Spatial Planning (MANDATORY)
   PKKPR → Siteplan
   
3. Environmental (MANDATORY/OPTIONAL tergantung skala)
   UKL-UPL / AMDAL / SPPL
   
4. Building (MANDATORY untuk konstruksi)
   Siteplan → PBG → SLF
   
5. Traffic (OPTIONAL tergantung lokasi)
   Andalalin (bisa paralel atau sebelum PBG)
   
6. Operational (MANDATORY untuk bisnis)
   SLF + Izin Lingkungan → Izin Operasional
```

---

## 🎨 Best Practice yang Diterapkan

### 1. **Always Start with Land**
- Sertifikat Tanah adalah prasyarat #1
- Tidak ada gunanya urus izin lain tanpa sertifikat
- Realistis dengan kondisi lapangan

### 2. **Spatial Planning is Critical**
- PKKPR wajib untuk memastikan kegiatan sesuai RTRW
- Banyak proyek gagal karena tidak sesuai tata ruang
- Harus diurus di awal sebelum design

### 3. **Environmental Before Building**
- UKL-UPL/AMDAL harus disetujui sebelum konstruksi
- Sesuai PP 22/2021
- Mencegah pembongkaran jika nanti ditolak

### 4. **Building Flow**
- Siteplan → PBG → Konstruksi → SLF
- Standar flow perizinan bangunan
- SLF wajib sebelum operasional

### 5. **Business Last**
- NIB bisa di awal (identitas usaha)
- Tapi Izin Operasional harus paling akhir
- Setelah semua prasyarat teknis terpenuhi

---

## 💡 Saran & Recommendations

### **1. Tambahkan Template Spesifik Lainnya**

```php
// Template Mining/Pertambangan
- IUP (Izin Usaha Pertambangan)
- AMDAL Pertambangan
- Izin Lingkungan Khusus
- Reklamasi & Pasca Tambang

// Template Food & Beverage
- NIB
- PIRT/MD (izin edar makanan)
- Sertifikat Halal
- Izin Operasional Restoran

// Template Healthcare
- NIB
- Izin Klinik/RS
- Izin Alat Kesehatan
- Sertifikat Akreditasi
```

### **2. Add Template Versioning**

```php
// PermitTemplate model
protected $fillable = [
    'version', // untuk track perubahan regulasi
    'valid_from', // berlaku sejak
    'valid_until', // kadaluarsa jika regulasi berubah
];
```

### **3. Template Comparison Feature**

Fitur untuk compare 2 template:
- Beda jumlah izin
- Beda estimasi waktu
- Beda estimasi biaya
- Membantu user pilih template yang tepat

### **4. Template Success Rate**

Track berapa project yang sukses pakai template tertentu:
```php
'usage_count' => 10, // sudah dipakai 10x
'success_rate' => 0.8, // 80% sukses approve
'avg_actual_days' => 220, // rata-rata aktual 220 hari (vs estimasi 204)
```

### **5. Template Checklist**

Setiap template punya checklist dokumen:
```php
'required_documents' => [
    'sertifikat_tanah' => 'Sertifikat Hak Milik/HGB',
    'ktp_direktur' => 'KTP Direktur',
    'akta_perusahaan' => 'Akta Pendirian PT/CV',
    'npwp' => 'NPWP Perusahaan',
    'gambar_teknis' => 'Gambar Arsitektur & Struktur',
]
```

### **6. Template Cost Calculator**

Hitung total biaya berdasarkan luas lahan/bangunan:
```php
// Formula
$cost_per_sqm = 50000; // Rp 50rb/m2
$land_area = 5000; // m2
$total_estimated_cost = $template->total_cost + ($cost_per_sqm * $land_area);
```

### **7. Template Timeline Visualization**

Gantt chart untuk visualisasi timeline:
```
Month 1-2: Sertifikat & Pertek BPN
Month 3: PKKPR & Siteplan
Month 4-5: Andalalin & PBG
Month 6: SLF & UKL-UPL
Month 7: Izin Operasional
```

### **8. Template Consultation Notes**

Tips dari konsultan untuk setiap template:
```php
'consultation_notes' => [
    'tips' => 'Urus Andalalin sedini mungkin, sering bottleneck',
    'common_issues' => 'PKKPR sering ditolak jika tidak sesuai RTRW',
    'time_saving' => 'PBG dan Andalalin bisa parallel untuk hemat waktu',
    'cost_saving' => 'Gunakan konsultan terakreditasi untuk AMDAL',
]
```

---

## 📊 Comparison: Old vs New Templates

| Aspek | Template Lama | Template Baru |
|-------|--------------|--------------|
| **Alur** | Tidak jelas | Structured dengan dependencies |
| **Prasyarat Awal** | Langsung ke izin teknis | Dimulai dari Sertifikat Tanah |
| **Goal Permit** | Tidak ada | Jelas ditandai [GOAL] |
| **Dependencies** | Tidak ada | 4-8 dependencies per template |
| **Estimasi Waktu** | Kosong/null | Lengkap per item |
| **Estimasi Biaya** | Kosong/null | Lengkap per item |
| **Deskripsi** | Singkat | Lengkap dengan use case |
| **Category** | Generic | Specific (industrial, strategic, business, commercial) |
| **Realistic** | ❌ | ✅ |

---

## 🚀 Implementation

### **Jalankan Seeder**

```bash
docker exec bizmark_app php artisan db:seed --class=ImprovedPermitTemplatesSeeder
```

### **Verifikasi**

```bash
docker exec bizmark_app php artisan tinker --execute="
\$template = App\Models\PermitTemplate::with(['items', 'dependencies'])->find(7);
echo \"Template: {\$template->name}\n\";
echo \"Items: {\$template->items->count()}\n\";
echo \"Dependencies: {\$template->dependencies->count()}\n\";
"
```

### **Apply ke Project**

```php
// ProjectController
public function applyTemplate(Request $request, Project $project)
{
    $template = PermitTemplate::findOrFail($request->template_id);
    
    // Create permits from template items
    foreach ($template->items as $item) {
        ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $item->permit_type_id,
            'sequence_order' => $item->sequence_order,
            'is_goal_permit' => $item->is_goal_permit,
            'estimated_days' => $item->estimated_days,
            'estimated_cost' => $item->estimated_cost,
            'status' => 'NOT_STARTED',
        ]);
    }
    
    // Create dependencies
    foreach ($template->dependencies as $dep) {
        // Map template item IDs to project permit IDs
        // ...
    }
    
    $template->incrementUsage();
    
    return redirect()->back()->with('success', 'Template applied successfully');
}
```

---

## ✅ Results

**4 Template Baru Dibuat:**
1. ✅ UKL-UPL Pabrik/Industri Lengkap (9 izin, 8 dependencies)
2. ✅ AMDAL Proyek Strategis (7 izin, 6 dependencies)
3. ✅ Startup Bisnis Lengkap (6 izin, 4 dependencies)
4. ✅ Bangunan Komersial (7 izin, 6 dependencies)

**Total**:
- 29 permit items
- 24 dependencies
- 100% dengan estimasi waktu & biaya
- 100% dengan goal permit yang jelas

---

## 📞 Next Steps

1. ✅ Seeder sudah dijalankan
2. 🔄 Update UI template index untuk show dependencies
3. 🔄 Add flow chart visualization
4. 🔄 Add "Apply to Project" button
5. 🔄 Add template comparison feature
6. 🔄 Add success rate tracking

---

Last Updated: October 3, 2025
Version: 2.0.0
