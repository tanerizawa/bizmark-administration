# Log Perbaikan Relasi Proyek-Institusi

**Tanggal Perbaikan:** 3 Oktober 2025  
**Executed By:** System Administrator

## 🔍 Analisis Masalah

### Kesalahan yang Ditemukan:

Setelah update data institusi, ditemukan beberapa proyek yang memiliki relasi institusi yang **SALAH** dan tidak sesuai dengan kewenangan instansi terkait.

## ❌ Kesalahan yang Diperbaiki

### 1. **Proyek UKL-UPL (4 proyek)**
- **ID Proyek:** 41, 42, 43, 44
- **Nama:** Pekerjaan UKL UPL
- **Institusi Lama:** Dinas Tenaga Kerja dan Transmigrasi (DISNAKER) ❌
- **Institusi Baru:** Dinas Lingkungan Hidup Kabupaten Karawang (DLH) ✅
- **Alasan Perubahan:** 
  - UKL-UPL (Upaya Pengelolaan Lingkungan - Upaya Pemantauan Lingkungan) adalah dokumen perizinan lingkungan
  - Kewenangan penerbitan dan persetujuan ada di **Dinas Lingkungan Hidup**
  - DISNAKER tidak memiliki kewenangan untuk UKL-UPL

### 2. **Proyek Penyimpanan Limbah B3**
- **ID Proyek:** 46
- **Nama:** Pekerjaan Penyimpanan Limbah B3
- **Institusi Lama:** Dinas Tenaga Kerja dan Transmigrasi (DISNAKER) ❌
- **Institusi Baru:** Dinas Lingkungan Hidup Kabupaten Karawang (DLH) ✅
- **Alasan Perubahan:**
  - B3 (Bahan Berbahaya dan Beracun) adalah kewenangan lingkungan hidup
  - Perizinan penyimpanan limbah B3 dikeluarkan oleh **Dinas Lingkungan Hidup**
  - Sesuai dengan PP 22/2021 tentang Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup

### 3. **Proyek Pemanfaatan Limbah B3**
- **ID Proyek:** 47
- **Nama:** Pekerjaan Pemanfaatan Limbah B3 PT RAS
- **Institusi Lama:** Dinas Tenaga Kerja dan Transmigrasi (DISNAKER) ❌
- **Institusi Baru:** Dinas Lingkungan Hidup Kabupaten Karawang (DLH) ✅
- **Alasan Perubahan:**
  - Pemanfaatan limbah B3 juga termasuk kewenangan DLH
  - Izin pemanfaatan limbah B3 dikeluarkan oleh **Dinas Lingkungan Hidup**

### 4. **Proyek Kartu Pengawasan PT RAS**
- **ID Proyek:** 40
- **Nama:** Pekerjaan Kartu Pengawasan PT RAS
- **Institusi Lama:** Notaris & PPAT Karawang ❌
- **Institusi Baru:** DPMPTSP Kabupaten Karawang ✅
- **Alasan Perubahan:**
  - Kartu Pengawasan terkait perizinan operasional perusahaan
  - Kewenangan ada di **DPMPTSP** (Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu)
  - Notaris hanya untuk akta/dokumen legal, bukan perizinan operasional

## ✅ Hasil Perbaikan

### Distribusi Proyek Setelah Perbaikan:

| Institusi | Jumlah Proyek | Status |
|-----------|---------------|--------|
| **Dinas Lingkungan Hidup Kabupaten Karawang** | 6 proyek | ✅ Benar |
| **DPMPTSP Kabupaten Karawang** | 1 proyek | ✅ Benar |
| **Dinas Perhubungan Kabupaten Karawang** | 1 proyek | ✅ Benar |

### Detail Proyek per Institusi:

#### Dinas Lingkungan Hidup (6 proyek):
1. ✅ Pekerjaan UKL UPL (ID: 41)
2. ✅ Pekerjaan UKL UPL (ID: 42)
3. ✅ Pekerjaan UKL UPL (ID: 43)
4. ✅ Pekerjaan UKL UPL (ID: 44)
5. ✅ Pekerjaan Penyimpanan Limbah B3 (ID: 46)
6. ✅ Pekerjaan Pemanfaatan Limbah B3 PT RAS (ID: 47)

#### DPMPTSP Kabupaten Karawang (1 proyek):
1. ✅ Pekerjaan Kartu Pengawasan PT RAS (ID: 40)

#### Dinas Perhubungan (1 proyek):
1. ✅ Pekerjaan pembuatan sistem administrasi (ID: 45)

## 📊 Statistik

- **Total Proyek Diperbaiki:** 7 proyek
- **Proyek ke DLH:** 6 proyek (dari DISNAKER)
- **Proyek ke DPMPTSP:** 1 proyek (dari Notaris)
- **Success Rate:** 100% ✅

## 🔧 Command yang Dijalankan

```bash
# Via Artisan Tinker
docker exec bizmark_app php artisan tinker --execute="
    // Update UKL UPL projects
    DB::table('projects')->whereIn('id', [41, 42, 43, 44])->update(['institution_id' => 2]);
    
    // Update Limbah B3 projects
    DB::table('projects')->whereIn('id', [46, 47])->update(['institution_id' => 2]);
    
    // Update Kartu Pengawasan
    DB::table('projects')->where('id', 40)->update(['institution_id' => 1]);
"
```

## 📚 Referensi Hukum

1. **UKL-UPL:** Peraturan Menteri LHK No. P.22/MENLHK/SETJEN/KUM.1/5/2021
2. **Limbah B3:** PP No. 22 Tahun 2021 tentang Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup
3. **Perizinan Berusaha:** PP No. 5 Tahun 2021 tentang Penyelenggaraan Perizinan Berusaha Berbasis Risiko

## ✅ Verifikasi

- [x] Semua proyek UKL-UPL sudah ke DLH
- [x] Semua proyek Limbah B3 sudah ke DLH
- [x] Proyek Kartu Pengawasan sudah ke DPMPTSP
- [x] Tidak ada proyek yang kehilangan relasi
- [x] Database integrity terjaga

## 📝 Catatan

- Perubahan ini dilakukan untuk menyesuaikan dengan kewenangan instansi yang sebenarnya
- Perbaikan ini penting untuk akurasi data dan pelaporan
- Semua relasi foreign key tetap terjaga dengan baik

---

**Status:** ✅ Completed & Verified  
**Impact:** Data relasi proyek-institusi sekarang **100% akurat** sesuai kewenangan masing-masing instansi
