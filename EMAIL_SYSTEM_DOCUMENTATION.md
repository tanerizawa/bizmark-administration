# 📧 Dokumentasi Sistem Email Bizmark.id

## ✅ Status Setup
**Email System Status: ACTIVE & FULLY OPERATIONAL**

- SMTP Server: `smtp-relay.brevo.com:587` (Brevo/Sendinblue)
- From Email: `noreply@bizmark.id`
- Queue Worker: Running (2 processes via Supervisor)
- Email Limit: 9,000 emails/bulan (Free Tier Brevo)
- Test Email: ✅ Berhasil terkirim

---

## 📋 Fitur Email System

### 1. **Email Campaign**
Kirim email massal ke subscribers dengan template kustom.

**Akses:** Dashboard Admin → Campaigns

**Fitur:**
- ✅ Bulk email ke semua subscribers atau filter berdasarkan tags
- ✅ Support HTML email dengan template editor
- ✅ Schedule campaign untuk dikirim nanti
- ✅ Track open rate, click rate, bounce rate
- ✅ Status tracking: Draft, Scheduled, Sending, Sent

**Cara Pakai:**
1. Buka `/admin/campaigns`
2. Klik "Create Campaign"
3. Pilih template atau buat konten baru
4. Pilih recipients (all/tags)
5. Schedule atau kirim sekarang
6. Monitor hasil via dashboard

---

### 2. **Email Templates**
Template yang bisa digunakan untuk campaign dan email otomatis.

**Akses:** Dashboard Admin → Templates

**Template Tersedia:**
- ✅ Monthly Newsletter - Newsletter bulanan
- ✅ Promotion Email - Email promo/diskon
- ✅ Welcome Email - Email selamat datang

**Variabel Template:**
- `{{month}}` - Nama bulan
- `{{year}}` - Tahun
- `{{name}}` - Nama subscriber
- `{{email}}` - Email subscriber
- Custom variables bisa ditambahkan

**Cara Buat Template Baru:**
1. Buka `/admin/templates/create`
2. Isi nama dan subject template
3. Design konten HTML
4. Tambahkan variabel dinamis dengan `{{variable}}`
5. Save template

---

### 3. **Email Subscribers**
Manage daftar email subscribers untuk newsletter.

**Akses:** Dashboard Admin → Subscribers

**Fitur:**
- ✅ Add subscribers manual atau via API
- ✅ Import subscribers dari CSV
- ✅ Tags/segmentation untuk targeting
- ✅ Status tracking: Active, Unsubscribed, Bounced
- ✅ Auto-unsubscribe link di setiap email

**Subscribe Methods:**

**A. Via Landing Page:**
```html
<!-- Form di landing page sudah tersedia -->
POST /subscribe
{
    "email": "user@example.com",
    "name": "John Doe"
}
```

**B. Via Dashboard:**
```
/admin/subscribers/create
```

**C. Via API:**
```bash
curl -X POST https://bizmark.id/subscribe \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","name":"John"}'
```

---

### 4. **Email Inbox**
Internal inbox untuk menerima dan membalas email.

**Akses:** Dashboard Admin → Inbox

**Fitur:**
- ✅ Receive emails
- ✅ Reply to emails
- ✅ Star important emails
- ✅ Move to trash
- ✅ Mark as read/unread
- ✅ Compose new email

**Cara Pakai:**
1. Buka `/admin/inbox`
2. View received emails
3. Klik email untuk read/reply
4. Compose untuk kirim email baru

---

### 5. **Email Tracking**
Track pembukaan dan klik email campaign.

**Fitur:**
- ✅ Open tracking (pixel tracking)
- ✅ Click tracking (link tracking)
- ✅ Analytics dashboard
- ✅ Per-campaign statistics
- ✅ Per-subscriber history

**Metrics:**
- **Open Rate:** Berapa persen subscriber buka email
- **Click Rate:** Berapa persen klik link di email
- **Bounce Rate:** Email yang gagal terkirim
- **Unsubscribe Rate:** Yang unsubscribe setelah campaign

**Route Tracking:**
```
GET /email/track/{tracking_id} - Track email open
```

---

