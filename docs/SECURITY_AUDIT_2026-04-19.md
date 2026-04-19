# Security Audit 2026-04-19

## Temuan Utama

### SQL Injection

- Sorting dinamis dan `orderByRaw()` tanpa whitelist/binding berisiko injeksi.
- Perbaikan dilakukan dengan whitelist kolom + normalisasi arah sort, serta binding untuk `orderByRaw()`.

### XSS

- Render HTML mentah (`{!! !!}`) untuk output AI/RAG dan konten artikel/karir berisiko stored/reflective XSS.
- Perbaikan dilakukan dengan sanitasi HTML berbasis allowlist sebelum render.

### File Upload

- Penggunaan `getClientOriginalName()` langsung pada `storeAs()` berisiko path traversal/overwrite.
- Penyimpanan dokumen sensitif di disk `public` berisiko bypass otorisasi.
- Perbaikan: nama file UUID + disk `private`.

### Session

- Cookie sesi perlu `secure=true` pada lingkungan produksi.

## Dependency Scanner (Composer Audit)

`composer audit` melaporkan advisory pada beberapa paket (transitif dan dev). Untuk menutup celah di produksi, jalankan pembaruan dependency di VPS dan pastikan audit bersih.

Rekomendasi di VPS:

```bash
composer update --with-all-dependencies
composer audit
```

Catatan:

- `phpunit/phpunit` adalah dependensi dev, namun tetap perlu diperbarui untuk keamanan lingkungan CI.

