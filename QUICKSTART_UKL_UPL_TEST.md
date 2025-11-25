# 🚀 Quick Start Guide - UKL/UPL Document Editing Test

## ✅ Status Implementasi

| Komponen | Status | Detail |
|----------|--------|--------|
| **Database** | ✅ Complete | Migrations executed successfully |
| **Backend** | ✅ Complete | Controllers, models, routes ready |
| **Form UI** | ✅ Complete | Conditional document editing section integrated |
| **Dokumentasi** | ✅ Complete | Full template guide available |
| **Helper Tool** | ✅ Complete | HTML criteria copy helper created |

---

## 📁 Files Created

### 1. **TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md**
   - 📍 Location: `/home/bizmark/bizmark.id/TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md`
   - 📝 Content: Complete documentation dengan:
     - Test overview & specifications
     - 22 kriteria penilaian detail (100 poin total)
     - Instruksi untuk kandidat
     - Struktur dokumen UKL/UPL standar
     - Cara membuat test di admin panel
     - Step-by-step evaluation workflow
     - Scoring examples
     - Troubleshooting guide

### 2. **ukl-upl-criteria-helper.html**
   - 📍 Location: `/home/bizmark/bizmark.id/public/templates/ukl-upl-criteria-helper.html`
   - 🌐 URL: `https://bizmark.id/templates/ukl-upl-criteria-helper.html`
   - ✨ Features:
     - Beautiful interactive UI dengan gradient design
     - Copy individual criteria dengan 1 klik
     - Copy all 22 criteria as JSON
     - Live stats: 22 kriteria, 100 poin, 120 menit, 70% passing
     - Responsive mobile-friendly
     - Toast notifications
     - JSON output viewer

---

## 🎯 Langkah Persiapan Template File

### Dokumen yang Harus Disiapkan:

#### 1. **UKL_UPL_Template_Broken.docx** (MANDATORY)
Dokumen Word dengan **kesalahan yang disengaja** untuk ditest:

**Kesalahan Formatting:**
- [ ] Font tidak konsisten (mix Arial, Calibri, Times New Roman berbagai size)
- [ ] Heading tidak menggunakan Styles (manual bold saja)
- [ ] Line spacing acak (ada yang 1.0, 1.15, 1.5, 2.0)
- [ ] Margin tidak standar (1cm-1cm-1cm-1cm)
- [ ] Page number hilang atau format salah
- [ ] Tidak ada header/footer

**Kesalahan Penomoran:**
- [ ] Penomoran BAB inkonsisten: "Bab 1", "BAB II", "bab III", "BAB 4"
- [ ] Sub-bab acak: "1.1", "1.2.", "1-3", "1,4"
- [ ] Tabel tidak diberi nomor atau acak: "Table 1", "Tabel 2.", "Tbl 3"
- [ ] Gambar tidak ada nomor atau format salah
- [ ] Daftar isi outdated atau manual

**Konten Kosong/Tidak Lengkap:**
- [ ] Rumus perhitungan hanya placeholder: `[RUMUS TSS DISINI]`
- [ ] Tabel kualitas air banyak sel kosong
- [ ] Tabel parameter lingkungan tidak ada data
- [ ] Baku mutu tidak dicantumkan
- [ ] Metodologi sampling hanya 1 paragraf singkat
- [ ] Koordinat lokasi: `XX°XX'XX" LS` (tidak diisi)
- [ ] Data pemrakarsa kosong

**Kesalahan Teknis:**
- [ ] Satuan salah: "mgl" seharusnya "mg/L"
- [ ] Nama parameter salah: "Total Suspended Solid" → "Solids"
- [ ] Referensi regulasi outdated: "PP 82 Tahun 2001" (sudah diganti PP 22/2021)
- [ ] Metode sampling tidak sesuai SNI
- [ ] Parameter kualitas udara salah unit (ppm vs µg/Nm³)
- [ ] pH ditulis sebagai "7,5" seharusnya "7.5" (titik bukan koma)
- [ ] Waktu sampling tidak logis: "Pagi pukul 23:00"

**Struktur yang Harus Ada:**
```
HALAMAN JUDUL (ada typo di nama perusahaan)
KATA PENGANTAR (format salah, tidak justified)
DAFTAR ISI (manual, page number salah)
DAFTAR TABEL (kosong)
DAFTAR GAMBAR (kosong)

BAB I PENDAHULUAN (penomoran 1.1 loncat ke 1.3)
BAB II DESKRIPSI (font Comic Sans pada 1 paragraf)
BAB III RONA LINGKUNGAN (tabel kualitas air banyak kosong)
BAB IV DAMPAK LINGKUNGAN (matriks dampak tidak ada)
BAB V UKL (beberapa strategi pengelolaan kosong: "[ISI STRATEGI]")
BAB VI UPL (parameter pemantauan tidak ada frekuensi)
BAB VII KESIMPULAN (hanya 2 kalimat, tidak substansial)

DAFTAR PUSTAKA (format tidak konsisten, ada yang APA, ada yang Harvard)
LAMPIRAN (tidak ada nomor lampiran)
```

