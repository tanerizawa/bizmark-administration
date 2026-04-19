# Database Optimization (MySQL)

## Indeks Tambahan

Repo ini menambahkan migrasi indeks untuk mempercepat filter/sort pada panel admin:

- `permit_applications`: `status`, `submitted_at`, `client_id`, `permit_type_id`, `application_number`, dan komposit (`status`, `submitted_at`).
- `documents`: `project_id`, `category`, `created_at`.
- `projects`: `client_id`, `status_id`, `created_at`.
- `clients`: `status`, `client_type`, `created_at`.

File: `database/migrations/2026_04_19_090000_add_query_indexes_for_admin_filters.php`

## Analisis Query Lambat

1. Aktifkan slow query log (sementara) di MySQL.
2. Ambil query paling sering/lambat dari log.
3. Jalankan `EXPLAIN` dan pastikan:
   - `type` tidak jatuh ke `ALL` untuk query yang seharusnya bisa memakai indeks.
   - `rows` dan `filtered` masuk akal.
   - `Extra` tidak selalu `Using filesort` untuk kolom yang sering di-sort.

## Tuning MySQL (Baseline)

Nilai berikut bergantung RAM/traffic VPS, tapi biasanya aman sebagai titik awal:

- `innodb_buffer_pool_size`: 50–70% RAM (dedicated DB).
- `innodb_log_file_size`: 256MB–1GB (sesuaikan write load).
- `max_connections`: realistis sesuai pool aplikasi.
- Pastikan `sql_mode` tidak terlalu longgar dan timezone konsisten.

## Praktik Aplikasi

- Hindari N+1: gunakan eager loading dan `withCount` untuk list.
- Gunakan pagination untuk list admin.
- Pastikan filter yang sering dipakai punya indeks.

