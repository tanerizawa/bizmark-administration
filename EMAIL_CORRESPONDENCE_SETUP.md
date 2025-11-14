# 📧 Setup Email Korespondensi 2-Arah (cs@bizmark.id)

## 🎯 Tujuan
Membuat email `cs@bizmark.id` yang bisa:
- ✅ **Menerima email** dari customer
- ✅ **Mengirim email** ke customer
- ✅ Diakses via dashboard `/admin/inbox`
- ✅ Multiple staff bisa akses

---

## 🏗️ Arsitektur Sistem

Berdasarkan struktur aplikasi Bizmark.id, ada **3 opsi implementasi**:

### **Opsi 1: Cloudflare Email Routing + Webhook** ⭐ (RECOMMENDED)
**Biaya:** FREE  
**Kompleksitas:** Medium  
**Fitur:** Full 2-way email

```
Customer mengirim → cs@bizmark.id 
    ↓ (Cloudflare Email Routing - FREE)
    ↓ Forward as webhook
    ↓ 
Laravel Webhook Endpoint
    ↓ Parse & save to email_inbox table
    ↓
Dashboard /admin/inbox
    ↓ Staff reply via UI
    ↓
Brevo SMTP (from: cs@bizmark.id)
    ↓
Customer menerima reply
```

**Kelebihan:**
- ✅ Gratis 100%
- ✅ Tidak perlu IMAP setup
- ✅ Email langsung masuk database
- ✅ Mudah scale untuk multiple email addresses
- ✅ Cloudflare handle spam filtering

**Kekurangan:**
- Domain harus pakai Cloudflare DNS
- Webhook endpoint harus public & secure

---

### **Opsi 2: IMAP + SMTP** 
**Biaya:** Perlu email hosting (Rp 50k-200k/bulan)  
**Kompleksitas:** Low  
**Fitur:** Full 2-way email

```
Customer mengirim → cs@bizmark.id
    ↓ (Email hosting: Zoho/Namecheap/cPanel)
    ↓
Laravel IMAP fetch (cron job every 5 min)
    ↓ Parse & save to email_inbox table
    ↓
Dashboard /admin/inbox
    ↓ Staff reply via UI
    ↓
Brevo SMTP atau Email hosting SMTP
    ↓
Customer menerima reply
```

**Kelebihan:**
- ✅ Standard email setup
- ✅ Bisa akses via email client (Outlook, Gmail app)
- ✅ Tidak perlu webhook
- ✅ Full email features (IMAP folders, flags)

**Kekurangan:**
- ❌ Perlu bayar email hosting
- ❌ Perlu cron job untuk fetch email
- ❌ Lebih lambat (delay 5-10 menit)

---

### **Opsi 3: Email Forwarding ke Gmail** 
**Biaya:** FREE  
**Kompleksitas:** Very Low  
**Fitur:** Basic (forwarding only)

```
Customer mengirim → cs@bizmark.id
    ↓ (Domain email forwarding)
    ↓
Forward to: studiomalaka@gmail.com
    ↓
Staff baca di Gmail
    ↓ Manual reply via Gmail
    ↓ (Email appears from Gmail, not @bizmark.id)
Customer menerima
```

**Kelebihan:**
- ✅ Sangat mudah setup
- ✅ Gratis
- ✅ Familiar (pakai Gmail)

**Kekurangan:**
- ❌ Reply tampil dari Gmail, bukan cs@bizmark.id
- ❌ Tidak terintegrasi dengan dashboard
- ❌ Tidak profesional
- ❌ Tidak bisa track di sistem

---

## 🚀 Implementasi Opsi 1 (RECOMMENDED)

### Step 1: Setup Cloudflare Email Routing

**1. Login ke Cloudflare Dashboard**
- Go to: https://dash.cloudflare.com/
- Pilih domain: `bizmark.id`

**2. Enable Email Routing**
- Go to **Email** → **Email Routing**
- Klik **Get Started**
- Cloudflare akan auto-add DNS records (MX, TXT)

