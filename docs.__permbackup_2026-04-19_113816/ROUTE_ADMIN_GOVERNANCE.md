# Route Admin — Governance & Konvensi

Dokumen ini mengatur cara menambah dan mengubah route di bawah prefix `/admin` agar konsisten setelah pemecahan ke `routes/admin/*.php`.

---

## Struktur file

| File | Isi domain |
|------|------------|
| `routes/web.php` | Publik, auth, client, webhook, API ringan, redirect. **Bukan** definisi bulk admin. |
| `routes/web_admin.php` | Aggregator: hanya `require` partial di bawah. |
| `routes/admin/core.php` | Dashboard, profil, export ringan, resource inti (projects, tasks, documents, clients, leads). |
| `routes/admin/operations.php` | Keuangan, artikel, permit, settings berat, lead/service-cost lanjutan. |
| `routes/admin/settings_recruitment.php` | Settings, recruitment, email hub. |
| `routes/admin/communications_seo_ai.php` | Email detail, KBLI settings, SEO, AI documents. |

Tambah route admin baru: **pilih file domain terdekat**; jangan membesarkan satu file tanpa alasan.

---

## Aturan teknis wajib

### 1. Setiap partial route harus valid PHP

File di `routes/admin/*.php` **wajib** diawali `<?php` di baris pertama.

Tanpa ini, isi file bisa dianggap output teks dan registrasi route putus sebagian.

### 2. Resolver controller

Gunakan **FQCN** untuk controller di partial admin, contoh:

```php
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
```

Alasan: `require` dari file lain **tidak** mewarisi `use` dari `web.php`. Aliasan singkat di PR cukup.

### 3. Nama route & grup middleware

- Ikuti pola nama existing (`admin.*`, `projects.*`, dll.).
- Jangan mengganti nama route publik tanpa redirect/compatibility kecuali RFC kecil.

### 4. Urutan route

- Route statis / literal dulu, wildcard `{param}` belakangan (hindari greedy match).
- Untuk SEO scores: POST/GET tetap sebelum `scores/{articleId}`.

### 5. Verifikasi sebelum merge

```bash
php -l routes/web.php routes/web_admin.php routes/admin/*.php
php artisan route:list --except-vendor
```

Target: perintah sukses, jumlah route stabil (regresi = diff tak terduga pada route kritikal).

---

## Checklist PR (tambah route admin)

- [ ] File partial yang benar (`core` / `operations` / `settings_recruitment` / `communications_seo_ai`)
- [ ] `<?php` di baris pertama file
- [ ] FQCN controller
- [ ] Middleware + permission selaras dengan fitur
- [ ] `php -l` + `route:list` lulus
- [ ] Jika mengubah URL: dokumentasikan redirect di PR body

---

## Controller admin SEO

Redirect + flash (`success` / `error`) untuk modul `App\Http\Controllers\Admin\Seo\*` dipusatkan lewat trait `App\Http\Controllers\Admin\Seo\Concerns\SeoAdminFlashRedirect` agar pola konsisten.

---

## Anti-pola

- Jangan menyimpan credential atau path rahasia di route file.
- Jangan menggabungkan kembali seluruh admin ke satu file raksasa tanpa alasan arsitektur.
- Hindari `use` panjang di `web_admin.php` lalu mengandalkan partial tanpa FQCN — rawan drift.
