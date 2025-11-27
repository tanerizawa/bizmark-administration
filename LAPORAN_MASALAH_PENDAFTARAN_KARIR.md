# LAPORAN INVESTIGASI: Masalah Pendaftaran Lamaran Kerja

## 📋 Ringkasan Masalah
Beberapa kandidat melaporkan gagal mendaftar (redirect ke halaman awal setelah submit) sementara kandidat lain berhasil.

## 🔍 Hasil Investigasi

### 1. Error dari Log (23-24 November 2025)

#### Error A: NOT NULL constraint - `full_name`
```
Tanggal: 23 Nov 2025 13:06:39
Email: studiomalaka@gmail.com
Error: null value in column "full_name" violates not-null constraint
```

#### Error B: NOT NULL constraint - `education_level`
```
Tanggal: 23 Nov 2025 13:07:24
Email: test.candidate@example.com

Tanggal: 24 Nov 2025 16:48:07  
Email: test.automation@bizmark.test

Error: null value in column "education_level" violates not-null constraint
```

### 2. Analisis Database Schema

Field yang WAJIB diisi (NOT NULL tanpa default):
- ✅ job_vacancy_id
- ✅ full_name
- ✅ email
- ✅ phone
- ✅ education_level
- ✅ major
- ✅ institution

### 3. Masalah yang Ditemukan

#### ❌ MASALAH #1: Multi-Step Form Tanpa Validasi JavaScript
**Lokasi**: `resources/views/career/apply.blade.php`

```javascript
nextStep() {
    if (this.currentStep < this.totalSteps) this.currentStep++;
}
```

**Dampak**: 
- User bisa klik tombol "Selanjutnya" tanpa mengisi field required
- HTML5 `required` attribute tidak di-enforce saat pindah step
- User sampai ke step 4 (submit) dengan data tidak lengkap
- Form submission gagal di backend
- Error ditangani dengan redirect ke career.show tanpa pesan error jelas

#### ❌ MASALAH #2: Error Handling yang Kurang Informatif
**Lokasi**: `app/Http/Controllers/JobApplicationController.php`

```php
catch (\Exception $e) {
    $vacancy = JobVacancy::find($request->job_vacancy_id);
    
    if ($vacancy && $vacancy->google_form_url) {
        return redirect($vacancy->google_form_url)
            ->with('info', 'Terjadi kesalahan. Silakan lengkapi lamaran melalui Google Form.');
    }

    return back()->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
}
```

**Dampak**:
- Jika vacancy punya `google_form_url`, user di-redirect ke Google Form (terlihat seperti kembali ke halaman awal)
- Jika tidak punya google_form_url, error ditampilkan tapi terlalu teknis untuk user

#### ❌ MASALAH #3: Tidak Ada Validasi Per-Step
**Dampak**:
- User dengan browser lama/JavaScript disabled bisa bypass required fields
- Autofill browser mungkin tidak mengisi semua field
- Copy-paste antar field bisa skip validation

## 🎯 Skenario Kegagalan

### Scenario A: User Terburu-buru
1. User buka form
2. Step 1: Isi nama, email, phone → klik "Selanjutnya" ✅
3. Step 2: **Skip education_level** → langsung klik "Selanjutnya" ⚠️
4. Step 3: Isi experience → klik "Selanjutnya" ✅
5. Step 4: Upload CV → klik "Kirim Lamaran" ⚠️
6. Backend validation fail → catch exception
7. Redirect ke Google Form atau career.show
8. **User bingung: "Kok balik ke awal?"**

### Scenario B: Browser Autofill Tidak Lengkap
1. Browser autofill isi beberapa field
2. Field `education_level`, `major`, atau `institution` tidak terisi
3. User tidak notice karena scroll cepat
4. Submit → gagal → redirect
5. **User frustrated: "Kenapa tidak bisa submit?"**

### Scenario C: JavaScript Disabled/Error
1. User dengan browser lama atau ad blocker agresif
2. Alpine.js tidak load properly
3. Multi-step navigation tidak berfungsi
4. Form terlihat aneh atau tidak bisa navigate
5. **User abandon form**

## ✅ Kandidat yang Berhasil

User yang **teliti** dan **mengisi semua field required** berhasil submit tanpa masalah karena:
- Validasi backend bekerja dengan baik
- Field yang diisi lengkap sesuai schema database
- Create job application berhasil

## 📊 Kesimpulan

### Penyebab Utama:
1. **Validasi Frontend Lemah**: Tidak ada validasi JavaScript saat pindah step
2. **UX Error Handling Buruk**: User tidak diberi feedback jelas kenapa gagal
3. **Multi-step Form Risk**: User bisa skip field tanpa sadar

### Tingkat Keparahan:
- 🔴 **CRITICAL**: Berdampak langsung pada konversi kandidat
- 🔴 **HIGH USER IMPACT**: User frustasi dan abandon application
- 🟡 **BUSINESS IMPACT**: Kehilangan kandidat potensial

## 💡 Rekomendasi Perbaikan

### Priority 1 - URGENT:
1. ✅ Tambah validasi JavaScript per-step sebelum nextStep()
2. ✅ Tampilkan error message yang user-friendly
3. ✅ Highlight field yang belum diisi dengan visual cue
4. ✅ Disable tombol "Selanjutnya" jika field required kosong

### Priority 2 - HIGH:
1. ✅ Improve error handling di backend dengan pesan lebih spesifik
2. ✅ Log error lebih detail untuk debugging
3. ✅ Tambah form validation summary di setiap step

### Priority 3 - MEDIUM:
1. ✅ Tambah auto-save draft ke localStorage
2. ✅ Progress bar dengan indicator field completion
3. ✅ Konfirmasi sebelum meninggalkan page dengan data belum tersimpan

## �� Action Plan

**Immediate Fix** (Target: 30 menit):
- [ ] Tambah validasi JavaScript di nextStep()
- [ ] Perbaiki error handling controller
- [ ] Test dengan berbagai browser

**Short-term** (Target: 2 jam):
- [ ] Tambah visual indicator untuk required fields
- [ ] Improve error messages
- [ ] Add form validation helper

**Long-term** (Target: 1 hari):
- [ ] Implement form draft saving
- [ ] Add progress tracking
- [ ] Comprehensive E2E testing

---
**Dibuat**: 26 November 2025
**Investigator**: AI Assistant
**Status**: Ready for Fix
