# 🚀 Quick Start - Email Marketing Bizmark.ID

## ✅ Status: READY TO USE

Sistem email management sudah lengkap dan siap digunakan! Semua views, controllers, models, dan routes sudah terimplementasi.

---

## 📍 Menu Lokasi

Akses dari sidebar admin panel:

```
📧 Email Management
├── 📥 Inbox (dengan badge unread count)
├── 📤 Campaigns
├── 👥 Subscribers (dengan badge active count)
└── 📄 Templates
```

---

## 🎯 3 Langkah Cepat

### 1️⃣ Konfigurasi Email (5 menit)

Edit `.env`:

```env
# Untuk Testing (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.ID"
```

**Cara dapat App Password Gmail:**
1. Buka https://myaccount.google.com/security
2. Enable "2-Step Verification"
3. Cari "App passwords"
4. Generate untuk "Mail"
5. Copy 16-digit code ke MAIL_PASSWORD

Lalu jalankan:
```bash
php artisan config:clear
```

### 2️⃣ Test Kirim Email (2 menit)

1. Buka **https://bizmark.id/admin/inbox**
2. Klik tombol **"Compose Email"**
3. Isi:
   - **To:** email@anda.com
   - **Subject:** Test Email Bizmark.ID
   - **Body:** Ini adalah test email pertama
4. Klik **"Send Email"**
5. Check inbox Anda

✅ Jika berhasil, SMTP sudah OK!

### 3️⃣ Kirim Campaign Pertama (5 menit)

1. Buka **https://bizmark.id/admin/campaigns**
2. Klik **"Create Campaign"**
3. Isi form:
   - **Campaign Name:** Newsletter November 2025
   - **Subject:** 🎉 Update Terbaru dari Bizmark.ID
   - **Template:** Pilih "Monthly Newsletter"
   - **Recipients:** Pilih "Active Only"
4. Klik **"Preview"** untuk lihat tampilan
5. Klik **"Create & Send"**
6. Check semua checklist
7. Klik **"Send Campaign Now"**

🎉 Campaign terkirim ke semua active subscribers!

---

## 📖 Panduan Lengkap

### A. Email Inbox Management

**Path:** `/admin/inbox`

#### Fitur:
- ✉️ **Compose:** Kirim email baru
- 📧 **Inbox:** Email masuk
- 📤 **Sent:** Email terkirim
- ⭐ **Starred:** Email berbintang
- 🗑️ **Trash:** Email dihapus

#### Cara Pakai:
1. **Kirim Email Baru:**
   - Klik "Compose Email"
   - Isi to, subject, body
   - Klik "Send Email"

2. **Balas Email:**
   - Buka email dari list
   - Klik "Reply"
   - Tulis balasan
   - Klik "Send Reply"

3. **Organize Email:**
   - Klik ⭐ untuk star/unstar
   - Klik 🗑️ untuk trash
   - Gunakan search untuk cari

4. **Draft Auto-Save:**
   - Ketik email di compose
   - Klik "Save Draft"
   - Draft tersimpan otomatis di browser
   - Buka compose lagi untuk restore

---

### B. Email Campaign

**Path:** `/admin/campaigns`

#### Dashboard Stats:
- 📊 Total Campaigns
- 📝 Draft (belum kirim)
- ⏰ Scheduled (dijadwal)
- ✅ Sent (sudah terkirim)

#### Workflow Campaign:

**1. Create Campaign**
```
/admin/campaigns/create
├── Basic Info (name, subject)
├── Template (opsional)
├── Content (HTML editor)
├── Recipients (all/active/tags)
└── Schedule (now/later)
```

**2. Review & Edit** (untuk draft)
```
/admin/campaigns/{id}/edit
└── Ubah content, recipients, schedule
```

**3. Send Confirmation**
```
/admin/campaigns/{id}/send
├── Preview email
├── List recipients
├── Pre-send checklist
└── Confirm send
```

**4. Monitor Analytics**
```
/admin/campaigns/{id}
├── 📤 Sent Count
├── 📧 Open Rate (%)
├── 🖱️ Click Rate (%)
└── ⚠️ Bounce Rate (%)
```

#### Tips Campaign:
✅ **Subject Line:** Max 50 karakter, pakai emoji
✅ **Content:** Gunakan template yang sudah ada
✅ **Recipients:** Test dulu dengan "Tags: test"
✅ **Timing:** Kirim Selasa-Kamis jam 10-11 pagi
✅ **Preview:** Selalu preview sebelum kirim

---

### C. Subscriber Management

**Path:** `/admin/subscribers`

#### Stats:
- 👥 Total Subscribers
- ✅ Active
- 🔕 Unsubscribed
- ⚠️ Bounced

#### Cara Manage:

**1. Lihat List Subscribers**
- Filter by status
- Search by email/name
- Export (coming soon)

**2. Add Manual** (via database)
```sql
INSERT INTO email_subscribers (email, name, status, source, tags)
VALUES ('customer@example.com', 'John Doe', 'active', 'manual', '["customer", "vip"]');
```