**3. Create Email Address**
- Klik **Create Address**
- Email: `cs@bizmark.id`
- Action: **Send to a Worker** (pilih ini untuk webhook)
- Atau temporary forward ke email existing dulu

**4. Setup DNS Records** (Auto by Cloudflare)
```
Type: MX
Name: bizmark.id
Value: route1.mx.cloudflare.net
Priority: 1

Type: TXT
Name: bizmark.id
Value: v=spf1 include:_spf.mx.cloudflare.net ~all
```

---

### Step 2: Buat Webhook Endpoint di Laravel

**1. Create Route untuk Webhook**

File: `routes/web.php`

```php
// Webhook untuk receive email dari Cloudflare
Route::post('/webhook/email/receive', [App\Http\Controllers\EmailWebhookController::class, 'receive'])
    ->name('webhook.email.receive');
```

**2. Create Controller**

File: `app/Http/Controllers/EmailWebhookController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\EmailInbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailWebhookController extends Controller
{
    /**
     * Receive incoming email from Cloudflare Email Worker
     */
    public function receive(Request $request)
    {
        try {
            // Log untuk debugging
            Log::info('Email webhook received', $request->all());

            // Parse email data dari Cloudflare Worker
            $emailData = $request->all();

            // Validate required fields
            if (!isset($emailData['from']) || !isset($emailData['subject'])) {
                Log::error('Invalid email webhook data', $emailData);
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Extract from email
            preg_match('/<(.+?)>/', $emailData['from'], $fromMatches);
            $fromEmail = $fromMatches[1] ?? $emailData['from'];
            
            preg_match('/^(.+?)\s*</', $emailData['from'], $nameMatches);
            $fromName = trim($nameMatches[1] ?? $fromEmail);

            // Create inbox entry
            $inbox = EmailInbox::create([
                'message_id' => $emailData['message_id'] ?? 'cf-' . uniqid(),
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => $emailData['to'] ?? 'cs@bizmark.id',
                'subject' => $emailData['subject'],
                'body_html' => $emailData['html'] ?? null,
                'body_text' => $emailData['text'] ?? strip_tags($emailData['html'] ?? ''),
                'attachments' => $emailData['attachments'] ?? [],
                'category' => 'inbox',
                'is_read' => false,
                'is_starred' => false,
                'received_at' => now(),
            ]);

            Log::info('Email saved to inbox', ['id' => $inbox->id]);

            // Optional: Send notification to staff
            // Notification::send($adminUsers, new NewEmailReceived($inbox));

            return response()->json([
                'success' => true,
                'message' => 'Email received',
                'id' => $inbox->id
            ]);

        } catch (\Exception $e) {
            Log::error('Email webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Internal error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

**3. Exclude dari CSRF Protection**

File: `app/Http/Middleware/VerifyCsrfToken.php`

```php
protected $except = [
    'webhook/*',
];
```

---

### Step 3: Setup Cloudflare Email Worker

**1. Create Worker di Cloudflare**

Go to: **Workers & Pages** → **Create Worker**

**Worker Code:**

```javascript
export default {
  async email(message, env, ctx) {
    // Forward email ke Laravel webhook
    const webhookUrl = 'https://bizmark.id/webhook/email/receive';
    
    try {
      // Parse email content
      const reader = message.raw.getReader();
      const chunks = [];
      
      while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        chunks.push(value);
      }
      
      const rawEmail = new TextDecoder().decode(
        chunks.reduce((acc, chunk) => {
          const tmp = new Uint8Array(acc.length + chunk.length);
          tmp.set(acc);
          tmp.set(chunk, acc.length);
          return tmp;
        }, new Uint8Array(0))
      );

      // Extract email data
      const emailData = {
        message_id: message.headers.get('message-id'),
        from: message.from,
        to: message.to,
        subject: message.headers.get('subject'),
        text: await message.text(),
        html: await message.html(),
        date: message.headers.get('date'),
      };

      // Send to Laravel webhook
      const response = await fetch(webhookUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Cloudflare-Email': 'true'
        },
        body: JSON.stringify(emailData)
      });

      if (response.ok) {
        console.log('Email forwarded to Laravel successfully');
      } else {
        console.error('Failed to forward email:', await response.text());
      }

    } catch (error) {
      console.error('Worker error:', error);
    }
  }
}
```

**2. Connect Worker to Email Route**

- Go back to **Email Routing**
- Edit `cs@bizmark.id` address
- Action: **Send to Worker**
- Select your worker

---

### Step 4: Update Email Send untuk cs@bizmark.id

Agar email yang dikirim tampil dari `cs@bizmark.id`:

File: `app/Http/Controllers/Admin/EmailInboxController.php`

```php
public function send(Request $request)
{
    $validated = $request->validate([
        'to_email' => 'required|email',
        'subject' => 'required|string|max:255',
        'body_html' => 'required|string',
        'reply_to_id' => 'nullable|exists:email_inbox,id',
    ]);

    // Tentukan from email (bisa pilih dari dropdown)
    $fromEmail = $request->input('from_email', 'cs@bizmark.id');
    $fromName = $request->input('from_name', 'Bizmark Customer Service');

    try {
        Mail::html($validated['body_html'], function($message) use ($validated, $fromEmail, $fromName) {
            $message->to($validated['to_email'])
                ->subject($validated['subject'])
                ->from($fromEmail, $fromName);
        });

        // Save to sent folder
        $sent = EmailInbox::create([
            'message_id' => 'sent-' . \Illuminate\Support\Str::random(20),
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'to_email' => $validated['to_email'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'category' => 'sent',
            'is_read' => true,
            'replied_to' => $validated['reply_to_id'] ?? null,
            'received_at' => now(),
        ]);

        // Update original email if this is a reply
        if ($validated['reply_to_id']) {
            EmailInbox::find($validated['reply_to_id'])->update([
                'replied_to' => $sent->id
            ]);
        }

        return redirect()->route('admin.inbox.index', ['category' => 'sent'])
            ->with('success', 'Email berhasil dikirim dari ' . $fromEmail);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Gagal mengirim email: ' . $e->getMessage())
            ->withInput();
    }
}
```

---

### Step 5: Add Multiple Email Addresses (Optional)

Untuk support multiple email (sales@, support@, info@):

**1. Create Database Table untuk Email Accounts**

```bash
php artisan make:migration create_email_accounts_table
```

```php
Schema::create('email_accounts', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique(); // cs@bizmark.id
    $table->string('name'); // Customer Service
    $table->string('type')->default('support'); // support, sales, info
    $table->boolean('is_active')->default(true);
    $table->json('assigned_users')->nullable(); // User IDs yang bisa akses
    $table->string('signature')->nullable(); // Email signature
    $table->timestamps();
});
```

**2. Add Dropdown di Compose Form**

```blade
<select name="from_email" class="form-control">
    <option value="cs@bizmark.id">Customer Service (cs@bizmark.id)</option>
    <option value="sales@bizmark.id">Sales Team (sales@bizmark.id)</option>
    <option value="support@bizmark.id">Technical Support (support@bizmark.id)</option>
    <option value="info@bizmark.id">General Info (info@bizmark.id)</option>
