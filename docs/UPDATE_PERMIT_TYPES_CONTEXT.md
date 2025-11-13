# Update Permit Types Context - Documentation

## 📋 Overview
Perbaikan data master jenis izin (permit types) agar institusi penerbit, estimasi waktu, dan biaya sesuai dengan konteks perizinan di Indonesia berdasarkan regulasi terbaru.

## 🎯 Masalah yang Diperbaiki
1. **Institusi tidak relevan**: Semua izin lingkungan mengarah ke "Dinas Tenaga Kerja" padahal seharusnya ke "Dinas Lingkungan Hidup" atau "KLHK"
2. **Waktu & Biaya tidak akurat**: Estimasi tidak sesuai dengan praktek dan regulasi yang berlaku
3. **Kurang informasi**: Tidak ada deskripsi detail tentang setiap jenis izin

## ✅ Solusi yang Diterapkan

### 1. Mapping Institusi yang Benar

#### **Environmental Permits**
| Jenis Izin | Institusi Sebelum | Institusi Sesudah | Alasan |
|------------|------------------|-------------------|---------|
| AMDAL | Disnaker | KLHK | AMDAL untuk proyek strategis nasional dikeluarkan oleh Kementerian LHK |
| UKL-UPL | Disnaker | DLH Kabupaten | UKL-UPL untuk kegiatan lokal dikeluarkan oleh Dinas Lingkungan Hidup |
| SPPL | Disnaker | DLH Kabupaten | SPPL untuk UMKM dikeluarkan oleh DLH setempat |
| Izin Lingkungan | Disnaker | DLH Kabupaten | Izin turunan dari AMDAL/UKL-UPL |
| Pertek/Rintek LB3 | Disnaker | DLH Kabupaten | Izin pengelolaan limbah B3 |

#### **Building Permits**
| Jenis Izin | Institusi | Alasan |
|------------|-----------|---------|
| IMB | DPMPTSP | Sistem lama sebelum UU Cipta Kerja |
| PBG | DPMPTSP | Pengganti IMB sesuai UU Cipta Kerja, terintegrasi OSS |
| SLF | DPMPTSP | Sertifikat kelayakan bangunan |
| Siteplan | Dinas PUPR | Persetujuan rencana tapak |

#### **Business Permits**
| Jenis Izin | Institusi | Alasan |
|------------|-----------|---------|
| NIB | OSS | Nomor Induk Berusaha melalui sistem OSS online |
| TDP, SIUP, TDI | OSS | Terintegrasi dalam NIB sejak sistem OSS |
| Izin Operasional | OSS | Izin komersial pasca konstruksi |

#### **Land & Spatial**
| Jenis Izin | Institusi | Alasan |
|------------|-----------|---------|
| Sertifikat Tanah | BPN | Kewenangan Kantor Pertanahan |
| Pertek BPN | BPN | Persetujuan teknis pemetaan |
| PKKPR | DPMPTSP | Kesesuaian tata ruang |

#### **Transportation**
| Jenis Izin | Institusi | Alasan |
|------------|-----------|---------|
| Andalalin | Dinas Perhubungan | Analisis dampak lalu lintas |
| Izin Trayek | Dinas Perhubungan | Izin angkutan |

### 2. Estimasi Waktu yang Akurat

Berdasarkan PP 22/2021 dan regulasi terkait:

| Jenis Izin | Waktu Lama | Waktu Baru | Sumber Regulasi |
|------------|------------|------------|-----------------|
| AMDAL | 14 hari | 75 hari | PP 22/2021 - Proses kompleks dengan kajian mendalam |
| UKL-UPL | 14 hari | 14 hari | PP 22/2021 - Sudah sesuai |
| SPPL | 7 hari | 7 hari | Praktek lapangan |
| NIB | 1 hari | 1 hari | OSS online realtime |
| PBG | 30 hari | 30 hari | Sesuai regulasi OSS RBA |
| Andalalin | - | 30 hari | Praktek lapangan |

### 3. Estimasi Biaya yang Realistis

| Jenis Izin | Range Biaya | Catatan |
|------------|-------------|---------|
| AMDAL | Rp 50jt - 200jt | Tergantung kompleksitas proyek |
| UKL-UPL | Rp 5jt - 15jt | Untuk kegiatan menengah |
| SPPL | Rp 500rb - 2jt | Untuk UMKM, biaya minimal |
| NIB | Gratis - 500rb | Gratis di OSS, biaya konsultan opsional |
| PBG | Rp 10jt - 50jt | Tergantung luas dan tinggi bangunan |
| Sertifikat Tanah | Rp 5jt - 20jt | Biaya notaris, BPHTB, dll |
| Andalalin | Rp 15jt - 50jt | Untuk proyek besar |

