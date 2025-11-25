# ✅ UKL/UPL Document Editing Test - TEMPLATE CREATED

## 🎉 Status: TEMPLATE BERHASIL DIBUAT!

Template test UKL/UPL sudah dibuat di database dengan ID: **#4**

---

## 📋 Template Details

| Parameter | Value |
|-----------|-------|
| **ID** | 4 |
| **Title** | Document Editing Test - UKL/UPL Environmental Specialist |
| **Test Type** | document-editing |
| **Duration** | 120 minutes (2 jam) |
| **Passing Score** | 70% |
| **Total Criteria** | 22 kriteria |
| **Total Points** | 100 poin |
| **Status** | ⚠️ INACTIVE (menunggu upload dokumen) |
| **Template File** | ⚠️ BELUM DIUPLOAD |

---

## 📝 Yang Sudah Dibuat

### ✅ 1. Database Template (DONE)
- [x] Test template created dengan ID #4
- [x] 22 kriteria penilaian lengkap:
  - **Formatting & Layout** (5 kriteria, 25 poin)
  - **Penomoran & Struktur** (5 kriteria, 25 poin)
  - **Content Completion** (5 kriteria, 30 poin)
  - **Technical Review** (4 kriteria, 15 poin)
  - **Overall Quality** (3 kriteria, 5 poin)
- [x] Instruksi lengkap untuk kandidat
- [x] Passing score 70% dan duration 120 menit

### ✅ 2. Dokumentasi Lengkap (DONE)
- [x] **TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md** - Full documentation
- [x] **QUICKSTART_UKL_UPL_TEST.md** - Quick start guide
- [x] **ukl-upl-criteria-helper.html** - Interactive criteria helper

### ✅ 3. Helper Script (DONE)
- [x] **update_ukl_upl_template.php** - Script untuk upload dokumen

---

## 🚀 NEXT STEP: Upload Dokumen Template

### Option A: Via Helper Script (RECOMMENDED)

1. **Siapkan file dokumen**
   ```bash
   # Buat file: UKL_UPL_Template_Broken.docx
   # dengan kesalahan-kesalahan yang disengaja
   ```

2. **Upload ke storage**
   ```bash
   # Copy file ke directory
   cp UKL_UPL_Template_Broken.docx /home/bizmark/bizmark.id/storage/app/test-templates/
   ```

3. **Run update script**
   ```bash
   cd /home/bizmark/bizmark.id
   php update_ukl_upl_template.php
   
   # Script akan:
   # - List semua file di test-templates/
   # - Tanya nama file yang mau diupload
   # - Konfirmasi
   # - Update template
   # - Aktivasi template
   ```

### Option B: Via Admin Panel

1. **Navigate ke edit page**
   ```
   URL: https://bizmark.id/admin/recruitment/tests/4/edit
   ```

2. **Upload dokumen**
   - Scroll ke section "Template File (Word Document)"
   - Klik "Choose File"
   - Pilih: `UKL_UPL_Template_Broken.docx`
   - Klik "Update Test"

3. **Aktivasi template**
   - Centang checkbox "Template Aktif"
   - Save

### Option C: Manual via Tinker

```bash
php artisan tinker

# Update template
$template = \App\Models\TestTemplate::find(4);
$template->template_file_path = 'test-templates/UKL_UPL_Template_Broken.docx';
$template->is_active = true;
$template->save();
```

---

## 📄 Membuat Dokumen Template

Dokumen harus dibuat dengan **sengaja berisi kesalahan** untuk ditest. Lihat panduan lengkap di:
- `TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md` (section "Template File yang Harus Disiapkan")
- `QUICKSTART_UKL_UPL_TEST.md` (section "Langkah Persiapan Template File")

### Kesalahan yang Harus Ada:

#### ❌ Formatting Errors (25 poin)
- [ ] Font tidak konsisten (mix Arial, Calibri, Times New Roman)
- [ ] Heading tidak menggunakan Styles
- [ ] Line spacing acak (1.0, 1.15, 1.5, 2.0)
- [ ] Margin tidak standar
- [ ] Page numbering hilang/salah
- [ ] Tidak ada header/footer

#### ❌ Numbering Errors (25 poin)
- [ ] Penomoran BAB inkonsisten: "Bab 1", "BAB II", "bab III"
- [ ] Sub-bab acak: "1.1", "1.2.", "1-3"
- [ ] Tabel tidak diberi nomor
- [ ] Gambar tidak ada nomor
- [ ] Daftar isi outdated