### 6. **Email Settings**
Konfigurasi SMTP dan email preferences.

**Akses:** Dashboard Admin → Email Settings

**Konfigurasi Saat Ini:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=9b8609001@smtp-brevo.com
MAIL_PASSWORD=xsmtpsib-***
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@bizmark.id"
MAIL_FROM_NAME="Bizmark"
```

**Test Email:**
```
POST /admin/email/settings/test
```

---

## 🚀 Cara Kirim Email

### 1. Kirim Email Biasa (Programmatic)

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Konten email', function($message) {
    $message->to('user@example.com')
            ->subject('Subject Email')
            ->from('noreply@bizmark.id', 'Bizmark Team');
});
```

### 2. Kirim dengan Template

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

Mail::to('user@example.com')->send(new WelcomeMail($data));
```

### 3. Kirim Campaign

```php
use App\Models\EmailCampaign;
use App\Jobs\SendCampaignEmail;

$campaign = EmailCampaign::find(1);
$subscribers = EmailSubscriber::active()->get();

foreach($subscribers as $subscriber) {
    SendCampaignEmail::dispatch($campaign, $subscriber);
}
```

---

## ⚙️ Queue Worker

Email dikirim via queue untuk performa lebih baik.

**Status:** ✅ Running (2 processes)

**Supervisor Config:** `/etc/supervisor/conf.d/bizmark-worker.conf`

**Commands:**
```bash
# Check status
supervisorctl status bizmark-worker:*

# Restart worker
supervisorctl restart bizmark-worker:*

# Stop worker
supervisorctl stop bizmark-worker:*

# View logs
tail -f /home/bizmark/bizmark.id/storage/logs/worker.log
```

**Manual Queue Worker:**
```bash
cd /home/bizmark/bizmark.id
php artisan queue:work --sleep=3 --tries=3
```

---

## 📊 Monitoring & Logs

### Email Logs
Semua email yang terkirim dicatat di `email_logs` table.

**Query Logs:**
```php
use App\Models\EmailLog;

// Recent sent emails
$logs = EmailLog::where('status', 'sent')
               ->orderBy('sent_at', 'desc')
               ->take(100)
               ->get();

// Campaign stats
$campaign = EmailCampaign::find(1);
echo "Sent: " . $campaign->sent_count;
echo "Opens: " . $campaign->opened_count;
echo "Clicks: " . $campaign->clicked_count;
```

### Laravel Logs
```bash
tail -f /home/bizmark/bizmark.id/storage/logs/laravel.log
```

### Worker Logs
```bash
tail -f /home/bizmark/bizmark.id/storage/logs/worker.log
```

---

## 🔧 Troubleshooting

### Email Tidak Terkirim

**1. Cek Queue Worker:**
```bash
supervisorctl status
```

**2. Cek Failed Jobs:**
```bash
php artisan queue:failed
php artisan queue:retry all
```

**3. Cek Email Logs:**
```bash
tail -f storage/logs/laravel.log | grep -i mail
```

**4. Test SMTP Connection:**
```bash
php artisan tinker
Mail::raw('Test', function($m) { 
    $m->to('test@example.com')->subject('Test'); 
});
```

### Queue Macet

**Restart Worker:**
```bash
supervisorctl restart bizmark-worker:*
```

**Clear Queue:**
```bash
php artisan queue:flush
```

### Email Masuk Spam

**Solusi:**
1. ✅ Verifikasi domain di Brevo (bizmark.id)
2. ✅ Setup SPF record di DNS
3. ✅ Setup DKIM di DNS
4. ✅ Setup DMARC policy
5. ✅ Warm up IP (kirim sedikit dulu, naikkan perlahan)
6. ✅ Hindari spam words di subject

---

## 📈 Best Practices

### 1. Email Content
- ✅ Gunakan plain text alternative
- ✅ Avoid excessive images
- ✅ Test di multiple clients (Gmail, Outlook, dll)
- ✅ Responsive design untuk mobile
- ✅ Clear call-to-action

### 2. Campaign Management
- ✅ Segment subscribers by interest
- ✅ A/B test subject lines
- ✅ Optimal send time (avoid weekend/midnight)
- ✅ Monitor metrics per campaign
- ✅ Clean inactive subscribers

### 3. Compliance
- ✅ Always include unsubscribe link
- ✅ Honor unsubscribe requests immediately
- ✅ Add physical address di footer
- ✅ Don't buy email lists
- ✅ Get explicit consent (GDPR)

---

## 🔐 Security

### Rate Limiting
```php
// Limit 60 emails per minute per user
RateLimiter::for('email', function ($job) {
    return Limit::perMinute(60)->by($job->user->id);
});
```

### Email Validation
```php
// Validasi email sebelum kirim
$validator = validator(['email' => $email], [
    'email' => 'required|email:rfc,dns|max:255'
]);

