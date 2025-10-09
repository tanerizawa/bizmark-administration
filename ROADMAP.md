# Rancangan Roadmap & Todo Pengembangan Sistem Administrasi Konsultan Perizinan

## Phase 0: Persiapan & Perencanaan ✅ SELESAI
- [x] Finalisasi kebutuhan bisnis & scope MVP
- [x] Pilih stack teknologi (Laravel + MySQL, SSR Blade, VPS Debian, Docker)
- [x] Setup version control (Git, repo privat)
- [x] Setup Docker environment dengan MySQL, PHP-FPM, Nginx, PHPMyAdmin
- [x] Rancang struktur database awal (projects, tasks, documents, users, institutions)
- [x] Buat wireframe/mockup UI sederhana untuk dashboard

## Phase 1: MVP (Minimum Viable Product) ✅ SELESAI
- [x] Inisialisasi project Laravel
- [x] Setup autentikasi user (login/logout, role sederhana: admin/staff)
- [x] **Modul Database & Model**
  - [x] Migrasi database lengkap untuk semua tabel
  - [x] Model Eloquent dengan relationship (Project, Task, Document, User, Institution, ProjectStatus, ProjectLog)
  - [x] Seeder data awal (status proyek, institusi, user)
- [x] **Dashboard sederhana**
  - [x] Statistik dasar (total proyek, tugas, dokumen)
  - [x] Daftar proyek terbaru
  - [x] Distribusi status proyek
  - [x] Tugas prioritas tinggi dan terlambat
  - [x] UI responsif dengan Tailwind CSS

### Status Saat Ini
✅ **Database Schema Lengkap:**
- Tabel users (dengan role dan info lengkap)
- Tabel institutions (DLH, BPN, OSS, Notaris, dll)
- Tabel project_statuses (workflow status)
- Tabel projects (dengan client info dan tracking lengkap)
- Tabel tasks (dengan priority dan assignment)
- Tabel documents (dengan versioning dan access control)
- Tabel project_logs (audit trail)

✅ **Docker Environment:**
- Container app (PHP 8.2-FPM dengan Laravel)
- Container web (Nginx reverse proxy)
- Container db (MySQL 8.0)
- Container phpmyadmin (untuk administrasi database)
- Semua berjalan di network internal
- Akses via http://localhost:8081

✅ **Data Awal:**
- 11 status proyek (Penawaran → SK Terbit)
- 6 institusi default (DLH, BPN, OSS, Notaris, Dishub, Dinkes)
- 5 user default (admin, manager, 3 staff)

## Phase 2: Peningkatan Fitur & UX 🔄 SEDANG BERLANGSUNG
- [ ] **Modul Manajemen Proyek/Izin**
  - [ ] CRUD proyek lengkap
  - [ ] Update status workflow
  - [ ] Log aktivitas otomatis
- [ ] **Modul Manajemen Dokumen**
  - [ ] Upload/download dokumen per proyek
  - [ ] Metadata dan kategorisasi
  - [ ] Kontrol akses dasar
- [ ] **Modul Manajemen Tugas**
  - [ ] CRUD tugas per proyek
  - [ ] Assignment ke user
  - [ ] Status dan due date tracking
- [ ] Fitur pencarian/filter proyek, dokumen, tugas
- [ ] Notifikasi email internal (tugas due, status proyek stagnan)
- [ ] Dashboard lebih informatif
- [ ] Perbaikan UX/UI berdasarkan feedback user
- [ ] Logging aktivitas penting
- [ ] Backup otomatis & restore

## Phase 3: Otomatisasi & Integrasi
- [ ] Template otomatis tugas per jenis izin
- [ ] Integrasi kalender (Gantt chart/timeline tugas)
- [ ] Laporan formal (export PDF/Excel)
- [ ] Integrasi API eksternal (opsional, jika memungkinkan)
- [ ] Versioning dokumen sederhana
- [ ] Fitur arsip/history proyek

## Phase 4: Best Practice & Maintenance
- [ ] CI/CD pipeline sederhana (deploy ke VPS via git, auto-backup)
- [ ] Penulisan dokumentasi user & teknis (README, struktur DB, SOP backup)
- [ ] Penulisan automated test (unit, feature test Laravel)
- [ ] Audit keamanan berkala (update OS, framework, dependency)
- [ ] Evaluasi kebutuhan fitur baru (misal: client portal, mobile app, advanced analytics)

---

## Setup Development

### Akses Aplikasi
- **Web App**: http://localhost:8081
- **PHPMyAdmin**: http://localhost:8080
- **Database**: mysql://localhost:3306 (bizmark_db)

### Kredensial Default
- **Admin**: admin@bizmark.id / admin123
- **Manager**: manager@bizmark.id / manager123  
- **Staff**: siti@bizmark.id / staff123

### Perintah Docker
```bash
# Menjalankan semua containers
docker-compose up -d

# Menjalankan migration
docker-compose exec app php artisan migrate

# Menjalankan seeder
docker-compose exec app php artisan db:seed

# Akses container app
docker-compose exec app bash

# Melihat logs
docker-compose logs -f app
```

### Struktur Database
- **Modular**: Setiap modul memiliki tabel terpisah dengan foreign key relationship
- **Audit Trail**: Project logs untuk tracking semua perubahan
- **Flexible Status**: Project status disimpan di tabel terpisah, mudah dikustomisasi
- **Document Management**: Support versioning dan access control
- **User Roles**: Admin, Staff, Viewer dengan permission berbeda

---

### Catatan Pengembangan Modular
- Struktur kode per modul (controller, model, view, migration, policy)
- Gunakan package Laravel untuk modularisasi jika perlu (nwidart/laravel-modules)
- Simpan konfigurasi workflow/status di database agar mudah diubah
- Terapkan backup & restore terjadwal (database & dokumen)
- Dokumentasikan setiap perubahan besar (changelog)

### Best Practice Tambahan
- Gunakan HTTPS untuk semua akses
- Simpan password terenkripsi (bcrypt/argon2)
- Validasi & sanitasi semua input
- Simpan dokumen di folder terproteksi, akses via aplikasi
- Logging & monitoring aktivitas penting
- Lakukan review kode sebelum merge ke branch utama

---

Roadmap ini dapat dikembangkan sesuai kebutuhan bisnis dan feedback user. Setiap phase sebaiknya dilakukan dalam siklus agile (2-4 minggu) agar sistem cepat deliver value dan mudah diadaptasi.