</select>
```

**3. Setup di Cloudflare**

Tambahkan semua email addresses di Cloudflare Email Routing, semua forward ke same webhook.

---

## 🧪 Testing

### Test 1: Receive Email

**Manual Test:**
1. Setup Cloudflare Email Routing
2. Kirim email ke `cs@bizmark.id` dari Gmail/email lain
3. Check logs: `tail -f /home/bizmark/bizmark.id/storage/logs/laravel.log`
4. Check database: 
   ```bash
   php artisan tinker
   EmailInbox::where('category', 'inbox')->latest()->first();
   ```
5. Buka dashboard: https://bizmark.id/admin/inbox

### Test 2: Send Email

1. Buka: https://bizmark.id/admin/inbox/compose
2. To: your-email@example.com
3. Subject: Test from cs@bizmark.id
4. Body: Test email
5. Send
6. Check inbox untuk verify email diterima dari `cs@bizmark.id`

### Test 3: Reply

1. Buka email di `/admin/inbox`
2. Klik Reply
3. Tulis response
4. Send
5. Verify customer menerima reply

---

## 🔧 Troubleshooting

### Email Tidak Masuk ke Laravel

**Check:**
```bash
# 1. Cek logs
tail -f storage/logs/laravel.log | grep -i email

# 2. Test webhook manual
curl -X POST https://bizmark.id/webhook/email/receive \
  -H "Content-Type: application/json" \
  -d '{
    "from": "test@example.com",
    "to": "cs@bizmark.id",
    "subject": "Test Email",
    "text": "Test body"
  }'