**Contoh Tabel Kualitas Air yang Rusak:**
```
| Parameter | Satuan | Hasil | Baku Mutu | Ket |
|-----------|--------|-------|-----------|-----|
| pH        | -      |       |           |     |
| TSS       | mgl    | 45    |           |     |
| BOD       |        | 12.5  | 30        | MS  |
| COD       | mg/L   |       | 100       |     |
| Minyak    | mg/l   | 2.3   | 5         |     |
```

#### 2. **UKL_UPL_Template_ANSWER_KEY.docx** (OPTIONAL - untuk evaluator)
Dokumen berisi:
- ✅ Semua kesalahan yang sengaja dibuat (list dengan checkbox)
- ✅ Jawaban benar untuk setiap kesalahan
- ✅ Nilai/bobot per kesalahan
- ✅ Notes untuk evaluator tentang partial credit

---

## 🖥️ Cara Membuat Test (Step-by-Step)

### Option A: Manual Input via Form

1. **Navigate ke Admin Panel**
   ```
   URL: https://bizmark.id/admin/recruitment/tests/create
   ```

2. **Isi Form Dasar**
   - **Test Title:** `Document Editing Test - UKL/UPL Environmental Specialist`
   - **Test Type:** Pilih dropdown → `Document Editing`
   - **Position:** `Environmental Specialist` atau `Konsultan Lingkungan`
   - **Duration:** `120` menit
   - **Passing Score:** `70` %
   - **Instructions:** Copy dari `TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md` bagian "Instruksi untuk Kandidat"

3. **Upload Template File**
   - Klik **Choose File** di section "Template File (Word Document)"
   - Pilih file: `UKL_UPL_Template_Broken.docx`
   - Pastikan file < 10MB

4. **Tambah Kriteria Penilaian (22 kriteria)**

   **CARA CEPAT:** Buka helper tool
   ```
   URL: https://bizmark.id/templates/ukl-upl-criteria-helper.html
   ```
   Klik tombol individual "Copy" pada setiap kriteria, lalu paste ke form.

   **ATAU manual input:**

   **Kriteria 1-5: Formatting & Layout (25 poin)**
   
   Klik **"Tambah Kriteria"** → Isi:
   ```
   Category: Formatting & Layout
   Description: Font consistency (Times New Roman 12pt body, Arial 14-16pt heading)
   Points: 5
   Type: Technical
   ```
   
   Klik **"Tambah Kriteria"** lagi → Isi:
   ```
   Category: Formatting & Layout
   Description: Heading hierarchy (H1 untuk BAB, H2 untuk sub-bab, H3 untuk sub-sub-bab)
   Points: 5
   Type: Technical
   ```
   
   ... (Ulangi untuk semua 22 kriteria sesuai tabel di dokumentasi)

5. **Review & Submit**
   - Pastikan total poin = 100
   - Pastikan semua 22 kriteria terisi
   - Klik **"Create Test"**

### Option B: Using Helper Tool (RECOMMENDED)

1. **Buka Helper Tool**
   ```bash
   # Browser
   https://bizmark.id/templates/ukl-upl-criteria-helper.html
   ```

2. **Copy JSON All Criteria**
   - Scroll ke bawah
   - Klik tombol **"📋 Copy All 22 Criteria (JSON)"**
   - JSON dengan 22 kriteria akan di-copy ke clipboard

3. **Paste ke Developer Console (Auto-fill form)**
   ```javascript
   // Buka Console (F12)
   // Paste criteria JSON yang sudah di-copy
   const criteria = [
     { category: 'Formatting & Layout', description: '...', points: 5, type: 'Technical' },
     // ... 21 kriteria lainnya
   ];
   
   // Auto-populate form
   criteria.forEach((c, index) => {
       // Klik "Tambah Kriteria"
       document.getElementById('addCriteria').click();
       
       // Fill fields
       document.querySelector(`input[name="evaluation_criteria[${index}][category]"]`).value = c.category;
       document.querySelector(`textarea[name="evaluation_criteria[${index}][description]"]`).value = c.description;
       document.querySelector(`input[name="evaluation_criteria[${index}][points]"]`).value = c.points;
       document.querySelector(`select[name="evaluation_criteria[${index}][type]"]`).value = c.type;
   });
   ```

4. **Upload Template & Submit**
   - Upload file `UKL_UPL_Template_Broken.docx`
   - Review semua data
   - Submit

---

## 🧪 Testing Workflow

### A. Admin Creates Test
```
✅ Login sebagai admin
✅ Navigate: Dashboard → Recruitment → Tests → Create New
✅ Select type: "Document Editing"
✅ Fill form & upload template
✅ Add 22 criteria (total 100 points)
✅ Set duration: 120 minutes, passing: 70%
✅ Submit → Test template created
```

### B. Assign to Candidate
```
✅ Go to: Recruitment → Candidates
✅ Select candidate
✅ Assign test: UKL/UPL Document Editing
✅ Set deadline
✅ Send invitation
```

