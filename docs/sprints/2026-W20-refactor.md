# 2026-W20 Refactor Sprint
## Perencanaan: Senin, 21 Apr - Jumat, 25 Apr 2026

> Status: **COMPLETE** ✅ — kecuali W20-14 (butuh aksi manual production)

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W20 Refactor Sprint`
- **Focus**: CSS Bootstrap → Tailwind migration (finalisasi), visual QA, test coverage boost

---

## 1) Carry-over (dari W19)

| ID | Item | Catatan |
|----|------|--------|
| W19-carry | SENTRY_LARAVEL_DSN production | Perlu diset manual di server `.env` |
| W19-carry | Migration squash | HIGH RISK — deferred jangka panjang |

---

## 2) Backlog

| ID | P | Item | Status |
|----|---|------|--------|
| W20-01 | P1 | CSS: `admin/email/campaigns/edit.blade.php` → 100% Tailwind (Bootstrap modal → Alpine.js preview modal) | DONE |
| W20-02 | P1 | CSS: `admin/email-accounts/edit.blade.php` → 100% Tailwind (Alpine.js auto-reply + type toggle) | DONE |
| W20-03 | P1 | CSS: `admin/email/inbox/compose.blade.php` → 100% Tailwind (form + draft localStorage) | DONE |
| W20-04 | P1 | CSS: `admin/email/inbox/reply.blade.php` → 100% Tailwind (quick responses + draft localStorage + Alpine.js collapse) | DONE |
| W20-05 | P1 | CSS: `client/applications/revisions/show.blade.php` → 100% Tailwind (48 hits → 0, Bootstrap modal → Alpine.js) | DONE |
| W20-06 | P1 | CSS: `cash-accounts/tabs/general-transactions.blade.php` → 100% Tailwind (2 Bootstrap modals + `bootstrap.Modal` JS → Alpine.js) | DONE |
| W20-07 | P2 | CSS: `reconciliations/index.blade.php` → 100% Tailwind (`alert alert-*` → Tailwind utilities) | DONE |
| W20-08 | P2 | CSS: `projects/show.blade.php` → 100% Tailwind (`alert alert-*` → Tailwind utilities) | DONE |
| W20-09 | P2 | CSS: `cash-accounts/index.blade.php` → 100% Tailwind (`alert alert-*` → Tailwind utilities) | DONE |
| W20-10 | P2 | CSS: `admin/applications/show.blade.php` → 100% Tailwind (`form-control` → Tailwind) | DONE |
| W20-11 | P1 | CSS: `admin/email/campaigns/create.blade.php` → 100% Tailwind (Bootstrap preview modal → Alpine.js) | DONE |
| W20-12 | P1 | **Bootstrap CDN REMOVED** dari `layouts/app.blade.php` — Bootstrap 5.3 CSS + JS bundle dihapus | DONE |
| W20-13 | P2 | Test coverage: push ke 264 (AdminEmailSettingsTest, 13 tests, 900 assertions) | DONE |
| W20-14 | P2 | Set SENTRY_LARAVEL_DSN di production `.env` + verify Sentry di staging | PENDING — aksi manual di server |
| W20-15 | P2 | Visual regression check — diklarifikasi: akses via `https://bizmark.id` (bukan Windsurf proxy). Port audit selesai, `docs/PORT_MAP.md` dibuat. | DONE |
| W20-16 | P3 | Port & service mapping — audit semua port server, dokumentasi di `docs/PORT_MAP.md` | DONE |

---

## 3) Hasil & Catatan

### CSS Migration — COMPLETE ✅

- **0** `modal fade` / `data-bs-*` / `bootstrap.Modal` tersisa di **semua** views
- **0** Bootstrap CDN (`bootstrap.min.css`, `bootstrap.bundle.min.js`) di-load di `layouts/app.blade.php`
- Views yang masih terdeteksi "btn btn-*" (`landing/`, `programmatic/`, `services/`, `permohonan/`) menggunakan **custom CSS** (`public/css/tokens.css`) — bukan Bootstrap library
- Total file yang dimigrasi sesi W20: **11 views + 1 layout** = 12 file

### Alpine.js Modal Pattern (Standard)

Semua modal Bootstrap telah diganti dengan pola Alpine.js berikut:

```blade
<div id="myModalRoot" x-data="{ open: false }">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-lg">
            ...
        </div>
    </div>
</div>
```

JS trigger: `document.getElementById('myModalRoot').__x.$data.open = true;`

---

## 4) Referensi

- `docs/sprints/2026-W19-refactor.md`
- `docs/SYSTEM_ARCHITECTURE_AUDIT.md` — section 13
