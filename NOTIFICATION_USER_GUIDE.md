# 🔔 Cara Menggunakan Fitur Test Notifikasi

## Langkah-Langkah Penggunaan

### 1️⃣ Akses Halaman Profil
1. Login ke Portal Klien
2. Klik menu **Profil** atau **Profile** di navigasi atas
3. Atau akses langsung: `https://bizmark.id/client/profile/edit`

### 2️⃣ Aktifkan Notifikasi (Pertama Kali)

**Jika Status: "Belum diaktifkan"**
1. Cari section **"Pengaturan Notifikasi"**
2. Klik tombol **"Aktifkan Notifikasi"** 
3. Browser akan meminta izin → Klik **"Allow"** atau **"Izinkan"**
4. Tunggu hingga status berubah menjadi **"Aktif ✓"**
5. Notifikasi test otomatis akan dikirim

### 3️⃣ Test Notifikasi

**Jika Status: "Aktif"**
1. Scroll ke section **"Pengaturan Notifikasi"**
2. Klik tombol **"Test Notifikasi"**
3. Tombol akan menampilkan "Mengirim..."
4. Tunggu beberapa detik
5. Notifikasi akan muncul:
   - Di desktop: pojok kanan bawah
   - Di mobile: notification bar

### 4️⃣ Hasil Test

**✅ Jika Berhasil:**
- Muncul notifikasi dengan judul: "🔔 Test Notifikasi"
- Pesan: "Notifikasi push Anda berfungsi dengan baik!"
- Toast hijau: "Test notifikasi berhasil dikirim ke X perangkat!"
- Tombol kembali normal

**❌ Jika Gagal:**
- Muncul toast merah dengan pesan error
- Periksa:
  - Apakah notifikasi sudah diaktifkan?
  - Apakah browser mengizinkan notifikasi?
  - Apakah koneksi internet stabil?

## 🎯 Tips & Troubleshooting

### Notifikasi Tidak Muncul?

**1. Periksa Permission Browser:**
- Chrome: Klik ikon gembok di address bar → Site Settings → Notifications → Allow
- Firefox: Klik ikon i di address bar → Permissions → Notifications → Allow
- Safari: Settings → Websites → Notifications → bizmark.id → Allow

**2. Periksa Do Not Disturb:**
- Windows: Matikan Focus Assist
- Mac: Matikan Do Not Disturb
- Android: Matikan mode Senyap
- iOS: Matikan Do Not Disturb

**3. Refresh & Retry:**
- Refresh halaman (F5)
- Klik "Test Notifikasi" lagi
- Jika masih gagal, logout dan login kembali

**4. Browser Compatibility:**
- ✅ Chrome 42+
- ✅ Firefox 44+
- ✅ Edge 17+
- ✅ Safari 16.4+ (iOS/Mac)
- ✅ Samsung Internet
- ❌ Internet Explorer (tidak didukung)

### Error: "Notifikasi diblokir"

**Cara Mengaktifkan Kembali:**
1. Klik ikon gembok di address bar
2. Cari "Notifications" atau "Notifikasi"
3. Ubah dari "Block" ke "Allow"
4. Refresh halaman
5. Klik "Test Notifikasi" lagi

### Error: "No push subscriptions found"

**Solusi Otomatis:**
Sistem akan otomatis mencoba subscribe ulang dan mengirim test lagi.

**Solusi Manual:**
1. Klik "Aktifkan Notifikasi" lagi
2. Tunggu hingga status "Aktif"
3. Klik "Test Notifikasi"

### Notifikasi Muncul 2x?

Ini normal! Bisa jadi:
- Satu dari service worker
- Satu dari direct browser notification
- Atau Anda memiliki 2 perangkat yang ter-subscribe

## 📱 Penggunaan di Mobile (PWA)

### Android:
1. Install PWA (Add to Home Screen)
2. Buka app dari home screen
3. Aktifkan notifikasi
4. Test notifikasi
5. Notifikasi akan muncul seperti app native

### iOS (Safari 16.4+):
1. Add to Home Screen
2. Buka dari home screen
3. Aktifkan notifikasi (hanya work di iOS 16.4+)
4. Test notifikasi
5. Notifikasi muncul di notification center

## 🔐 Keamanan & Privacy

### Data yang Disimpan:
- Subscription endpoint (encrypted)
- Public key untuk push
- Auth token
- Device info (browser, OS)

### Privacy:
- ✅ Data ter-enkripsi end-to-end
- ✅ Tidak bisa dibaca pihak ketiga
- ✅ Bisa di-unsubscribe kapan saja
- ✅ GDPR compliant

### Unsubscribe:
1. Block notifikasi di browser settings
2. Atau matikan toggle di "Pengaturan Notifikasi"
3. Data subscription akan dihapus otomatis

## 📊 Informasi Subscription

**Di section "Informasi Langganan" Anda bisa lihat:**
- Perangkat: Browser dan OS yang Anda gunakan
- Langganan: Jumlah perangkat yang ter-subscribe
- Status: Aktif/Tidak aktif

**Contoh:**
```
Perangkat: Chrome 120 on Windows 10
Langganan: 2 perangkat
Status: Aktif ✓
```

Artinya Anda subscribe dari 2 perangkat (misal: laptop & hp).

## 🎨 Preferensi Notifikasi

**Anda bisa mengatur jenis notifikasi:**
- ✅ Update Status: Perubahan status izin
- ✅ Permintaan Dokumen: Ada dokumen yang perlu dilengkapi
- ✅ Pengingat Deadline: Mendekati deadline
- ✅ Pesan Baru: Pesan dari admin/tim

**Cara Mengatur:**
1. Toggle ON/OFF sesuai preferensi
2. Perubahan auto-save
3. Efektif segera

## ❓ FAQ

**Q: Apakah notifikasi akan tetap muncul saat browser ditutup?**
A: Ya, jika Anda menggunakan PWA atau browser support background notifications.

**Q: Berapa banyak perangkat yang bisa subscribe?**
A: Unlimited. Satu akun bisa subscribe dari banyak perangkat.

**Q: Apakah ada biaya untuk notifikasi push?**
A: Tidak, gratis 100%.

**Q: Bagaimana cara unsubscribe?**
A: Block notifikasi di browser settings atau matikan toggle.

**Q: Apakah notifikasi akan mengganggu?**
A: Anda bisa atur preferensi jenis notifikasi yang ingin diterima.

**Q: Data saya aman?**
A: Ya, semua data ter-enkripsi dan tidak bisa dibaca pihak ketiga.

## 📞 Butuh Bantuan?

Jika masih mengalami kendala:
1. Screenshot error yang muncul
2. Catat browser & OS yang digunakan
3. Hubungi support: cs@bizmark.id
4. Atau gunakan fitur chat di portal

---

**Enjoy seamless notifications! 🎉**
