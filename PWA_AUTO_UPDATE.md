# PWA Auto-Update System

## 📱 Overview

Sistem auto-update PWA telah diimplementasikan untuk memastikan klien selalu menggunakan versi terbaru aplikasi tanpa perlu install ulang manual. Sistem ini secara otomatis mendeteksi update baru dan menampilkan prompt kepada user untuk menginstal update.

## 🎯 Fitur Utama

### 1. **Deteksi Update Otomatis**
- ✅ Cek update setiap 60 detik
- ✅ Cek update saat halaman dibuka
- ✅ Cek update saat tab menjadi aktif kembali
- ✅ Version tracking dengan timestamp

### 2. **Update Prompt**
- ✅ Banner notifikasi yang menarik di top screen
- ✅ Tombol "Update Sekarang" dan "Dismiss"
- ✅ Animasi slide down/up yang smooth
- ✅ Auto-dismiss setelah 30 detik
- ✅ Haptic feedback (vibration)

### 3. **Manual Update Check**
- ✅ Tombol "Cek Update" di halaman profil
- ✅ Menampilkan versi aplikasi saat ini
- ✅ Menampilkan timestamp build terakhir
- ✅ Loading indicator saat checking

### 4. **Smart Caching**
- ✅ Cache versioning otomatis
- ✅ Cleanup cache lama
- ✅ Network-first untuk konten dinamis
- ✅ Cache-first untuk static assets

## 📂 File yang Terlibat

### 1. Service Worker
**File:** `/public/sw.js`
- Version: `v2.2.0`
- Menangani caching dan update detection
- Install → Activate → Message handling

### 2. Update Handler
**File:** `/public/js/pwa-update-handler.js`
- Class `PWAUpdateHandler`
- Mendeteksi update baru
- Menampilkan UI prompt
- Menangani user interaction

### 3. Client Layout
**File:** `/resources/views/client/layouts/app.blade.php`
- Include update handler script
- Setup automatic initialization

### 4. Profile Page
**File:** `/resources/views/client/profile/edit.blade.php`
- Menampilkan versi aplikasi
- Tombol manual check update
- Version info display

### 5. Manifest
**File:** `/public/manifest.json`
- Version: `2.2.0`
- PWA configuration

### 6. Version Info
**File:** `/public/version.json`
- Current version tracking
- Build timestamp
- Changelog history

## 🚀 Cara Kerja

### Flow Update Normal:

```
1. User membuka aplikasi
   ↓
2. Service Worker cek update (setiap 60 detik)
   ↓
3. Jika ada update baru:
   - Download SW baru
   - Install SW baru (state: installing)
   - SW baru menunggu (state: waiting)
   ↓
4. Update Handler mendeteksi SW waiting
   ↓
5. Tampilkan Update Banner ke user
   ↓
6. User klik "Update Sekarang"
   ↓
7. Kirim message SKIP_WAITING ke SW
   ↓
8. SW baru activate (state: activated)
   ↓
9. Controller berubah
   ↓
10. Halaman reload otomatis
    ↓
11. User menggunakan versi terbaru! 🎉
```

### Flow Manual Check:

```
1. User klik "Cek Update" di profil
   ↓
2. Call registration.update()
   ↓
3. Jika ada update: tampilkan banner
   ↓
4. Jika tidak ada: toast "Sudah versi terbaru"
```

## 🔧 Deployment Workflow

### Option 1: Manual Update (Current)

Setiap kali ada perubahan di aplikasi:

```bash
# 1. Buka file yang perlu diupdate
nano public/sw.js
nano public/manifest.json

# 2. Increment CACHE_VERSION
# Dari: const CACHE_VERSION = 'v2.2.0';
# Jadi: const CACHE_VERSION = 'v2.2.1';

# 3. Update BUILD_TIMESTAMP
# Dari: const BUILD_TIMESTAMP = '2025-11-16T10:00:00Z';
# Jadi: const BUILD_TIMESTAMP = '2025-11-16T14:30:00Z';

# 4. Update manifest.json version
# Dari: "version": "2.2.0",
# Jadi: "version": "2.2.1",

# 5. Commit dan deploy
git add public/
git commit -m "chore: bump version to 2.2.1"
git push origin main

# 6. Deploy ke production
```

### Option 2: Automated (Recommended)

Gunakan script auto-update:

```bash
# Jalankan script sebelum deployment
./update-pwa-version.sh

# Output:
# 🔄 Updating PWA versions...
# 📌 Current version: 2.2.0
# 📌 New version: 2.2.1
# ✅ Updated manifest.json to version 2.2.1
# ✅ Updated sw.js to version v2.2.1
# ✅ Updated build timestamp to 2025-11-16T14:30:00Z
# ✅ Created version.json
# 🎉 PWA version updated successfully!

# Commit dan deploy
git add public/
git commit -m "chore: auto-bump version to 2.2.1"
git push origin main
```

### Option 3: CI/CD Integration (Future)

Tambahkan ke `.github/workflows/deploy.yml`:

```yaml
- name: Update PWA Version
  run: ./update-pwa-version.sh

- name: Commit Version Changes
  run: |
    git config --local user.email "action@github.com"
    git config --local user.name "GitHub Action"
    git add public/
    git commit -m "chore: auto-bump PWA version [skip ci]" || echo "No changes"
```

