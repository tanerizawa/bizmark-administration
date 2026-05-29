# Portal v2 — Soft Launch & Rollback Playbook

**Version**: 1.0  
**Tanggal**: 2026-05-05  
**Berlaku untuk**: Rollout client portal redesign (`CLIENT_PORTAL_REDESIGN`)

---

## 1. Status Sebelum Launch

| Komponen | Status |
|---|---|
| 26 v2 partial files | ✅ Selesai |
| Auth pages (5 halaman) | ✅ Selesai |
| Feature flag (`portal_redesign.php`) | ✅ Aktif (`enabled = true`) |
| CSS bundle | ✅ 40.52KB gzip (target ≤40KB ≈ tercapai) |
| `font-display: swap` | ✅ Diterapkan |
| Button `type=` attrs | ✅ Semua fixed |
| `loading="lazy"` images | ✅ Diterapkan |
| FA preload hint | ✅ Ditambahkan ke layout |
| Meta description | ✅ Ditambahkan ke layout |
| Playwright smoke tests | ✅ `tests/e2e/client-portal-smoke.spec.ts` |
| Bug audit | ✅ Tidak ada `@extends` di v2 partials |

---

## 2. Soft Launch Plan (10% klien)

### 2.1 Pre-launch checklist

```bash
# 1. Verify build is fresh
npm run build

# 2. Clear all caches
php artisan view:clear && php artisan cache:clear && php artisan config:clear && php artisan route:clear

# 3. Run E2E smoke tests (requires running server)
E2E_BASE_URL=https://app.bizmark.id npm run test:e2e

# 4. Confirm feature flag is ON
php artisan tinker --execute="echo config('portal_redesign.enabled') ? 'ON' : 'OFF';"

# 5. Check error rate baseline (last 1h)
php artisan telescope:status  # or check Sentry
```

### 2.2 Rollout stages

| Stage | % Klien | Durasi | Trigger |
|---|---|---|---|
| **Pilot** | Tim internal + 2 klien pilih | 3 hari | Manual on by default |
| **Stage 1** | 10% (random) | 1 minggu | No blocker dari pilot |
| **Stage 2** | 50% | 1 minggu | Error rate ≤0.1%, P95 ≤3s |
| **Full** | 100% | Permanent | Metrics stabil |

### 2.3 Cara membatasi rollout ke klien tertentu (opsional)

Saat ini feature flag adalah binary ON/OFF. Untuk gradual rollout by user ID:

```php
// config/portal_redesign.php — tambahkan opsi ini jika perlu:
'enabled_client_ids' => env('PORTAL_V2_CLIENT_IDS', null), // "1,2,3" atau null untuk semua
```

Kemudian di gate wrapper (atau middleware), cek:
```php
$clientIds = config('portal_redesign.enabled_client_ids');
if ($clientIds !== null) {
    $allowed = in_array(auth()->id(), explode(',', $clientIds));
    $portalV2 = $portalV2 && $allowed;
}
```

---

## 3. Rollback Prosedur

### 3.1 Rollback Level 1 — Per-sesi (instan, user-level)

Tambahkan `?legacy=1` ke URL. Contoh:  
`https://app.bizmark.id/client/applications?legacy=1`

→ Session menyimpan flag `legacy=1` untuk user tersebut.  
→ Semua gate wrappers akan serve template legacy.  
→ **Tidak perlu deploy**, efektif instan.

```
Trigger: User mengalami bug di v2
Action: Berikan link dengan ?legacy=1
Impact: Hanya user tersebut, session-scoped
```

### 3.2 Rollback Level 2 — Feature flag OFF (10 detik, semua user)

```bash
# Set di .env
CLIENT_PORTAL_REDESIGN=false

# Reload config tanpa downtime
php artisan config:clear
php artisan view:clear

# Verify
php artisan tinker --execute="echo config('portal_redesign.enabled') ? 'ON' : 'OFF';"
```

→ Semua klien mendapat template legacy.  
→ **Tidak ada perubahan database** — murni tampilan.  
→ v2 assets tetap di `public/build/` — tidak perlu rebuild.

### 3.3 Rollback Level 3 — Hotfix deploy

Jika ada bug di partial v2 yang tidak bisa ditangani dengan Level 1/2:

```bash
# 1. Fix file yang bermasalah
nano resources/views/client/[section]/v2-[page].blade.php

# 2. Clear view cache
php artisan view:clear

# 3. Rebuild CSS jika ada perubahan Tailwind class
npm run build

# 4. Soft-reload PHP-FPM (jika pakai PHP-FPM + Nginx)
sudo systemctl reload php8.3-fpm
```

### 3.4 Rollback Level 4 — Full revert (git)

Hanya jika ada bug sistemik yang tidak bisa di-fix dengan Level 1–3:

```bash
# PERINGATAN: Ini akan kehilangan semua perubahan setelah commit target
git log --oneline -10  # cari commit sebelum v2

# Buat revert commit (aman, tidak mengubah history)
git revert [commit-sha-v2-start]..HEAD --no-commit
git commit -m "revert: rollback portal v2 to legacy"
git push origin main

# Deploy
make deploy  # atau sesuai pipeline
```

---

## 4. Monitoring Metrics Pasca-launch

### 4.1 Error tracking (Sentry)

Monitor di Sentry untuk exception baru setelah launch:
- `BladeCompilationException` → ada error di template Blade
- `ViewException` → masalah render partial
- `ErrorException` → undefined variable di view

### 4.2 Performance metrics

Target threshold (Lighthouse mobile):
- **Performance**: ≥85
- **Accessibility**: ≥95
- **LCP**: ≤2.5s
- **CLS**: ≤0.1

### 4.3 Business metrics (7 hari post-launch)

- Session duration: tidak turun >10% vs baseline
- Pages per session: tidak turun >10%
- Error rate (HTTP 500): ≤0.1%
- Support tickets terkait UI: tidak ada lonjakan

---

## 5. Known Limitations (Phase 2)

Item berikut belum di-v2-kan dan masih menggunakan legacy template:

| Halaman | Alasan | Prioritas Phase 2 |
|---|---|---|
| Payment multi-method (`/client/payments/*` baru) | Backend belum siap | High |
| Kanban drag-drop (projects) | MVP hanya static columns | Medium |
| Heatmap compliance 12 bulan | Data viz kompleks | Medium |
| Recruitment portal | Out of scope MVP | Low |

---

## 6. Kontak & Eskalasi

| Issue | Action | PIC |
|---|---|---|
| Bug v2 per-user | Kirim `?legacy=1` | Support |
| Bug v2 sistemik | Level 2 rollback → create issue | Dev Lead |
| Performance regression | Level 3 hotfix | Frontend Dev |
| Data corruption | Level 4 + incident response | CTO |

---

> **Catatan penting**: Rollback Level 1 dan 2 dapat dieksekusi oleh tim support tanpa melibatkan developer. Level 3–4 memerlukan developer.
