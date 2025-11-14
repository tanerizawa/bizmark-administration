# Panduan Import Data KBLI

## 📋 Overview
Sistem KBLI (Klasifikasi Baku Lapangan Usaha Indonesia) telah dimigrasi ke PostgreSQL database dengan fitur import/export CSV untuk admin.

## 🎯 Akses Halaman KBLI

1. Login sebagai admin
2. Buka menu sidebar **Master Data** → **Data KBLI**
3. URL: `https://bizmark.id/admin/settings/kbli`

## 📊 Fitur yang Tersedia

### 1. **Statistik Dashboard**
- Total KBLI: Jumlah total klasifikasi dalam database
- Risiko Rendah: Bisnis dengan risiko rendah
- Risiko Menengah: Kombinasi menengah rendah & menengah tinggi
- Risiko Tinggi: Bisnis dengan risiko tinggi
- Distribusi per Sektor: Breakdown berdasarkan sektor A-U

### 2. **Import Data dari CSV**

#### Format CSV yang Diperlukan:
```csv
code,description,category,sector,notes
62010,Aktivitas Pemrograman Komputer,Rendah,J,Pengembangan software dan aplikasi
62020,Aktivitas Konsultasi Komputer,Rendah,J,Konsultan IT dan system integrator
55101,Hotel Bintang 5,Menengah Tinggi,I,Perhotelan berbintang lima
11010,Industri Minuman Keras,Tinggi,C,Produksi minuman beralkohol
```

#### Kolom CSV (harus urut):
1. **code** (wajib) - Kode KBLI 5 digit (contoh: 62010)
2. **description** (wajib) - Deskripsi aktivitas bisnis
3. **category** (opsional) - Tingkat risiko: `Rendah`, `Menengah Rendah`, `Menengah Tinggi`, `Tinggi`
4. **sector** (wajib) - Sektor bisnis satu huruf: A-U
5. **notes** (opsional) - Catatan tambahan atau penjelasan

#### Kategori Risiko:
- **Rendah** - Risiko minimal, perizinan sederhana
- **Menengah Rendah** - Risiko rendah-sedang
- **Menengah Tinggi** - Risiko sedang-tinggi
- **Tinggi** - Risiko tinggi, perizinan ketat

#### Sektor Bisnis (A-U):
- **A** - Pertanian, Kehutanan, Perikanan
- **B** - Pertambangan dan Penggalian
- **C** - Industri Pengolahan
- **D** - Pengadaan Listrik, Gas, Uap/Air Panas
- **E** - Pengadaan Air, Pengelolaan Sampah, Limbah
- **F** - Konstruksi
- **G** - Perdagangan Besar dan Eceran
- **H** - Transportasi dan Pergudangan
- **I** - Penyediaan Akomodasi dan Makan Minum
- **J** - Informasi dan Komunikasi
- **K** - Jasa Keuangan dan Asuransi
- **L** - Real Estate
- **M** - Jasa Profesional, Ilmiah, dan Teknis
- **N** - Jasa Persewaan dan Sewa Guna Usaha
- **O** - Administrasi Pemerintahan, Pertahanan
- **P** - Jasa Pendidikan
- **Q** - Jasa Kesehatan dan Kegiatan Sosial
- **R** - Kesenian, Hiburan, dan Rekreasi
- **S** - Kegiatan Jasa Lainnya
- **T** - Jasa Perorangan yang Melayani Rumah Tangga
- **U** - Kegiatan Badan Internasional

#### Langkah Import:

1. **Download Template CSV**
   - Klik tombol "Download Template CSV"
   - Template berisi format dan contoh data

2. **Siapkan File CSV**
   - Buka template dengan Excel/LibreOffice/Text Editor
   - Isi data KBLI sesuai format
   - Simpan sebagai CSV (UTF-8)

3. **Upload File**
   - Pilih file CSV (max 10MB)
   - Centang "Hapus semua data KBLI yang ada" jika ingin replace total
   - Klik "Import Data"

4. **Hasil Import**
   - Success: Menampilkan jumlah data yang berhasil diimport
   - Skipped: Jumlah baris yang dilewati (data invalid/duplikat)
   - Error: Pesan kesalahan jika ada masalah

### 3. **Download Template CSV**
- Berisi format yang benar dengan 4 contoh data
- Gunakan sebagai referensi untuk membuat data baru

### 4. **Export Data ke CSV**
- Download semua data KBLI dalam format CSV
- File: `kbli_export_YYYY-MM-DD_HHMMSS.csv`
- Berguna untuk backup atau edit massal

### 5. **Hapus Semua Data**
- Menghapus seluruh data KBLI dari database
- **PERHATIAN:** Tindakan ini tidak dapat dibatalkan!
- Konfirmasi diperlukan sebelum eksekusi

## 🔧 Sumber Data KBLI Lengkap

### Opsi 1: Download dari BPS (Badan Pusat Statistik)
- Website: https://www.bps.go.id/
- Cari "KBLI 2020"
- Download file resmi dalam format Excel/PDF
- Convert ke CSV dengan format di atas

### Opsi 2: Download dari OSS (Online Single Submission)
- Website: https://oss.go.id/kbli
- Browse kategori KBLI
- Copy-paste data ke Excel
- Export ke CSV

### Opsi 3: Request dari Developer
- Hubungi developer untuk mendapatkan file CSV lengkap (1,790+ codes)
- File sudah terformat sesuai sistem

## 📝 Tips dan Best Practices

### ✅ DO:
- Backup data sebelum import massal (gunakan Export)
- Test dengan sample kecil terlebih dahulu
- Validasi format CSV sebelum upload
- Gunakan UTF-8 encoding untuk karakter Indonesia
- Update data secara berkala dari sumber resmi

### ❌ DON'T:
- Jangan centang "Hapus data yang ada" kecuali yakin ingin replace total
- Jangan upload file lebih dari 10MB
- Jangan mengubah urutan kolom CSV
- Jangan gunakan karakter spesial dalam code
- Jangan import data duplikat (akan di-skip)

## 🔍 Troubleshooting

### Import Gagal
- **Cek format CSV**: Pastikan header dan urutan kolom benar
- **Cek encoding**: Gunakan UTF-8
- **Cek ukuran file**: Max 10MB
- **Cek data wajib**: Code, description, sector harus diisi

### Data Tidak Muncul di Autocomplete
- **Clear cache**: Cache akan refresh otomatis dalam 1-24 jam
- **Cek API**: Test endpoint `/api/kbli/search?q=keyword`
- **Cek database**: Pastikan data ter-import dengan benar

### Performance Lambat
- **Indexing**: Database sudah menggunakan index
- **Caching**: Service menggunakan cache 1-24 jam
- **Limit hasil**: Autocomplete dibatasi 20 hasil per search

## 🚀 Integrasi dengan Sistem

### Autocomplete di Form Aplikasi
- Client form: `/client/applications/create`
- Real-time search dengan debounce 300ms
- Color-coded berdasarkan kategori risiko
- Max 20 hasil per pencarian

### API Endpoints
```
GET /api/kbli/search?q={keyword}  - Search KBLI
GET /api/kbli/{code}              - Get by code
GET /api/kbli                     - Get all (with filters)
```

### Cache Management
- Search results: 1 jam
- Get by code: 24 jam
- Get all: 24 jam
- Cache akan di-clear otomatis saat import baru

## 📞 Support

Jika mengalami kesulitan, hubungi developer atau cek log aplikasi:
```bash
tail -f storage/logs/laravel.log
```

---

**Last Updated:** November 14, 2025
**Version:** 1.0.0
