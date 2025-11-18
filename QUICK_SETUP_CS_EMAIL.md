# 🚀 Quick Setup: cs@bizmark.id - Email 2-Arah

## ✅ Status
**Backend Laravel:** ✅ SIAP
**Webhook Endpoint:** ✅ AKTIF (https://bizmark.id/webhook/email/receive)
**SMTP Sending:** ✅ SIAP (Brevo)

---

## 📋 Setup Steps (15 menit)

### Step 1: Login ke Cloudflare (2 menit)

1. **Go to:** https://dash.cloudflare.com/
2. **Login** dengan akun Cloudflare Anda
3. **Pilih domain:** `bizmark.id`

---

### Step 2: Enable Email Routing (3 menit)

1. **Di sidebar kiri, klik:** `Email` → `Email Routing`

2. **Klik:** `Get Started` atau `Enable Email Routing`

3. **Cloudflare akan auto-add DNS records:**
   ```
   Type: MX
   Name: @
   Value: route1.mx.cloudflare.net
   Priority: 1
   
   Type: MX  
   Name: @
   Value: route2.mx.cloudflare.net
   Priority: 2
   
   Type: TXT
   Name: @
   Value: v=spf1 include:_spf.mx.cloudflare.net ~all
   ```

4. **Klik:** `Add records and enable`

5. **Tunggu 2-5 menit** untuk DNS propagation

---

### Step 3: Create Cloudflare Worker (5 menit)

1. **Di sidebar, klik:** `Workers & Pages`

2. **Klik:** `Create` → `Create Worker`

3. **Name:** `bizmark-email-handler`

4. **Klik:** `Deploy` (deploy dulu, edit nanti)

5. **Setelah deploy, klik:** `Edit Code`

6. **Hapus semua code, replace dengan:**

```javascript
export default {
  async email(message, env, ctx) {
    const webhookUrl = 'https://bizmark.id/webhook/email/receive';
    
    try {
      const textContent = await message.text();
      const htmlContent = await message.html();
      
      const emailData = {
        message_id: message.headers.get('message-id'),
        from: message.from,
        to: message.to,
        subject: message.headers.get('subject'),
        text: textContent,
        html: htmlContent,
        date: message.headers.get('date'),
      };

      const response = await fetch(webhookUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(emailData)
      });

      console.log('Email forwarded:', response.ok ? 'Success' : 'Failed');
    } catch (error) {
      console.error('Worker error:', error);
    }
  }
}
```

7. **Klik:** `Save and Deploy`

---

### Step 4: Create Email Address (3 menit)

1. **Kembali ke:** `Email` → `Email Routing` → `Destination addresses`

2. **Klik:** `Create address`

3. **Email address:** `cs@bizmark.id`

4. **Action:** Pilih `Send to a Worker`

5. **Select Worker:** `bizmark-email-handler`

6. **Klik:** `Save`

---

### Step 5: Test Email (2 menit)

**Test Receive:**

1. Dari Gmail/email pribadi Anda, kirim email ke: **cs@bizmark.id**
   - Subject: "Test dari [nama Anda]"
   - Body: "Halo, ini test email"

2. **Tunggu 10-30 detik**

3. **Buka dashboard:** https://bizmark.id/admin/inbox

4. **Cek apakah email masuk**

**Test Send/Reply:**

1. Buka email yang baru masuk di dashboard
2. Klik tombol **Reply**
3. Tulis reply message
4. Klik **Send**
5. **Check inbox Gmail Anda** - should receive reply from cs@bizmark.id

---

## ✅ Verification Checklist

Setelah setup, verify:

- [ ] Cloudflare Email Routing enabled
- [ ] MX records added (check DNS)
- [ ] Worker deployed dan aktif
- [ ] Email address cs@bizmark.id created
- [ ] Worker connected to cs@bizmark.id
- [ ] Test email dikirim
- [ ] Email muncul di https://bizmark.id/admin/inbox
- [ ] Bisa reply dan customer terima dari cs@bizmark.id

---

## 🧪 Test Commands

**Test 1: Webhook Status**
```bash
curl https://bizmark.id/webhook/email/status
```

**Test 2: Send Dummy Email via Webhook**
```bash
curl -X POST https://bizmark.id/webhook/email/test \
  -H "Content-Type: application/json"
```

**Test 3: Check Database**
```bash
cd /home/bizmark/bizmark.id
php artisan tinker --execute="
echo 'Total Inbox: ' . \App\Models\EmailInbox::where('category', 'inbox')->count() . PHP_EOL;
echo 'Latest Email: ' . PHP_EOL;
\$latest = \App\Models\EmailInbox::latest()->first();
if (\$latest) {
    echo '  From: ' . \$latest->from_email . PHP_EOL;
    echo '  Subject: ' . \$latest->subject . PHP_EOL;
    echo '  Date: ' . \$latest->received_at . PHP_EOL;
}
"
```

**Test 4: View Logs**
```bash
tail -f /home/bizmark/bizmark.id/storage/logs/laravel.log | grep -i "email webhook"
```

---

## 🔧 Troubleshooting

### Email Tidak Masuk ke Inbox

**1. Check Cloudflare Worker Logs:**
- Go to: Workers & Pages → bizmark-email-handler → Logs
- Lihat apakah ada error

**2. Check Laravel Logs:**
```bash
tail -50 /home/bizmark/bizmark.id/storage/logs/laravel.log | grep "webhook"
```

**3. Test Webhook Manual:**
```bash
curl -X POST https://bizmark.id/webhook/email/receive \
  -H "Content-Type: application/json" \
  -d '{
    "from": "Test <test@example.com>",
    "to": "cs@bizmark.id",
    "subject": "Manual Test",
    "text": "Test body",
    "html": "<p>Test body</p>"
  }'
```

### Email Masuk tapi Tidak Muncul di Dashboard

**Check database:**
```bash
psql -U hadez -d bizmark_db -c "SELECT id, from_email, subject, category, received_at FROM email_inbox ORDER BY received_at DESC LIMIT 5;"
```

### Worker Error "fetch failed"

**Kemungkinan:**
- URL webhook salah (check: `https://bizmark.id/webhook/email/receive`)
- SSL certificate issue
- Firewall blocking Cloudflare IPs

**Fix:** Update worker dengan error handling lebih baik

---

## 📧 Add More Email Addresses

Untuk menambah email lain (sales@, support@, info@):

1. **Go to:** Email Routing → Destination addresses
2. **Create address:** `sales@bizmark.id`
3. **Action:** Send to Worker → `bizmark-email-handler`
4. **Save**

Semua email akan masuk ke same inbox, bisa filter by `to_email` di dashboard.

---

## 🎯 Next Features (Optional)

Setelah basic setup jalan, tambahkan:

### 1. Auto-Reply untuk Off-Hours
```php
// EmailWebhookController.php - di method receive()
if (now()->hour < 9 || now()->hour > 17) {
    // Send auto-reply
    Mail::raw('Terima kasih. Kami akan balas dalam 1x24 jam.', 
        fn($m) => $m->to($fromEmail)->subject('Re: ' . $subject));
}
```

### 2. Email Categories/Labels
```php
// Auto-categorize based on subject/content
if (str_contains(strtolower($subject), 'invoice')) {
    $inbox->category = 'billing';
} elseif (str_contains(strtolower($subject), 'support')) {
    $inbox->category = 'support';
}
```

### 3. Email Notifications
```php
// Notify admin via Telegram/Slack when new email
Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
    ->notify(new NewEmailReceived($inbox));
```

### 4. Canned Responses
Buat template responses untuk pertanyaan umum:
- "Bagaimana cara daftar?"
- "Berapa biaya konsultasi?"
- "Status perizinan saya?"

### 5. Email Signature
Auto-add signature ke setiap reply:
```
--
Best regards,
Customer Service Team
Bizmark.id - Solusi Perizinan Usaha
📧 cs@bizmark.id
🌐 https://bizmark.id
📱 +62xxx
```

---

## 📊 Monitoring

**Daily Check:**
```bash
cd /home/bizmark/bizmark.id
php artisan tinker --execute="
echo '📧 Email Stats (Last 24h)' . PHP_EOL;
echo '========================' . PHP_EOL;
echo 'Received: ' . \App\Models\EmailInbox::where('category', 'inbox')->whereDate('received_at', '>=', now()->subDay())->count() . PHP_EOL;
echo 'Sent: ' . \App\Models\EmailInbox::where('category', 'sent')->whereDate('received_at', '>=', now()->subDay())->count() . PHP_EOL;
echo 'Unread: ' . \App\Models\EmailInbox::where('is_read', false)->count() . PHP_EOL;
"
```

---

## 💡 Pro Tips

1. **Forward Important Emails:** Setup rule di Cloudflare untuk auto-forward email penting ke admin personal email

2. **Backup Email:** Worker bisa forward ke 2 tempat (webhook + email backup)

3. **Spam Prevention:** Cloudflare Email Routing sudah include spam filtering

4. **Multiple Inboxes:** Buat inbox terpisah per department:
   - cs@bizmark.id → Customer Service team
   - sales@bizmark.id → Sales team  
   - cs@bizmark.id → Technical Support

5. **SLA Tracking:** Track response time per email untuk improve customer service

---

## 📱 Mobile Access

Dashboard `/admin/inbox` sudah responsive, bisa diakses dari mobile untuk:
- Read emails
- Reply to customers
- Mark as read/starred
- Quick responses

---

**Setup time:** ~15 minutes  
**Cost:** FREE (Cloudflare Email Routing + Laravel webhook)  
**Maintenance:** Zero (fully automated)  

**Ready to setup?** Follow Step 1-5 above! 🚀
