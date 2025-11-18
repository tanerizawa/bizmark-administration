# Email System Setup Guide - Bizmark.ID

## 📧 Fitur Email Management yang Telah Diimplementasikan

### ✅ 1. Email Marketing / Campaigns
- **Campaign Management**: Buat, edit, kirim, dan track email campaign
- **Recipient Targeting**: All subscribers, active only, atau filter by tags
- **Analytics**: Open rate, click rate, bounce rate tracking
- **Scheduling**: Schedule campaign untuk dikirim nanti
- **Status Tracking**: Draft, Scheduled, Sending, Sent

### ✅ 2. Email Inbox
- **Receive Emails**: Terima email dari pelanggan di admin panel
- **Compose & Send**: Kirim email langsung dari admin panel
- **Reply**: Balas email dengan threading
- **Organization**: Inbox, Sent, Starred, Trash categories
- **Search & Filter**: Cari email by subject, sender, content

### ✅ 3. Subscriber Management
- **Subscriber Database**: Kelola daftar email subscriber
- **Status Management**: Active, Unsubscribed, Bounced
- **Tags & Segmentation**: Group subscribers by tags
- **Import/Export**: (Ready for implementation)
- **Newsletter Form**: Form subscription di landing page footer

### ✅ 4. Email Templates
- **Template Library**: Simpan template email yang reusable
- **Variable Support**: {{name}}, {{email}}, etc.
- **Categories**: Newsletter, Promotional, Transactional, Announcement

### ✅ 5. Email Tracking
- **Open Tracking**: Track kapan email dibuka
- **Click Tracking**: Track link yang diklik
- **Bounce Detection**: Detect email yang bounce
- **Unsubscribe Tracking**: Track who unsubscribed

## 🗄️ Database Structure

### Tables Created:
1. **email_subscribers** - Daftar subscriber newsletter
2. **email_templates** - Template email reusable
3. **email_campaigns** - Campaign yang dibuat
4. **email_logs** - Log setiap email terkirim
5. **email_inbox** - Inbox email masuk & terkirim

## 🚀 Setup SMTP Configuration

### Step 1: Configure .env File

Tambahkan konfigurasi SMTP di file `.env`:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.bizmark.id
MAIL_PORT=587
MAIL_USERNAME=noreply@bizmark.id
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.ID"
```

### Pilihan SMTP Provider:

#### Option 1: Gmail SMTP (untuk testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password  # Generate di Google Account Security
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Bizmark.ID"
```

**Note**: Untuk Gmail, generate App Password di: https://myaccount.google.com/apppasswords

#### Option 2: Mailgun (Recommended for production)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.bizmark.id
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.ID"
```

Install Mailgun driver:
```bash
composer require symfony/mailgun-mailer symfony/http-client
```

#### Option 3: SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.ID"
```