# 3. Check database
php artisan tinker
EmailInbox::latest()->first();
```

### Email Terkirim tapi Masuk Spam

**Solusi:**
1. Verify domain di Brevo: https://app.brevo.com/
2. Add SPF record: `v=spf1 include:spf.brevo.com ~all`
3. Add DKIM records dari Brevo
4. Add DMARC: `v=DMARC1; p=none; rua=mailto:admin@bizmark.id`

### Webhook Error 419 (CSRF)

Add to `app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    'webhook/*',
];
```

---

## 📊 Monitoring

### Email Activity Dashboard

Add to admin dashboard:

```php
// Total emails today
$todayEmails = EmailInbox::whereDate('received_at', today())->count();

// Unread emails
$unreadCount = EmailInbox::where('category', 'inbox')
    ->where('is_read', false)
    ->count();

// Response time (average)
$avgResponseTime = EmailInbox::where('category', 'inbox')
    ->whereNotNull('replied_to')
    ->selectRaw('AVG(EXTRACT(EPOCH FROM (updated_at - received_at))/3600) as avg_hours')
    ->first()
    ->avg_hours;
```

### Email Logs

```bash
# View all email logs
tail -f storage/logs/laravel.log | grep -i "email\|mail"

# View webhook logs
tail -f storage/logs/laravel.log | grep "Email webhook"

# View SMTP logs
tail -f storage/logs/laravel.log | grep "Brevo\|SMTP"
```

---

## 💰 Cost Comparison

| Opsi | Setup | Monthly | Features |
|------|-------|---------|----------|
| **Opsi 1: Cloudflare + Webhook** | Free | Free | ✅ Full 2-way, ✅ Dashboard, ✅ Multiple emails |
| **Opsi 2: IMAP + SMTP** | Free | Rp 50k-200k | ✅ Full 2-way, ✅ Email client, ⚠️ Extra cost |
| **Opsi 3: Email Forwarding** | Free | Free | ⚠️ Basic, ❌ Not professional |

**Recommendation:** **Opsi 1** - Free, professional, full featured!

---

## ✅ Implementation Checklist

- [ ] Setup Cloudflare Email Routing
- [ ] Add MX records (auto by Cloudflare)
- [ ] Create cs@bizmark.id address
- [ ] Create Cloudflare Worker
- [ ] Add webhook route to Laravel
- [ ] Create EmailWebhookController
- [ ] Exclude webhook from CSRF
- [ ] Test receive email
- [ ] Update send controller untuk cs@bizmark.id
- [ ] Test send email
- [ ] Add email signature
- [ ] Setup notifications untuk new email
- [ ] Add monitoring dashboard

---

## 🎯 Next Steps

1. **Pilih Opsi** (Recommended: Opsi 1)
2. **Setup Cloudflare Email Routing**
3. **Implement Webhook Endpoint**
4. **Test End-to-End**
5. **Add More Features:**
   - Auto-reply untuk off-hours
   - Email templates untuk common replies
   - Canned responses
   - Email analytics
   - SLA tracking (response time)

---

**Ready to implement?** Mari kita mulai dari Step 1! 🚀