if ($validator->fails()) {
    // Invalid email
}
```

### Spam Prevention
- ✅ Captcha on subscription form
- ✅ Double opt-in untuk subscribers
- ✅ Rate limiting on subscribe endpoint
- ✅ Honeypot fields

---

## 📱 API Endpoints

### Subscribe Newsletter
```bash
POST /subscribe
Content-Type: application/json

{
    "email": "user@example.com",
    "name": "John Doe"
}

# Response
{
    "success": true,
    "message": "Successfully subscribed to newsletter"
}
```

### Unsubscribe
```bash
GET /unsubscribe/{email}/{token}

# Response
{
    "success": true,
    "message": "Successfully unsubscribed"
}
```

### Track Email Open
```bash
GET /email/track/{tracking_id}

# Returns 1x1 transparent pixel
```

---

## 💡 Tips & Tricks

### 1. Test Email Rendering
Gunakan Litmus atau Email on Acid untuk test rendering di berbagai email client.

### 2. Email Subject Lines
- Maksimal 50 karakter
- Personalisasi dengan nama
- Hindari CAPS dan excessive punctuation!!!
- Test dengan emoji 🎉 (optional)

### 3. Optimal Send Time
- **B2B:** Selasa-Kamis, jam 10:00 atau 14:00
- **B2C:** Sabtu pagi atau Minggu sore
- Monitor analytics untuk audience spesifik

### 4. Improve Open Rates
- Personalized subject lines (+26% open rate)
- Segment by interest (+20% engagement)
- A/B test everything
- Clean list regularly (remove inactive >6 bulan)

---

## 📞 Support

**Email System Support:**
- Technical Issue: Check logs di `/storage/logs/`
- SMTP Issues: Check Brevo dashboard
- Queue Issues: Restart supervisor worker

**Brevo Account:**
- Dashboard: https://app.brevo.com/
- Login: 9b8609001@smtp-brevo.com
- API Keys: https://app.brevo.com/settings/keys/api

**Documentation:**
- Laravel Mail: https://laravel.com/docs/mail
- Brevo API: https://developers.brevo.com/
- Queue: https://laravel.com/docs/queues

---

## ✅ Checklist Sebelum Production

- [x] SMTP credentials configured
- [x] Test email berhasil terkirim
- [x] Queue worker running dengan Supervisor
- [x] Email templates created
- [x] Subscriber management tested
- [x] Campaign system tested
- [x] Tracking system active
- [ ] Domain verified di Brevo (optional tapi recommended)
- [ ] SPF/DKIM/DMARC records setup (optional)
- [ ] Monitor email logs daily
- [ ] Setup email alerts untuk failed jobs

---

## 🎉 Summary

**Email System Bizmark.id sudah:**
✅ Fully configured dengan Brevo SMTP
✅ Queue worker running background
✅ Campaign management ready
✅ Template system ready
✅ Subscriber management ready
✅ Email tracking ready
✅ Test email berhasil terkirim

**Siap untuk:**
- Kirim newsletter bulanan
- Email campaign promo
- Transactional emails (password reset, notifikasi, dll)
- Auto-responder welcome series
- Email marketing automation

**Kapasitas:**
- 9,000 emails/bulan (Brevo free tier)
- 300 emails/hari average
- Bisa upgrade kapan saja jika butuh lebih

---

**Setup Complete! 🎊**

Email system Bizmark.id sudah siap digunakan untuk mendukung marketing dan komunikasi dengan pelanggan!
