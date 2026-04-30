# Integrasi Pexels API untuk Featured Image Artikel

## Deskripsi
Fitur ini memungkinkan admin untuk mencari dan memilih gambar gratis dari Pexels.com untuk digunakan sebagai featured image artikel, langsung dari halaman create/edit artikel.

## Fitur

### 1. Search & Filter
- Pencarian gambar berdasarkan kata kunci
- Filter berdasarkan orientasi (landscape, portrait, square)
- Filter berdasarkan ukuran (large 24MP, medium 12MP, small 4MP)
- Foto curated dari Pexels

### 2. Preview & Selection
- Grid preview gambar hasil pencarian
- Informasi fotografer pada hover
- Download otomatis saat memilih gambar
- Preview gambar sebelum disimpan

### 3. Attribution
- Sistem logging attribution untuk compliance dengan Pexels License
- Informasi fotografer disimpan di log

## Instalasi & Konfigurasi

### 1. API Key
API key sudah ditambahkan ke `.env`:
```env
PEXELS_API_KEY=k57TI9fmTVU3PcC93zg3LM1yt9oIAyKaagLDZ8O1OUVkxgLUuCuLO5Gv
```

### 2. Config
Konfigurasi ditambahkan di `config/services.php`:
```php
'pexels' => [
    'api_key' => env('PEXELS_API_KEY'),
],
```

## File yang Dibuat/Dimodifikasi

### Baru Dibuat:
1. **app/Services/PexelsService.php** - Service untuk handle API calls ke Pexels
2. **app/Http/Controllers/Admin/PexelsController.php** - Controller untuk search dan download

### Dimodifikasi:
1. **routes/web.php** - Menambahkan routes untuk Pexels API
2. **config/services.php** - Menambahkan konfigurasi Pexels
3. **app/Http/Controllers/ArticleController.php** - Update store() dan update() untuk handle Pexels images
4. **resources/views/articles/create.blade.php** - Menambahkan modal dan JavaScript
5. **resources/views/articles/edit.blade.php** - Menambahkan modal dan JavaScript

## API Endpoints

### Search Photos
```
GET /pexels/search
Parameters:
- query (required): Kata kunci pencarian
- page (optional): Nomor halaman
- per_page (optional): Jumlah hasil per halaman
- orientation (optional): landscape|portrait|square
- size (optional): large|medium|small
- color (optional): Warna hex atau nama warna
```

### Curated Photos
```
GET /pexels/curated
Parameters:
- page (optional): Nomor halaman
- per_page (optional): Jumlah hasil per halaman
```

### Download Photo
```
POST /pexels/download
Body (JSON):
- photo_id (required): ID foto dari Pexels
- photo_url (required): URL foto original
- photographer_name (required): Nama fotografer
- photographer_url (required): URL profil fotografer
- pexels_url (required): URL foto di Pexels
```

## Cara Penggunaan

### Di Halaman Create/Edit Artikel:

1. **Klik tombol "Cari dari Pexels"** di bagian Featured Image
2. **Modal Pexels akan terbuka** dengan foto curated
3. **Ketik kata kunci** di search box (contoh: "nature", "business", "technology")
4. **Gunakan filter** (opsional) untuk memfilter hasil berdasarkan orientasi atau ukuran
5. **Klik "Cari"** atau tekan Enter
6. **Klik pada gambar** yang diinginkan untuk memilih
7. **Foto akan otomatis diunduh** dan muncul di preview
8. **Simpan artikel** seperti biasa

### Upload Manual vs Pexels:
- Jika memilih foto dari Pexels, file input akan dikosongkan
- Jika upload file manual, path Pexels akan diabaikan
- Prioritas: Pexels > Upload Manual

## Rate Limiting

Pexels API memiliki rate limit:
- **200 requests per hour** (default)
- **20,000 requests per month** (default)

Untuk unlimited requests, hubungi Pexels melalui api@pexels.com dengan contoh implementasi.

## Compliance & Attribution

Sesuai dengan [Pexels License](https://www.pexels.com/license/):
- ✅ Foto dapat digunakan secara gratis
- ✅ Tidak perlu attribution (tapi direkomendasikan)
- ✅ Modifikasi dan penggunaan komersial diperbolehkan
- ❌ Tidak boleh menjual ulang foto tanpa modifikasi
- ❌ Tidak boleh membuat aplikasi wallpaper atau kompetitor Pexels

### Attribution yang Diimplementasikan:
1. **Logging**: Setiap download dicatat dengan informasi fotografer
2. **Link Back**: Modal menampilkan link ke Pexels.com
3. **Photographer Credit**: Nama fotografer ditampilkan saat hover

## Testing

### Manual Testing:
1. Buat artikel baru: `/articles/create`
2. Klik "Cari dari Pexels"
3. Test search dengan berbagai keyword
4. Test filter orientasi dan ukuran
5. Test pagination
6. Pilih foto dan verify download
7. Submit form dan verify artikel tersimpan dengan foto yang benar

### Error Handling:
- API key tidak valid → Error message
- Network error → Error message dengan retry option
- Download failed → Error message
- Rate limit exceeded → Error message

## Troubleshooting

### Foto tidak muncul:
- Cek API key di `.env`
- Cek koneksi internet
- Cek browser console untuk error
- Cek Laravel log di `storage/logs/laravel.log`

### Download gagal:
- Cek permission folder `storage/app/public/articles`
- Cek storage link: `php artisan storage:link`
- Cek space disk tersedia

### Rate limit exceeded:
- Tunggu 1 jam untuk reset
- Atau hubungi Pexels untuk unlimited access

## Documentation Links

- [Pexels API Documentation](https://www.pexels.com/api/documentation/)
- [Pexels License](https://www.pexels.com/license/)
- [Request Higher Limit](mailto:api@pexels.com)

## Changelog

### v1.0.0 (2026-01-13)
- ✅ Initial implementation
- ✅ Search photos from Pexels
- ✅ Filter by orientation and size
- ✅ Curated photos support
- ✅ Download and save photos
- ✅ Integration with article create/edit forms
- ✅ Attribution logging for compliance