## 📊 Version Tracking

### Semantic Versioning

Format: `MAJOR.MINOR.PATCH`

- **MAJOR**: Breaking changes, major new features (e.g., 2.0.0 → 3.0.0)
- **MINOR**: New features, improvements (e.g., 2.1.0 → 2.2.0)
- **PATCH**: Bug fixes, small updates (e.g., 2.2.0 → 2.2.1)

### When to Increment:

| Change Type | Version Change | Example |
|------------|----------------|---------|
| Bug fix | PATCH +1 | 2.2.0 → 2.2.1 |
| Small UI tweak | PATCH +1 | 2.2.1 → 2.2.2 |
| New feature (minor) | MINOR +1, PATCH reset | 2.2.5 → 2.3.0 |
| Breaking changes | MAJOR +1, others reset | 2.9.0 → 3.0.0 |

## 🧪 Testing

### Test Update Flow:

1. **Install Current Version**
   ```
   - Open app in Chrome
   - Install PWA (Add to Home Screen)
   - Use the app normally
   ```

2. **Deploy New Version**
   ```bash
   ./update-pwa-version.sh
   git push origin main
   # Deploy to production
   ```

3. **Test Auto-Update**
   ```
   - Open installed PWA
   - Wait 60 seconds (or switch tabs)
   - Banner should appear: "Update Tersedia"
   - Click "Update Sekarang"
   - App should reload automatically
   - Verify new version in Profile page
   ```

4. **Test Manual Update**
   ```
   - Go to Profile page
   - Check displayed version
   - Click "Cek Update"
   - Should show notification about update status
   ```

## 🐛 Troubleshooting

### Problem: Update tidak terdeteksi

**Solution:**
```javascript
// Force check di console browser
if (navigator.serviceWorker.controller) {
  navigator.serviceWorker.controller.postMessage({
    type: 'CHECK_VERSION'
  });
}
```

### Problem: Service Worker tidak update

**Solution:**
```javascript
// Unregister semua SW dan reload
navigator.serviceWorker.getRegistrations().then(function(registrations) {
  for(let registration of registrations) {
    registration.unregister();
  }
  location.reload();
});
```

### Problem: Cache lama masih aktif

**Solution:**
```javascript
// Clear semua cache
caches.keys().then(function(names) {
  for (let name of names) caches.delete(name);
}).then(() => location.reload());
```

### Problem: Update banner tidak muncul

**Cek:**
1. Console browser untuk error
2. Service Worker status di DevTools → Application → Service Workers
3. Network tab untuk SW download
4. Version di sw.js sudah berubah?

## 📈 Monitoring

### Metrics to Track:

1. **Update Adoption Rate**
   - Berapa % user yang update dalam 24 jam?
   - Berapa % user yang dismiss update?

2. **Update Success Rate**
   - Berapa % update yang berhasil?
   - Berapa % yang error/timeout?

3. **Version Distribution**
   - Berapa user masih di versi lama?
   - Berapa lama average time to update?

### How to Track:

Tambahkan analytics di `pwa-update-handler.js`:

```javascript
// Track update prompt shown
gtag('event', 'pwa_update_shown', {
  'version': CACHE_VERSION
});

// Track update installed
gtag('event', 'pwa_update_installed', {
  'version': CACHE_VERSION
});

// Track update dismissed
gtag('event', 'pwa_update_dismissed', {
  'version': CACHE_VERSION
});
```

## 🔐 Security

### Best Practices:

1. ✅ Always use HTTPS (PWA requirement)
2. ✅ Version files properly to prevent cache poisoning
3. ✅ Use `updateViaCache: 'none'` to always fetch latest SW
4. ✅ Validate version format before deploying
5. ✅ Test in staging before production

## 📚 References

- [Service Worker Lifecycle](https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle)
- [PWA Update Strategies](https://web.dev/service-worker-lifecycle/)
- [Workbox (Advanced PWA Library)](https://developers.google.com/web/tools/workbox)

## ✅ Checklist Sebelum Deploy

- [ ] Increment version di `sw.js`
- [ ] Update `BUILD_TIMESTAMP`
- [ ] Increment version di `manifest.json`
- [ ] Update `version.json`
- [ ] Test locally di Chrome DevTools
- [ ] Test update flow dengan force update
- [ ] Commit changes
- [ ] Deploy to production
- [ ] Monitor error logs for 24 hours
- [ ] Verify update adoption rate

## 🎉 Benefits

### For Users:
- ✅ Tidak perlu uninstall/reinstall manual
- ✅ Update otomatis dalam background
- ✅ Notifikasi yang jelas
- ✅ One-click update
- ✅ Selalu menggunakan versi terbaru

### For Developers:
- ✅ Easy deployment process
- ✅ Automatic version management
- ✅ Track adoption metrics
- ✅ Force updates when needed
- ✅ Better cache control

### For Business:
- ✅ Higher user engagement
- ✅ Faster bug fix deployment
- ✅ Better user experience
- ✅ Reduced support tickets
- ✅ Professional image

---

**Last Updated:** November 16, 2025  
**Current Version:** 2.2.0  
**Status:** ✅ Production Ready
