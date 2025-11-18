# 📱 Panduan Update Aplikasi PWA - Bizmark.ID

## Untuk Klien

### ✨ Fitur Auto-Update Baru!

Aplikasi Bizmark.ID Portal Klien sekarang dilengkapi dengan **sistem update otomatis**. Anda tidak perlu lagi melakukan install ulang aplikasi saat ada pembaruan!

### 🔄 Cara Kerja

1. **Otomatis**
   - Aplikasi akan otomatis mengecek update setiap 1 menit
   - Jika ada update baru, akan muncul banner notifikasi di bagian atas layar
   - Klik "Update Sekarang" untuk menginstal update
   - Aplikasi akan reload otomatis dengan versi terbaru

2. **Manual** 
   - Buka menu **Profil**
   - Scroll ke bawah ke bagian **Versi Aplikasi**
   - Klik tombol **"Cek Update"**
   - Jika ada update, akan muncul notifikasi

### 📋 Langkah-Langkah Update

#### Saat Banner Update Muncul:

```
┌─────────────────────────────────────────┐
│ 🔄 Update Tersedia                      │
│ Versi baru aplikasi sudah siap diinstal│
│                                         │
│ [Update Sekarang]  [✕]                │
└─────────────────────────────────────────┘
```

1. Klik **"Update Sekarang"**
2. Tunggu proses instalasi (2-5 detik)
3. Aplikasi akan reload otomatis
4. Selesai! Anda sudah menggunakan versi terbaru

#### Atau, Cek Manual dari Profil:

1. Tap icon profil Anda di pojok kanan atas
2. Pilih **"Profil Saya"**
3. Scroll ke bawah ke bagian **"Versi Aplikasi"**
4. Lihat versi saat ini dan timestamp update terakhir
5. Klik **"Cek Update"**
6. Ikuti instruksi jika ada update tersedia

### ❓ FAQ

**Q: Apakah data saya akan hilang saat update?**  
A: Tidak! Semua data Anda tetap aman. Update hanya mengubah kode aplikasi, bukan data Anda.

**Q: Berapa lama proses update?**  
A: Biasanya hanya 2-5 detik. Sangat cepat!

**Q: Apakah update wajib?**  
A: Sangat disarankan untuk selalu update ke versi terbaru agar mendapat fitur baru dan perbaikan bug. Anda bisa dismiss banner, tapi akan muncul lagi nanti.

**Q: Bagaimana jika saya tidak melihat banner update?**  
A: Cek manual dari halaman Profil → Versi Aplikasi → Cek Update

**Q: Apakah update menggunakan banyak data internet?**  
A: Tidak, hanya beberapa KB saja (biasanya kurang dari 100KB).

**Q: Saya sudah update tapi masih lihat versi lama?**  
A: Coba:
1. Close aplikasi sepenuhnya (swipe dari recent apps)
2. Buka lagi aplikasi
3. Atau, uninstall dan install ulang dari browser

**Q: Bagaimana cara melihat versi aplikasi saat ini?**  
A: Buka Profil → scroll ke "Versi Aplikasi" → lihat nomor versi dan tanggal update

### 🆕 Apa yang Baru?

#### Versi 2.2.0 (16 November 2025)
- ✅ **Auto-Update System** - Tidak perlu install ulang manual
- ✅ **Update Banner** - Notifikasi yang jelas saat ada update
- ✅ **Manual Check** - Tombol cek update di halaman profil
- ✅ **Version Info** - Lihat versi dan timestamp aplikasi
- ✅ **Fixed** - Duplikasi pesan login berhasil

#### Versi 2.1.0 (15 November 2025)
- 🔔 Push notification untuk update status aplikasi
- 📊 Dashboard monitoring real-time
- 📤 Upload dokumen dengan preview
- 🎨 UI improvements

### 📞 Butuh Bantuan?

Jika mengalami masalah dengan update:

1. **Via WhatsApp**: [+62 838 7960 2855](https://wa.me/6283879602855)
2. **Email**: cs@bizmark.id
3. **Portal**: Menu Help → Chat dengan Admin

---

## Untuk Developer/Admin

### 🚀 Deployment Checklist

Setiap kali melakukan deployment dengan perubahan kode:

```bash
# 1. Update version
./update-pwa-version.sh

# 2. Review changes
git diff public/sw.js public/manifest.json public/version.json

# 3. Commit
git add public/
git commit -m "chore: bump version to $(grep -oP '"version": "\K[^"]+' public/version.json)"

# 4. Push to production
git push origin main

# 5. Deploy
# (Your deployment command here)

# 6. Verify
# - Check version.json accessible: curl https://bizmark.id/version.json
# - Check SW updated: curl https://bizmark.id/sw.js | grep CACHE_VERSION
# - Test update flow in browser DevTools
```

### 🔍 Monitoring

Check update adoption:

```bash
# View version distribution from analytics
# Check how many users on each version
# Monitor update success rate
```

### 🐛 Force Update All Users

If critical bug needs immediate fix:

```javascript
// Add to sw.js temporarily
self.addEventListener('message', (event) => {
  if (event.data.type === 'FORCE_UPDATE') {
    self.skipWaiting();
  }
});

// Then call from any client
if (navigator.serviceWorker.controller) {
  navigator.serviceWorker.controller.postMessage({
    type: 'FORCE_UPDATE'
  });
}
```

### 📊 Analytics Events

Track these events:
- `pwa_update_shown` - Update banner displayed
- `pwa_update_installed` - User clicked "Update Now"
- `pwa_update_dismissed` - User dismissed banner
- `pwa_update_completed` - Update successful
- `pwa_manual_check` - User clicked manual check

---

**System Version:** 2.2.0  
**Last Updated:** November 16, 2025  
**Documentation:** See `PWA_AUTO_UPDATE.md` for technical details
