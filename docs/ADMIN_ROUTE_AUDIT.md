# Admin Route Audit & Permission Matrix

Dokumen ini mendeskripsikan pendekatan audit keamanan untuk seluruh admin routes (prefix `admin/`) di luar fokus awal permits/payments, termasuk klasifikasi risiko dan permission yang diperlukan.

## Cara menghasilkan laporan audit rute

Laporan audit rute bisa di-generate otomatis:

- Markdown:
  - `php artisan admin:routes-audit`
- JSON:
  - `php artisan admin:routes-audit --format=json`

Output berisi:
- method, uri, name
- module
- risk (HIGH/MEDIUM/LOW)
- required permission (proposed)
- flag apakah route tersebut tidak memiliki middleware permission yang sesuai

## Permission matrix (ringkas)

| Modul | Read (LOW/MEDIUM) | Write (MEDIUM/HIGH) | Catatan |
|---|---|---|---|
| Projects | `projects.view` | `projects.create` / `projects.edit` / `projects.delete` | Resource dipisah per action |
| Tasks | `tasks.view` | `tasks.create` / `tasks.edit` / `tasks.delete` / `tasks.assign` | Assignment dipisah |
| Documents | `documents.view` | `documents.upload` / `documents.delete` | Upload/edit diikat ke upload |
| Clients | `clients.view` | `clients.create` / `clients.edit` / `clients.delete` | Leads dikaitkan ke permissions ini |
| Institutions | `institutions.view` | `institutions.create` / `institutions.edit` / `institutions.delete` |  |
| Invoices | `invoices.view` | `invoices.create` / `invoices.edit` / `invoices.delete` / `invoices.approve` | High-risk |
| Finances | `finances.view` | `finances.manage_payments` / `finances.manage_expenses` / `finances.manage_accounts` | High-risk |
| Permits | `permits.manage` | `permits.manage` | Dilindungi terpisah |
| Email | `email.view_inbox` / `email.send_email` | `email.manage_*` / `email.manage` | Granular per sub-modul |
| SEO/Content | `content.view_articles` | `content.manage` / `content.create_articles` / `content.edit_articles` / `content.publish_articles` | SEO dianggap high-risk |
| Recruitment | `recruitment.view` | `recruitment.manage_jobs` / `recruitment.process_applications` | Dipisah per aksi |
| Settings | `settings.manage` | `settings.manage` | High-risk, akan jadi kandidat 2FA enforcement |
| Security | `security.view` / `security.manage` | `security.manage` | Dashboard metrics + audit logs |
| AI | `ai.manage_settings` | `ai.manage_settings` | High-risk |

## Klasifikasi risiko (aturan praktis)

- **HIGH**: operasi yang menyentuh konfigurasi sistem, security, AI settings, finance/invoices write, SEO/Content automation write, email settings/campaign send, recruitment pipeline write.
- **MEDIUM**: CRUD data operasional (projects/tasks/documents/clients) dan write pada leads.
- **LOW**: read-only list/detail operasional.

## Implementasi yang sudah diterapkan

- Pemisahan permission per action pada resource routes utama: [core.php](file:///home/bizmark/bizmark.id/routes/admin/core.php)
- Pengetatan permission pada routes operasional finansial/leads/content: [operations.php](file:///home/bizmark/bizmark.id/routes/admin/operations.php)
- Pengetatan permission pada recruitment (view vs manage): [settings_recruitment.php](file:///home/bizmark/bizmark.id/routes/admin/settings_recruitment.php)
- Pemisahan akses email/seo/kbli agar tidak berbagi guard yang terlalu luas: [communications_seo_ai.php](file:///home/bizmark/bizmark.id/routes/admin/communications_seo_ai.php)
- Permission baru untuk Security dashboard: `security.view`, `security.manage`
- Permission baru untuk AI settings: `ai.manage_settings`
- Enforced 2FA pada high-risk routes (settings, financial write, security, AI, dan financial exports).