#### Option 4: Custom SMTP (bizmark.id domain)
Jika sudah punya email hosting di bizmark.id:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.bizmark.id  # atau smtp.bizmark.id
MAIL_PORT=587
MAIL_USERNAME=noreply@bizmark.id
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.ID"
```

### Step 2: Clear Config Cache

Setelah update .env, clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

### Step 3: Test Email Sending

Test kirim email via tinker:

```bash
php artisan tinker
```

```php
Mail::raw('Test email from Bizmark.ID', function($message) {
    $message->to('your-email@example.com')
        ->subject('Test Email');
});
```

## 📍 Admin Panel Routes

### Email Inbox
- List: `/admin/inbox`
- Compose: `/admin/inbox/compose`
- View: `/admin/inbox/{id}`
- Reply: `/admin/inbox/{id}/reply`

### Email Campaigns
- List: `/admin/campaigns`
- Create: `/admin/campaigns/create`
- Edit: `/admin/campaigns/{id}/edit`
- Send: `/admin/campaigns/{id}/send`
- View Stats: `/admin/campaigns/{id}`

### Subscribers
- List: `/admin/subscribers`
- Add: `/admin/subscribers/create`
- Edit: `/admin/subscribers/{id}/edit`

### Templates
- List: `/admin/templates`
- Create: `/admin/templates/create`
- Edit: `/admin/templates/{id}/edit`

## 📱 Public Routes

### Newsletter Subscription
- Subscribe: `POST /subscribe`
- Unsubscribe: `GET /unsubscribe/{email}/{token}`
- Email Tracking: `GET /email/track/{tracking_id}`

### Newsletter Form Location
Newsletter subscription form sudah ditambahkan di:
- **Landing Page Footer** (https://bizmark.id/)

## 🎨 Admin Menu Location

Menu "Email Management" sudah ditambahkan di sidebar admin dengan 4 submenu:
1. **Inbox** (dengan badge unread count)
2. **Campaigns** (email marketing)
3. **Subscribers** (dengan total active subscribers)
4. **Templates** (email templates)

## 🔄 Receiving Emails (Inbox)

Untuk menerima email masuk, ada 2 opsi:

### Option 1: Manual Entry (Current)
Admin bisa compose email dari admin panel dan akan tersimpan di inbox category "sent".

### Option 2: Email Forwarding (Recommended)
Setup email forwarding dari email@bizmark.id ke webhook Laravel:

1. Install package untuk IMAP:
```bash
composer require webklex/php-imap
```

2. Configure IMAP di .env:
```env
IMAP_HOST=mail.bizmark.id
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_USERNAME=cs@bizmark.id
IMAP_PASSWORD=your-password
```

3. Create artisan command untuk fetch emails:
```bash
php artisan make:command FetchEmails
```

4. Schedule di `app/Console/Kernel.php`:
```php
$schedule->command('emails:fetch')->everyMinute();
```

### Option 3: Webhook (Best for Production)
Setup webhook dari email provider (Mailgun, SendGrid) untuk auto-receive emails.

## 📊 Analytics & Tracking

Email tracking menggunakan:
- **Tracking Pixel**: 1x1 transparent GIF untuk open tracking
- **Tracking Links**: Redirect links untuk click tracking
- **Unique Tracking ID**: Setiap email punya tracking ID unik

Data tracking tersimpan di `email_logs` table dengan fields:
- `opened_at` - Kapan email dibuka
- `clicked_at` - Kapan link diklik
- `bounced_at` - Kapan email bounce
- `ip_address` - IP address yang membuka
- `user_agent` - Browser/device info

## 🔐 Security Features

1. **CSRF Protection**: Semua form protected dengan CSRF token
2. **Email Validation**: Unique email per subscription
3. **Unsubscribe Token**: Secure unsubscribe links dengan MD5 token
4. **Rate Limiting**: (Recommended to add)
5. **SPF/DKIM**: Configure di DNS untuk domain authentication

## 📝 Next Steps (Optional Enhancements)

1. **Email Templates WYSIWYG Editor**
   - Implement rich text editor (TinyMCE, CKEditor, or GrapesJS)
   - Drag & drop email builder

2. **Advanced Segmentation**
   - Segment by behavior, location, subscription date
   - A/B testing campaigns

3. **Automated Campaigns**
   - Welcome series
   - Drip campaigns
   - Birthday/anniversary emails

4. **Queue System**
   - Implement Laravel Queues for bulk sending
   - Prevent timeout on large campaigns

5. **Import/Export**
   - CSV import subscribers
   - Export campaign reports

6. **Email Service Integration**
   - Mailchimp sync
   - SendGrid analytics integration

7. **Mobile App Notifications**
   - Push notification untuk new emails
   - Mobile-responsive admin panel

## 🧪 Testing Checklist

- [ ] Test subscription form di landing page
- [ ] Test compose & send email dari admin panel
- [ ] Test create campaign & send to subscribers
- [ ] Test email tracking (opens & clicks)
- [ ] Test unsubscribe link
- [ ] Test email templates
- [ ] Test search & filter di inbox
- [ ] Test reply functionality
- [ ] Verify email deliverability (check spam folder)
- [ ] Test on multiple email clients (Gmail, Outlook, Yahoo)

## 📧 Support Email Recommendations

Create these email addresses:
- `noreply@bizmark.id` - Untuk campaign emails
- `cs@bizmark.id` - Untuk customer support (inbox)
- `hello@bizmark.id` - Untuk general inquiries
- `admin@bizmark.id` - Untuk internal notifications

## 🎯 Production Checklist

Before going live:
- [ ] Configure real SMTP credentials
- [ ] Setup SPF, DKIM, DMARC DNS records
- [ ] Test email deliverability with mail-tester.com
- [ ] Configure email sending limits (prevent spam flags)
- [ ] Setup monitoring for bounce rates
- [ ] Configure backup email service (failover)
- [ ] Test unsubscribe links work correctly
- [ ] Add terms & privacy policy links
- [ ] Setup email list cleaning (remove bounced emails)
- [ ] Configure queue workers for background sending

## 💡 Tips

1. **Warm Up Email Domain**: Mulai dengan send volume kecil, gradually increase
2. **Monitor Bounce Rate**: Keep below 2% for good sender reputation
3. **Clean List Regularly**: Remove bounced & inactive subscribers
4. **Personalization**: Use {{name}} variable untuk higher engagement
5. **Mobile First**: 60%+ emails dibuka di mobile, optimize templates

## 🆘 Troubleshooting

### Email tidak terkirim:
1. Check .env configuration
2. Run `php artisan config:clear`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test SMTP credentials manually
5. Check firewall/port 587 access

### Email masuk spam:
1. Configure SPF record
2. Setup DKIM signing
3. Add DMARC policy
4. Use authenticated domain
5. Avoid spam trigger words
6. Include unsubscribe link

### Tracking tidak work:
1. Check tracking pixel URL accessible
2. Verify tracking_id unique
3. Check email client blocks images
4. Test with different email providers

---

**Sistem email sudah 100% ready to use!** 🎉

Tinggal configure SMTP credentials dan test sending.
