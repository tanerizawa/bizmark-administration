# Laporan Audit & Redesign Landing Page (Bizmark.ID)

Tanggal: 2026-04-19  
Ruang lingkup: landing page (`/`, `/en`) + komponen publik terkait (navbar, footer, about, services, contact, tools cards, FAQ, CTA).  
Target standar: usability + accessibility (WCAG 2.1), responsive, konsistensi visual, dan pesan brand yang jelas.

## 1) Ringkasan Eksekutif

**Temuan utama**
- Brand dan value Bizmark.ID sudah terlihat (perizinan, transparansi, kecepatan, layanan nasional, AI tools), tetapi **inkonsistensi visual** antar halaman publik tinggi (landing “magazine editorial”, services “neuroscience palette”, contact “dark/apple UI”).
- Landing versi lama punya struktur kuat (hero → layanan → tools → proses → about → CTA), namun banyak styling inline dan duplikasi template (ID/EN) yang memperbesar biaya maintenance.
- Aksesibilitas dasar sudah ada (skip link, fokus global), tetapi komponen dropdown/accordion sebelumnya kurang optimal untuk pola WCAG keyboard/ARIA yang konsisten.

**Hasil redesign (diimplementasikan)**
- Landing ID/EN dipindahkan ke layout bersama (`landing.layout`) dengan **satu komposisi halaman** (`landing.pages.home`) untuk menekan duplikasi dan menjaga konsistensi.
- Dibangun hero baru dengan “Quick start” (jalur cepat ke AI cost estimate, SHP maker, free permit analysis) agar value utama aplikasi (AI + workflow perizinan) terasa sejak detik pertama.
- Contact page didesain ulang agar konsisten dengan sistem visual landing (tanpa tema gelap terpisah).

## 2) Audit Desain Saat Ini (Before)

### 2.1 Struktur Layout & IA
- Navbar global: Home, Services, Process, About, Blog, Permohonan, Tools, switcher bahasa, login.
- Landing: hero (foto + overlay), stats, services grid, free tools, process, about, CTA, blog (opsional).
- Footer: informasi kontak, legal, bahasa, serta daftar “jangkauan layanan” berbasis kota (SEO).

### 2.2 Warna Dominan
- Landing: dominan navy gelap + aksen oranye + aksen biru muda, dengan latar putih/cream pada section.
- Services: menggunakan palet berbeda (biru lembut/coral/green) yang tidak selalu sejalan dengan landing.
- Contact: tema gelap dengan “apple blue”, menyimpang jauh dari halaman lain.

### 2.3 Tipografi
- Mayoritas memakai Inter (good), dengan heading tebal (700–800).
- Variasi ukuran/spacing cukup baik, tetapi ada banyak inline style yang membuat konsistensi sulit dijaga.

### 2.4 Elemen Visual
- Hero image “meeting/handshake” menyampaikan profesionalisme/kolaborasi.
- Ikon FontAwesome banyak dipakai sebagai penanda kategori/benefit.
- Ada highlight “tools AI” yang relevan dengan positioning produk.

### 2.5 Pesan Branding (yang tersirat)
- “Konsultan perizinan/lingkungan & legalitas usaha” + “transparan & cepat”.
- “Bersertifikasi/berpengalaman” dan “cakupan nasional”.
- “AI tools” sebagai diferensiasi.

## 3) Temuan UX/UI & Accessibility (WCAG 2.1)

### 3.1 Usability (Heuristic)
- **Value proposition** sudah ada, tapi “jalur tindakan” masih bercampur: CTA ke konsultasi, scroll, tools, dan daftar layanan panjang.
- **Konsistensi visual** lintas halaman publik rendah; user bisa merasa “pindah situs” saat masuk contact/services.
- **Maintenance**: duplikasi view landing ID/EN dan style inline meningkatkan risiko regresi dan menghambat iterasi.

### 3.2 Accessibility (WCAG 2.1) – Poin Kunci
- Sudah ada **skip link** dan `:focus-visible` styling (positif).
- Dropdown (Tools/Language) sudah berbasis `<button>`, tetapi perlu perhatian pada state `aria-expanded` saat toggle (sebagian sudah di-handle via JS).
- Accordion FAQ sebaiknya pakai elemen semantik (`<details>/<summary>`) untuk perilaku keyboard default yang baik.
- Tap target: mayoritas tombol/ikon sudah cukup besar, namun perlu konsistensi minimal 44×44 untuk elemen kecil.

## 4) Riset Pengguna (User-Centered Design)

Catatan: Riset ini berbasis analisis konteks aplikasi + pola umum industri perizinan (tanpa wawancara langsung).