### C. Candidate Takes Test
```
✅ Candidate receives email/notification
✅ Click test link
✅ Download template (timer starts automatically)
✅ Work on document (120 minutes):
   - Fix formatting & numbering
   - Complete missing content
   - Add technical comments
✅ Upload before timer expires
✅ Status: "Pending Manual Evaluation"
```

### D. HR/Evaluator Reviews
```
✅ Dashboard → Test Sessions → Filter "Pending Evaluation"
✅ Open submission
✅ Download:
   - Original template (reference)
   - Candidate submission
   - Answer key (if available)
✅ Open evaluation form
✅ Score each of 22 criteria (0 to max points)
✅ Add evaluator notes
✅ Submit evaluation
✅ System auto-calculates:
   - Total score (e.g., 85/100)
   - Percentage (85%)
   - Pass/Fail (PASS if ≥70%)
✅ Candidate notified
```

---

## 📊 Kriteria Penilaian Ringkas

| # | Kategori | Kriteria | Poin | Total |
|---|----------|----------|------|-------|
| 1-5 | **Formatting & Layout** | Font, heading, spacing, page numbering, header/footer | 5 each | **25** |
| 6-10 | **Penomoran & Struktur** | BAB, sub-bab, tabel, gambar, daftar isi | 7,6,5,4,3 | **25** |
| 11-15 | **Content Completion** | Rumus, tabel data, baku mutu, metodologi, dummy data | 8,8,6,5,3 | **30** |
| 16-19 | **Technical Review** | Identifikasi error, saran, compliance, critical issues | 5,4,3,3 | **15** |
| 20-22 | **Overall Quality** | Profesionalisme, readability, completeness | 2,2,1 | **5** |
| | | | **TOTAL** | **100** |

**Passing Score:** 70/100 (70%)

---

## 🔗 Quick Links

| Resource | URL/Path |
|----------|----------|
| **Admin Panel - Create Test** | https://bizmark.id/admin/recruitment/tests/create |
| **Criteria Helper Tool** | https://bizmark.id/templates/ukl-upl-criteria-helper.html |
| **Full Documentation** | `/home/bizmark/bizmark.id/TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md` |
| **Routes List** | `php artisan route:list --name=test` |
| **Test Template Model** | `app/Models/TestTemplate.php` |
| **Test Session Model** | `app/Models/TestSession.php` |
| **Document Editing Controller** | `app/Http/Controllers/Admin/DocumentEditingTestController.php` |
| **Test Management Controller** | `app/Http/Controllers/Admin/TestManagementController.php` |

---

## 🎯 Next Steps

1. ✅ **Buat dokumen template** (`UKL_UPL_Template_Broken.docx`) dengan kesalahan yang disengaja
2. ✅ **Upload dokumen** via admin panel
3. ✅ **Test workflow lengkap:**
   - Create test
   - Assign to test candidate
   - Download, edit, upload
   - Evaluate submission
4. ✅ **Refine kriteria** jika diperlukan berdasarkan hasil test
5. ✅ **Create answer key** untuk konsistensi evaluasi

---

## ⚠️ Important Notes

### Untuk HR/Admin:
- Dokumen template **HARUS berisi kesalahan** agar kandidat punya yang diperbaiki
- Simpan answer key untuk konsistensi evaluasi antar kandidat
- Pastikan evaluator memahami kriteria sebelum menilai
- Review 2-3 submission pertama untuk kalibrasi scoring

### Untuk Kandidat:
- Test ini **bukan open book test**, tapi boleh gunakan internet untuk referensi regulasi
- Fokus pada **high-value items** (Content Completion = 30 poin)
- Gunakan fitur Word **Comments** untuk technical review
- Save dokumen secara berkala (auto-save enabled)
- Upload **minimum 5 menit sebelum deadline** untuk antisipasi

### Technical Requirements:
- File format: `.docx` (tidak support .doc lama)
- Max file size: 10MB (jika terlalu besar, compress images)
- Browser: Chrome/Firefox/Edge terbaru (IE tidak support)
- Internet connection: Stable untuk upload file

---

## 📞 Support

Jika mengalami issue:
1. Check troubleshooting di `TEMPLATE_DOCUMENT_EDITING_UKL_UPL.md`
2. Review `DOCUMENT_EDITING_TEST_ANALYSIS.md` untuk technical details
3. Check console browser (F12) untuk error messages
4. Contact: Tech Team / HR Admin

---

## ✅ Checklist Siap Launch

- [x] Database migrations executed
- [x] Models updated (TestTemplate, TestSession)
- [x] Controllers created (DocumentEditingTestController, TestManagementController)
- [x] Routes registered and verified
- [x] Form UI integrated with conditional sections
- [x] JavaScript criteria builder working
- [x] Documentation complete
- [x] Helper tool created
- [ ] Template dokumen `UKL_UPL_Template_Broken.docx` disiapkan ← **ANDA DI SINI**
- [ ] Test workflow end-to-end
- [ ] Evaluator training (understanding criteria)

---

**Template siap digunakan! Tinggal upload dokumen UKL/UPL yang sudah disiapkan.**

*Last Updated: 2025-11-23*
