# Panduan Akses Aplikasi dari Komputer Lokal

## 🚀 Cara Mengakses Aplikasi

### Melalui VS Code Port Forwarding (Recommended)

1. **Buka VS Code Command Palette**
   - Windows/Linux: `Ctrl + Shift + P`
   - Mac: `Cmd + Shift + P`

2. **Forward Ports**
   - Ketik: `Ports: Forward a Port`
   - Tambahkan port `8081` untuk aplikasi web
   - Tambahkan port `8080` untuk PHPMyAdmin

3. **Akses Aplikasi**
   - **Aplikasi Web**: `http://localhost:8081`
   - **PHPMyAdmin**: `http://localhost:8080`

### Melalui Panel Ports di VS Code

1. Buka panel **PORTS** di bagian bawah VS Code
2. Klik tombol **Forward a Port** (ikon +)
3. Masukkan port `8081` dan `8080`
4. Aplikasi akan otomatis tersedia di browser lokal

## 🔗 URL Akses

| Service | URL | Description |
|---------|-----|-------------|
| **Dashboard Aplikasi** | http://localhost:8081 | Aplikasi utama sistem perizinan |
| **PHPMyAdmin** | http://localhost:8080 | Interface database management |
| **MySQL Direct** | localhost:3306 | Direct database connection |

## 👤 Login Credentials

### Default Users
```
Admin    : admin@bizmark.id    / admin123
Manager  : manager@bizmark.id  / manager123
Staff 1  : siti@bizmark.id     / staff123
Staff 2  : ahmad@bizmark.id    / staff123
Staff 3  : maya@bizmark.id     / staff123
```

### Database Access
```
Host     : localhost (atau 127.0.0.1)
Port     : 3306
Database : bizmark_db
Username : bizmark_user
Password : bizmark_password
```

## 🛠️ Troubleshooting

### Port Tidak Bisa Diakses
1. Pastikan containers berjalan: `docker-compose ps`
2. Restart containers: `docker-compose restart`
3. Check port binding: `docker-compose logs web`

### VS Code Port Forwarding Tidak Muncul
1. Pastikan VS Code extension "Remote Development" terinstall
2. Reload VS Code window: `Ctrl+Shift+P` → `Developer: Reload Window`
3. Manual forward via Command Palette

### Aplikasi Error 500
1. Check logs: `docker-compose logs app`
2. Check permissions: `docker-compose exec -u root app chown -R www:www /var/www/storage`
3. Clear cache: `docker-compose exec app php artisan cache:clear`

## 📱 Fitur yang Tersedia

### Dashboard Utama
- ✅ Statistik proyek real-time
- ✅ Daftar proyek terbaru
- ✅ Status proyek overview
- ✅ Tugas prioritas tinggi
- ✅ Tugas yang terlambat
- ✅ Quick actions menu

### Database Management
- ✅ Akses lengkap via PHPMyAdmin
- ✅ View semua tabel dan data
- ✅ Export/Import data
- ✅ Query builder interface

## 🔄 Development Commands

```bash
# Lihat status containers
docker-compose ps

# Restart aplikasi
docker-compose restart app

# Akses shell container
docker-compose exec app bash

# Jalankan migration
docker-compose exec app php artisan migrate

# Jalankan seeder
docker-compose exec app php artisan db:seed

# Clear cache
docker-compose exec app php artisan cache:clear
```

## 📝 Notes

- Port forwarding otomatis aktif jika menggunakan VS Code Remote
- Aplikasi dapat diakses dari browser manapun di komputer lokal
- Database dapat diakses menggunakan tools seperti MySQL Workbench atau DBeaver
- Semua perubahan code akan auto-reload (development mode)

---

**Happy Coding! 🚀**

Jika ada masalah akses, pastikan:
1. ✅ Containers berjalan (`docker-compose ps`)
2. ✅ Port forwarding aktif di VS Code
3. ✅ Firewall tidak memblokir port 8080/8081