### 4. Deskripsi Lengkap

Setiap jenis izin sekarang memiliki deskripsi yang menjelaskan:
- **Tujuan izin**: Untuk apa izin ini diperlukan
- **Institusi penerbit**: Siapa yang mengeluarkan dan mengapa
- **Dasar hukum**: Regulasi yang menjadi dasar
- **Scope**: Kapan izin ini wajib/direkomendasikan

Contoh:
```
AMDAL: "Analisis Mengenai Dampak Lingkungan untuk kegiatan yang berdampak 
penting terhadap lingkungan. Dikeluarkan oleh Kementerian LHK untuk kegiatan 
strategis nasional atau lintas provinsi."
```

## 🚀 Cara Menggunakan

### 1. Jalankan Seeder
```bash
docker exec bizmark_app php artisan db:seed --class=UpdatePermitTypesContextSeeder
```

### 2. Verifikasi Hasil
```bash
docker exec bizmark_app php artisan tinker --execute="
\$permitTypes = App\Models\PermitType::with('institution')
    ->whereIn('code', ['AMDAL', 'UKL_UPL', 'IMB', 'NIB'])
    ->get();

foreach (\$permitTypes as \$pt) {
    echo \"{\$pt->name}: {\$pt->institution->name}\n\";
}
"
```

## 📊 Hasil Perbaikan

### Before
```
AMDAL: Dinas Tenaga Kerja (❌ Salah)
UKL-UPL: Dinas Tenaga Kerja (❌ Salah)
Andalalin: Notaris & PPAT (❌ Salah)
```

### After
```
AMDAL: Kementerian LHK (✅ Benar)
UKL-UPL: Dinas Lingkungan Hidup (✅ Benar)
Andalalin: Dinas Perhubungan (✅ Benar)
```

## 🎨 Peningkatan UI

### 1. **Icon Category** 
Setiap kategori memiliki icon dan warna yang konsisten:
- 🌿 Environmental: Green (`fa-leaf`)
- 🏢 Building: Blue (`fa-building`)
- 💼 Business: Orange (`fa-briefcase`)
- 🗺️ Land: Purple (`fa-map`)
- 🚚 Transportation: Grey (`fa-truck`)

### 2. **Expandable Description**
Klik baris untuk melihat deskripsi lengkap dan dokumen yang diperlukan

### 3. **Better Visual Hierarchy**
- Institution name + type
- Icon untuk waktu (clock) dan biaya (money)
- Status badges dengan colors yang konsisten

## 📚 Referensi Regulasi

1. **PP 22/2021** - Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup
2. **UU 11/2020** - UU Cipta Kerja (PBG menggantikan IMB)
3. **PP 5/2021** - Penyelenggaraan Perizinan Berusaha Berbasis Risiko (OSS RBA)
4. **Permen LHK P.1/2021** - Program Penilaian Peringkat Kinerja Perusahaan (PROPER)
5. **Permen PUPR 16/2021** - Pembangunan Bangunan Gedung

## 🔧 Maintenance

### Menambah Jenis Izin Baru
1. Tambahkan di seeder `UpdatePermitTypesContextSeeder.php`
2. Tentukan institusi yang tepat
3. Riset estimasi waktu dari regulasi
4. Riset estimasi biaya dari praktek lapangan
5. Tulis deskripsi yang jelas dan informatif

### Update Regulasi
Jika ada perubahan regulasi:
1. Update seeder dengan data terbaru
2. Run seeder ulang
3. Update dokumentasi ini

## ✅ Checklist Quality Assurance

- [x] Semua environmental permits → DLH/KLHK
- [x] Semua building permits → DPMPTSP/PUPR
- [x] Semua business permits → OSS
- [x] Semua land permits → BPN/DPMPTSP
- [x] Semua transportation permits → Dishub
- [x] Estimasi waktu sesuai PP 22/2021
- [x] Estimasi biaya berdasarkan praktek lapangan 2024-2025
- [x] Deskripsi lengkap dan informatif
- [x] UI enhanced dengan icons dan expandable rows

## 🎯 Impact

**Sebelum:**
- ❌ Data tidak akurat
- ❌ User bingung dengan institusi yang salah
- ❌ Estimasi tidak membantu planning
- ❌ Kurang informasi

**Sesudah:**
- ✅ Data akurat sesuai regulasi
- ✅ Institusi penerbit jelas dan benar
- ✅ Estimasi membantu budgeting & scheduling
- ✅ Deskripsi lengkap sebagai panduan

## 📞 Contact

Jika ada pertanyaan atau update regulasi terbaru, hubungi tim development.

---
Last Updated: October 3, 2025
Version: 1.0.0
