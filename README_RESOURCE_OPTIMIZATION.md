# 🚀 Bizmark.id - Resource-Efficient Development Guide

## 📊 Server Infrastructure

Sistem ini di-design untuk **hemat resource** karena server sudah menjalankan sistem prioritas (APP-YK/nusantaragroup.co).

### Resource Sharing Strategy

✅ **PostgreSQL Database**: Share dengan nusantara-postgres container
- Database: `bizmark_db` 
- Host: `127.0.0.1:5432`
- User: `admin` / Password: `admin123`
- Container: `nusantara-postgres` (already running)
- **Benefit**: Zero additional database daemon, production-grade

✅ **On-Demand Server**: Laravel server hanya jalan saat development
- Script helper: `./dev.sh`
- Auto-stop setelah selesai development
- **Benefit**: Free ~100-150MB RAM saat tidak digunakan

## 🛠️ Development Workflow

### Start Development
```bash
cd /root/Bizmark.id
./dev.sh start
```

Server akan running di **http://127.0.0.1:8000**

### Stop Development (Hemat Resource)
```bash
./dev.sh stop
```
**PENTING**: Selalu stop server setelah selesai development!

### Check Status
```bash
./dev.sh status
```
Menampilkan:
- Server status (running/stopped)
- PID process
- Memory usage

### View Logs
```bash
./dev.sh logs
```

### Restart Server
```bash
./dev.sh restart
```

## 🔑 Default Login

- Email: `admin@example.com`
- Password: `password`

## ⚙️ Resource Optimization Tips

### 1. **Stop Server Saat Tidak Digunakan**
```bash
# Setelah selesai development
./dev.sh stop
```
Menghemat ~100-150MB RAM

### 2. **Share Database dengan APP-YK**
- Bizmark.id menggunakan PostgreSQL yang sama dengan APP-YK
- Hanya beda database name: `bizmark_db` vs `nusantara_construction`
- Zero additional resource untuk database daemon

### 3. **Build Assets Sekali Saja**
```bash
npm run build
```
Setelah build, tidak perlu run `npm run dev` (hemat Node.js process)

### 4. **Monitor Memory Usage**
```bash
./dev.sh status  # Lihat memory usage server
docker stats nusantara-postgres  # Monitor database container
```

## 📁 Database Information

### Schema
- **Full migrations**: Semua 40+ migrations berhasil dijalankan
- **Full seeders**: 12 seeders (termasuk ProjectSeeder, TaskSeeder, DocumentSeeder)
- **Sample data**: Ready untuk development & testing

### PostgreSQL Compatibility
Semua migrations telah di-fix untuk PostgreSQL:
- ✅ No MODIFY syntax (converted to DROP+ADD)
- ✅ FULLTEXT indexes supported
- ✅ ENUM types properly handled
- ✅ All constraints working

## 🎯 Best Practices

### Development Flow
1. **Start server**: `./dev.sh start`
2. **Development work**: Edit code, test features
3. **Check logs**: `./dev.sh logs` jika ada error
4. **Stop server**: `./dev.sh stop` ← **JANGAN LUPA!**

### Priority System
⭐ **APP-YK** (nusantaragroup.co) = PRIORITAS UTAMA
- Always running (production)
- Resource priority #1

🔧 **Bizmark.id** = Development/Testing only
- On-demand execution
- Stop when not needed

## 📈 Resource Comparison

| Component | Before | After | Savings |
|-----------|--------|-------|---------|
| Database | SQLite (separate) | PostgreSQL (shared) | Zero new daemon |
| PHP Server | Always on | On-demand | ~150MB when stopped |
| Migrations | 5 failed | All pass | Full functionality |
| Seeders | 3 skipped | All complete | Complete test data |

## 🔧 Troubleshooting

### Server tidak start
```bash
./dev.sh logs  # Check error logs
php artisan config:clear  # Clear cache
./dev.sh restart
```

### Database connection error
```bash
docker ps | grep nusantara-postgres  # Pastikan container running
psql -h 127.0.0.1 -U admin -d bizmark_db  # Test connection
```

### Port 8000 sudah digunakan
```bash
./dev.sh stop  # Stop existing server
# Atau ubah port di dev.sh: php artisan serve --port=8001
```

## 📝 Notes

- **Sistem ini BUKAN production**: Untuk development/testing saja
- **APP-YK adalah prioritas**: Jangan sampai mengganggu resource-nya
- **Always stop server**: Hemat resource untuk sistem utama
- **Database shared**: Jangan drop nusantara_construction database!

## 🤝 Resource Efficiency Summary

✅ **Zero new database daemon** (share PostgreSQL)  
✅ **On-demand server** (stop when not needed)  
✅ **One-time build** (no continuous npm process)  
✅ **Full functionality** (all migrations & seeders working)  
✅ **Production-grade database** (PostgreSQL vs SQLite)

---

**Total Additional Resource When Running:**
- RAM: ~100-150MB (Laravel server only)
- CPU: Minimal (shared database)
- Disk: ~500MB (node_modules + vendor + uploads)

**Total Resource When Stopped:**
- RAM: 0MB 🎉
- CPU: 0% 🎉
- Only disk space used (static files)
