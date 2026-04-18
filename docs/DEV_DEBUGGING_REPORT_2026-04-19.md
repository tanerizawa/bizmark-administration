# Laporan Debugging & Refactoring (Development)

Tanggal: 2026-04-19  
Scope: stabilisasi environment testing/dev, perbaikan migrasi agar test suite dapat berjalan, hardening minimal untuk observability (request correlation), dan perapihan test agar selaras dengan skema terbaru.

## Ringkasan Hasil

- Test suite: lulus (74 tests, 378 assertions)
- Perbaikan utama: kompatibilitas migrasi lintas driver (SQLite testing vs PostgreSQL production), isolasi path compiled view untuk testing, dan pemutakhiran test agar sesuai skema data aktual.
- Hardening: penambahan request correlation id (`X-Request-Id`) + log context global.

## Daftar Error yang Ditemukan & Solusi

### 1) Composer/Artisan gagal karena `bootstrap/cache` tidak writable

Gejala:
- `php artisan package:discover` gagal dengan pesan `bootstrap/cache directory must be present and writable`.

Akar masalah:
- Environment eksekusi membatasi pembuatan/rename file di `bootstrap/cache` sehingga proses yang menulis manifest/caches gagal.

Solusi:
- Jalankan install dependency dengan men-skip script: `composer install --no-scripts`.
- Jalankan test suite langsung via PHPUnit (menghindari `composer test` yang memanggil `php artisan config:clear`): `./vendor/bin/phpunit -c phpunit.xml`.

### 2) Migrasi gagal pada SQLite (testing) akibat operasi Postgres-specific / constraint/index

Gejala utama (berulang di beberapa migration):
- Drop column pada SQLite gagal karena index/foreign key masih mereferensikan kolom.
- Perintah Postgres-specific seperti `DROP CONSTRAINT`, `ALTER COLUMN TYPE`, GIN index, `to_tsvector(...)`, `jsonb`, fulltext index, tidak didukung SQLite.

Solusi:
- Menambahkan branching berdasarkan driver DB dan/atau no-op untuk migrasi yang memang spesifik PostgreSQL.
- Untuk kasus yang paling kompleks (restructure `projects`), pada SQLite dilakukan pendekatan drop+recreate table agar migrasi konsisten dan tidak terblokir constraint/index.

File yang disesuaikan (contoh):
- `database/migrations/2025_10_01_162642_update_projects_table_structure.php`
- `database/migrations/2025_10_03_200841_update_category_enum_in_project_expenses_table.php`
- `database/migrations/2025_10_10_201930_create_articles_table.php`
- `database/migrations/2025_11_14_061948_add_user_extended_fields_to_users_table.php`
- `database/migrations/2025_11_14_112756_add_converted_to_project_status_to_permit_applications.php`
- `database/migrations/2025_11_14_133029_create_kbli_table.php`
- `database/migrations/2025_11_14_135102_remove_category_from_kbli_table.php`
- `database/migrations/2025_11_16_232528_make_application_notes_author_id_nullable.php`
- `database/migrations/2025_11_23_164516_add_document_editing_fields_to_test_templates_table.php`
- `database/migrations/2025_11_23_174732_drop_test_type_constraint_from_test_templates.php`
- `database/migrations/2025_11_27_150000_create_kblis_table.php`
- `database/migrations/2025_11_27_151000_add_pricing_fields_to_kbli_table.php`

### 3) HTTP 500 pada rendering view: compiled blade tidak bisa ditulis

Gejala:
- Request feature test untuk halaman publik menghasilkan 500 dengan error `file_put_contents(storage/framework/views/...): Permission denied`.

Solusi:
- Override compiled view path pada testing agar mengarah ke direktori writable:
  - Update `phpunit.xml`: `VIEW_COMPILED_PATH=/tmp/bizmark-framework-views`
  - Direktori dibuat saat eksekusi test.

File terkait:
- `phpunit.xml`

### 4) Ketidaksesuaian test dengan skema terbaru

Gejala:
- Test membuat record dengan kolom yang sudah tidak ada (contoh `projects.code`, `permit_type`, `current_status_id`, dll).
- Test mengandalkan data lookup (ExpenseCategory/PaymentMethod) yang berasal dari DB namun belum di-seed, sehingga validasi `Rule::in(...)` selalu gagal.

Solusi:
- Update test agar:
  - Mengisi kolom sesuai skema baru (`status_id`, `client_contact`, dll).
  - Membuat data referensi minimal (ExpenseCategory, PaymentMethod) di dalam test.
  - Menambahkan middleware yang diperlukan untuk implicit route-model binding pada route testing.

File terkait:
- `tests/Feature/ProjectExpenseCategoryTest.php`
- `tests/Feature/ProjectPermitStatusTest.php`

## Refactoring & Hardening yang Dilakukan

### 1) Request correlation ID + log context

Tujuan:
- Memudahkan tracing error lintas middleware/controller/job dengan korelasi `request_id`.

Implementasi:
- Middleware baru `App\Http\Middleware\RequestId` men-set:
  - `X-Request-Id` pada response
  - `Log::withContext([...])` berisi `request_id`, `method`, `path`
- Middleware ditambahkan ke web group.

File:
- `app/Http/Middleware/RequestId.php`
- `bootstrap/app.php`

## Testing & Validasi

### Test suite (PHPUnit)

Konfigurasi:
- `phpunit.xml` memakai SQLite in-memory untuk cepat dan aman.
- `VIEW_COMPILED_PATH` diarahkan ke `/tmp/...` untuk menghindari permission issue pada compiled blade.

Hasil:
- Lulus: 74 tests, 378 assertions.

## Catatan Operasional untuk Development

- Untuk environment yang membatasi penulisan `bootstrap/cache`, hindari:
  - `composer test` (karena menjalankan `php artisan config:clear`)
  - `php artisan package:discover` jika menulis manifest via rename temp file
- Jalankan:
  - `composer install --no-scripts`
  - `./vendor/bin/phpunit -c phpunit.xml`