**3. Edit Subscriber** (via database)
```sql
UPDATE email_subscribers
SET tags = '["customer", "premium"]', status = 'active'
WHERE email = 'customer@example.com';
```

**4. Tag Management**
Tags berguna untuk segmentasi:
- `customer` - Pelanggan aktif
- `prospect` - Potential customer
- `vip` - VIP customer
- `test` - Untuk testing

Kirim campaign ke tag tertentu:
```
Recipient Type: By Tags
Tags: customer,vip
```

---

### D. Email Templates

**Path:** `/admin/templates`

#### Template Tersedia:
1. **Welcome Email** - Onboarding subscriber baru
2. **Monthly Newsletter** - Update bulanan
3. **Promotion Email** - Promo dan diskon

#### Variable yang Tersedia:
- `{{name}}` - Nama subscriber
- `{{email}}` - Email subscriber
- `{{unsubscribe_url}}` - Link unsubscribe

#### Cara Pakai Template:
1. Buka `/admin/campaigns/create`
2. Pilih template di dropdown
3. Content otomatis terisi
4. Edit sesuai kebutuhan
5. Variable akan diganti otomatis saat kirim

#### Buat Template Baru (via database):
```sql
INSERT INTO email_templates (name, subject, content, category, is_active)
VALUES (
    'Custom Template',
    'Subject Line Here',
    '<html>Your HTML content with {{name}}</html>',
    'newsletter',
    true
);
```

---

## 🎨 Newsletter di Landing Page

Sudah terintegrasi di footer website!

**Lokasi:** `https://bizmark.id` → Scroll ke footer

**Fitur:**
- Form subscribe otomatis
- AJAX submission (no reload)
- Success/error message
- Auto-add ke database
- Google Analytics tracking

**Test:**
1. Buka homepage
2. Scroll ke footer
3. Isi email
4. Klik "Subscribe"
5. Cek di `/admin/subscribers`

---

## 📊 Analytics & Tracking

### Email Open Tracking
Otomatis via tracking pixel:
```
<img src="https://bizmark.id/email/track/{tracking_id}" width="1" height="1">
```

### Link Click Tracking
Redirect link via:
```
https://bizmark.id/email/click/{tracking_id}?url=...
```

### View Stats:
1. Buka campaign yang sudah sent
2. Lihat stats cards:
   - Sent count
   - Open rate (%)
   - Click rate (%)
   - Bounce rate (%)
3. Scroll ke bawah untuk delivery log per recipient

---

## 🔐 Security & Best Practices

### Email Sending:
✅ Rate limiting (1000 per hour)
✅ Bounce detection
✅ Unsubscribe link wajib
✅ DKIM/SPF verification (setup di domain)

### Privacy:
✅ Unsubscribe otomatis via link
✅ Data subscriber ter-encrypt
✅ No sharing to third party
✅ GDPR compliant

### Anti-Spam:
✅ Double opt-in (coming soon)
✅ Clean inactive subscribers
✅ Proper from/reply-to headers
✅ Valid unsubscribe link

---

## 🐛 Troubleshooting

### "Email tidak terkirim"
1. Check SMTP config di `.env`
2. Test credentials dengan:
   ```bash
   php artisan tinker
   Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
   ```
3. Check firewall port 587/465
4. Verify Gmail App Password

### "Campaign stuck di 'sending'"
1. Check Laravel log: `storage/logs/laravel.log`
2. Reset status:
   ```sql
   UPDATE email_campaigns SET status='draft' WHERE id=...;
   ```
3. Coba kirim ulang

### "Subscribers tidak muncul"
1. Check database:
   ```sql
   SELECT * FROM email_subscribers;
   ```
2. Verify migration:
   ```bash
   php artisan migrate:status
   ```

### "Template tidak load"
1. Clear view cache:
   ```bash
   php artisan view:clear
   ```
2. Check template aktif:
   ```sql
   UPDATE email_templates SET is_active=true;
   ```

---

## 📞 Support

**Dokumentasi Lengkap:**
- `EMAIL_SYSTEM_SETUP.md` - Setup detail
- `EMAIL_COMPLETE_SUMMARY.md` - Feature overview
- `EMAIL_FIX_SUMMARY.md` - Bug fixes log

**Butuh Help?**
- Check Laravel log: `/storage/logs/laravel.log`
- Database logs: `email_logs` table
- Browser console untuk AJAX errors

---

## 🎉 Success!

Sistem email marketing Bizmark.ID siap digunakan! 

**Next Steps:**
1. ✅ Konfigurasi SMTP
2. ✅ Test kirim email
3. ✅ Buat campaign pertama
4. ✅ Monitor analytics
5. 🚀 Scale up!

**Pro Tips:**
- Start dengan small test campaign (10-20 subscribers)
- Monitor bounce rate, jika >5% check list quality
- Clean unsubscribed/bounced setiap bulan
- A/B test subject lines untuk optimize open rate
- Send konsisten (mingguan/bulanan) untuk engagement

---

*Last Updated: November 13, 2025*
*Version: 1.0 - Production Ready* ✅