#### ❌ Missing Content (30 poin)
- [ ] Rumus hanya placeholder: `[RUMUS DISINI]`
- [ ] Tabel kualitas air banyak sel kosong
- [ ] Baku mutu tidak ada
- [ ] Metodologi sampling minimal
- [ ] Koordinat lokasi kosong

#### ❌ Technical Errors (15 poin)
- [ ] Satuan salah: "mgl" → "mg/L"
- [ ] Parameter salah: "Suspended Solid" → "Solids"
- [ ] Regulasi outdated: PP 82/2001 → PP 22/2021
- [ ] Metode sampling tidak sesuai SNI
- [ ] Waktu sampling tidak logis

---

## 🔗 Quick Links

| Resource | URL |
|----------|-----|
| **View Template** | https://bizmark.id/admin/recruitment/tests/4 |
| **Edit Template** | https://bizmark.id/admin/recruitment/tests/4/edit |
| **All Tests** | https://bizmark.id/admin/recruitment/tests |
| **Helper Tool** | https://bizmark.id/templates/ukl-upl-criteria-helper.html |
| **Full Docs** | `/home/bizmark/bizmark.id/TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md` |
| **Quick Start** | `/home/bizmark/bizmark.id/QUICKSTART_UKL_UPL_TEST.md` |

---

## ✅ Verification Commands

```bash
# Check template in database
php artisan tinker --execute="App\Models\TestTemplate::find(4)"

# Check criteria count
php artisan tinker --execute="count(App\Models\TestTemplate::find(4)->evaluation_criteria['criteria'])"

# Check total points
php artisan tinker --execute="App\Models\TestTemplate::find(4)->evaluation_criteria['total_points']"

# List files in storage
php artisan tinker --execute="Storage::disk('private')->files('test-templates')"
```

---

## 📊 Template Structure (Preview)

```
Document Editing Test - UKL/UPL Environmental Specialist
├── Basic Info
│   ├── Duration: 120 minutes
│   ├── Passing Score: 70%
│   └── Type: document-editing
│
├── Instructions (Full text included)
│   ├── Latar Belakang
│   ├── 5 Tugas Utama
│   ├── Aturan Pengerjaan
│   └── Standar Dokumen
│
└── Evaluation Criteria (22 items, 100 points)
    ├── Formatting & Layout (5 items, 25 pts)
    ├── Penomoran & Struktur (5 items, 25 pts)
    ├── Content Completion (5 items, 30 pts)
    ├── Technical Review (4 items, 15 pts)
    └── Overall Quality (3 items, 5 pts)
```

---

## 🎯 Workflow Setelah Upload Dokumen

```
1. Admin uploads template document → Template active
2. Admin assigns test to candidate
3. Candidate downloads template → Timer starts (2 hours)
4. Candidate fixes document:
   ✓ Fix formatting & numbering
   ✓ Complete missing content
   ✓ Add technical comments
5. Candidate uploads before deadline
6. Status: "Pending Manual Evaluation"
7. HR/Evaluator reviews submission
8. HR scores based on 22 criteria
9. System calculates: Total (e.g., 85/100 = 85%)
10. Result: PASS (if ≥70%) or FAIL
11. Candidate notified
```

---

## 📞 Support

**Seeder File:** `/home/bizmark/bizmark.id/database/seeders/UklUplDocumentEditingTestSeeder.php`

**Update Script:** `/home/bizmark/bizmark.id/update_ukl_upl_template.php`

**Re-run Seeder:**
```bash
# Jika butuh create ulang (akan skip jika sudah ada)
php artisan db:seed --class=UklUplDocumentEditingTestSeeder
```

---

## 🎉 Summary

✅ **DONE:**
- Database template created (ID: 4)
- 22 evaluation criteria configured
- Full instructions included
- Documentation complete
- Helper tools ready

⏳ **TODO:**
- [ ] Buat dokumen Word: `UKL_UPL_Template_Broken.docx`
- [ ] Upload dokumen via script atau admin panel
- [ ] Aktivasi template (is_active = true)
- [ ] Test full workflow

---

**STATUS: SIAP UNTUK UPLOAD DOKUMEN!**

*Created: 2025-11-23 17:47:00*
*Template ID: 4*