### 4.1 Segmentasi Pengguna
1. **Pemilik UMKM / Founder lokal**
   - Tujuan: NIB/OSS, izin dasar, butuh estimasi biaya/waktu.
   - Pain: bingung KBLI, takut salah dokumen, butuh cepat.
2. **HSE/Compliance Manager manufaktur**
   - Tujuan: UKL-UPL/AMDAL/LB3, audit rutin, pembaruan regulasi.
   - Pain: risk penalty, kompleks, koordinasi lintas tim, butuh monitoring progres.
3. **Investor / PMA / ekspansi**
   - Tujuan: pendirian PT PMA + izin sektoral.
   - Pain: uncertainty, bahasa, timeline, dan koordinasi instansi.

### 4.2 Persona (Ringkas)
- **Raka (Founder lokal)**: perlu “cek kebutuhan izin cepat”, biaya transparan, CTA WhatsApp.
- **Dewi (HSE Manager)**: perlu “peta jalan compliance”, deliverable terukur, update berkala.
- **James (Investor PMA)**: perlu trust, proses jelas, bahasa Inggris, jalur mulai yang ringkas.

### 4.3 User Journey (Landing → Konversi)
**Journey A: Founder**
1. Landing → lihat estimasi biaya (AI) → konsultasi → onboarding dokumen → tracking.

**Journey B: HSE/Compliance**
1. Landing → lihat layanan lingkungan → baca proses/FAQ → konsultasi → timeline & reporting.

**Journey C: PMA**
1. Landing EN → proses + trust proof → konsultasi → proposal → eksekusi.

## 5) Rancangan Baru (After) – IA & Wireframe

### 5.1 Prinsip Desain
- **Clarity first**: headline + subtitle menjawab “Apa yang Bizmark lakukan & untuk siapa”.
- **Fast paths**: shortcut ke fitur AI dan konsultasi untuk mempercepat time-to-value.
- **Trust & compliance**: tampilkan bukti proses, testimoni, dan struktur kerja.
- **Accessible by default**: komponen semantik untuk FAQ dan fokus keyboard yang jelas.

### 5.2 Wireframe (Teks)
1. Hero (headline + CTA) + “Quick start” card (AI estimate, SHP, permit analysis).
2. Metrics strip.
3. Featured services (6 card) + link ke daftar layanan.
4. Free digital tools.
5. Process steps (dari config).
6. About summary + CTA ke contact/services.
7. Testimonials (top 3).
8. FAQ (details/summary).
9. CTA penutup (consult/email/whatsapp).

## 6) Design System (Ringkas)

### 6.1 Token
- Primary: `--color-primary` (navy) untuk trust & profesional.
- Secondary: `--color-secondary` (orange) untuk CTA utama.
- Accent: `--color-accent` (sky) untuk link/focus.
- Success: `--color-success` (green) untuk status/WhatsApp.
- Surface: `--surface`, `--surface-warm`, `--surface-cool`, `--surface-dark`.
- Text: `--text-primary`, `--text-secondary`, `--text-tertiary`.

### 6.2 Komponen
- Button: `.btn` + variasi `btn-primary`, `btn-secondary`, `btn-ghost`, `btn-outline-primary`, `btn-success`
- Card: `.card` dan `.magazine-card`
- Section: `.section`, `.section-sm`, `.container-wide`
- FAQ: `.faq-item` + `<details>/<summary>` untuk aksesibilitas

## 7) Prototype Interaktif & “Usability Test” (Praktis)

**Prototype**: halaman nyata yang sudah ter-implement (landing `/` dan `/en`, contact `/contact`).  
**Skenario uji cepat (5–10 menit, moderated/remote)**
1. “Temukan cara tercepat untuk tahu izin apa yang kamu butuh.”
2. “Cari estimasi biaya dan langkah berikutnya.”
3. “Temukan cara menghubungi Bizmark.”
4. “Cek FAQ tentang dokumen/timeline.”

**Kriteria keberhasilan**
- Waktu menemukan jalur “AI estimate / permit analysis” < 20 detik.
- User bisa submit pesan (contact form) dengan keyboard saja.
- Fokus keyboard terlihat jelas di semua komponen interaktif.

## 8) Implementasi (File Utama)

- Landing home composition: `resources/views/landing/pages/home.blade.php`
- Landing wrappers (ID/EN): `resources/views/landing/id/index.blade.php`, `resources/views/landing/en/index.blade.php`
- Design system CSS vars & components: `resources/views/landing/partials/styles-modern.blade.php`
- Contact redesign: `resources/views/contact/index.blade.php`

