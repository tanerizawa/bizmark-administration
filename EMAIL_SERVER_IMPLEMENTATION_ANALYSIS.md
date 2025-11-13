# 📧 Email Server Implementation Analysis - Bizmark.id

## 📊 Current Server Status

### Server Information
- **OS**: Debian 13 (Trixie) - 64bit
- **Kernel**: 6.12.48 (Cloud-optimized)
- **Hostname**: srv982494.hstgr.cloud
- **IP Address**: 31.97.222.208
- **IPv6**: 2a02:4780:59:b05f::1
- **RAM**: 7.8GB (2.4GB available)
- **Disk**: 99GB (54GB free)
- **Hosting**: Hostinger Cloud

### Current Email Configuration
- ❌ **No mail server installed** (Postfix, Exim, Sendmail)
- ❌ **No mail ports active** (25, 587, 465, 993, 143)
- ❌ **No MX records configured** for bizmark.id
- ❌ **No SPF records** configured
- ❌ **No DKIM/DMARC** configured
- ✅ **Laravel Mail**: Currently using `MAIL_MAILER=log` (file logging only)

### Current Laravel Email System
- ✅ Inbox Management
- ✅ Campaign Management
- ✅ Subscriber Management
- ✅ Template System
- ✅ Email Settings UI
- ⚠️ **NOT sending real emails** (using log driver)

---

## 🎯 Goal: Send Emails from @bizmark.id Domain

Anda ingin bisa:
- ✉️ Mengirim email dari `noreply@bizmark.id`
- 📧 Mengirim campaign dari `marketing@bizmark.id`
- 📮 Menerima email di `info@bizmark.id`
- 🔒 Professional email dengan domain sendiri

---

## 🔄 3 Implementation Options

### Option 1: Self-Hosted Mail Server (Postfix + Dovecot)

**Architecture:**
```
Internet ←→ [Port 25/587/465] ←→ Postfix (SMTP) ←→ Laravel
                                      ↓
                                  Dovecot (IMAP) ←→ Email Clients
                                      ↓
                                  PostgreSQL/Maildir
```

**Installation Steps:**

#### 1.1 Install Mail Server
```bash
# Update system
apt update && apt upgrade -y

# Install Postfix + Dovecot + Utilities
apt install -y postfix postfix-pgsql dovecot-core dovecot-imapd \
               dovecot-pop3d dovecot-lmtpd dovecot-pgsql \
               opendkim opendkim-tools spamassassin \
               certbot python3-certbot-nginx

# During installation:
# Postfix Configuration:
#   - General type: Internet Site
#   - System mail name: bizmark.id
```

#### 1.2 Configure Postfix
```bash
# Edit /etc/postfix/main.cf
nano /etc/postfix/main.cf
```

Add/modify:
```conf
# Basic Settings
myhostname = mail.bizmark.id
mydomain = bizmark.id
myorigin = $mydomain
inet_interfaces = all
inet_protocols = ipv4

# Mail Delivery
mydestination = $myhostname, localhost.$mydomain, localhost, $mydomain
home_mailbox = Maildir/

# SMTP Settings
smtpd_banner = $myhostname ESMTP $mail_name
smtpd_tls_cert_file = /etc/letsencrypt/live/bizmark.id/fullchain.pem
smtpd_tls_key_file = /etc/letsencrypt/live/bizmark.id/privkey.pem
smtpd_use_tls = yes
smtpd_tls_auth_only = yes
smtpd_tls_security_level = may
smtpd_tls_protocols = !SSLv2, !SSLv3, !TLSv1, !TLSv1.1
smtp_tls_security_level = may

# SASL Authentication
smtpd_sasl_type = dovecot
smtpd_sasl_path = private/auth
smtpd_sasl_auth_enable = yes
smtpd_sasl_security_options = noanonymous
smtpd_sasl_local_domain = $mydomain

# Anti-Spam
smtpd_recipient_restrictions =
    permit_sasl_authenticated,
    permit_mynetworks,
    reject_unauth_destination,
    reject_invalid_hostname,
    reject_non_fqdn_sender,
    reject_non_fqdn_recipient,
    reject_unknown_sender_domain,
    reject_unknown_recipient_domain,
    reject_rbl_client zen.spamhaus.org,
    reject_rbl_client bl.spamcop.net
    permit

# Virtual Alias (for Laravel)
virtual_alias_maps = hash:/etc/postfix/virtual
```

#### 1.3 Configure Virtual Aliases
```bash
nano /etc/postfix/virtual
```

Add:
```
noreply@bizmark.id      laravel@localhost
marketing@bizmark.id    laravel@localhost
info@bizmark.id         laravel@localhost
support@bizmark.id      laravel@localhost
```

```bash
postmap /etc/postfix/virtual
systemctl restart postfix
```

#### 1.4 Configure Dovecot
```bash
nano /etc/dovecot/dovecot.conf
```

```conf
protocols = imap pop3 lmtp
listen = *
```

```bash
nano /etc/dovecot/conf.d/10-mail.conf
```

```conf
mail_location = maildir:~/Maildir
mail_privileged_group = mail
```

```bash
nano /etc/dovecot/conf.d/10-auth.conf
```

```conf
disable_plaintext_auth = no
auth_mechanisms = plain login
```

```bash
# Restart Dovecot
systemctl restart dovecot
systemctl enable dovecot
```

#### 1.5 Setup DKIM (Email Authentication)
```bash
# Generate DKIM keys
mkdir -p /etc/opendkim/keys/bizmark.id
cd /etc/opendkim/keys/bizmark.id
opendkim-genkey -s mail -d bizmark.id
chown opendkim:opendkim mail.private

# Configure OpenDKIM
nano /etc/opendkim.conf
```

```conf
Domain                  bizmark.id
KeyFile                 /etc/opendkim/keys/bizmark.id/mail.private
Selector                mail
Socket                  inet:8891@localhost
```

```bash
systemctl restart opendkim
systemctl enable opendkim
```

#### 1.6 DNS Configuration Required

**Add these DNS records at your domain registrar:**

```dns
# MX Record (Mail Exchange)
bizmark.id.     IN  MX  10  mail.bizmark.id.

# A Record for mail subdomain
mail.bizmark.id.    IN  A   31.97.222.208

# SPF Record (Sender Policy Framework)
bizmark.id.     IN  TXT "v=spf1 mx a ip4:31.97.222.208 ~all"

# DKIM Record (get from /etc/opendkim/keys/bizmark.id/mail.txt)
mail._domainkey.bizmark.id. IN TXT "v=DKIM1; k=rsa; p=YOUR_PUBLIC_KEY_HERE"

# DMARC Record (Domain-based Message Authentication)
_dmarc.bizmark.id.  IN  TXT "v=DMARC1; p=quarantine; rua=mailto:dmarc@bizmark.id; ruf=mailto:dmarc@bizmark.id; fo=1"

# PTR Record (Reverse DNS - contact Hostinger support)
31.97.222.208.in-addr.arpa.  IN  PTR  mail.bizmark.id.
```

#### 1.7 Laravel Configuration
```env
# Update .env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=587
MAIL_USERNAME=noreply@bizmark.id
MAIL_PASSWORD=your_secure_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

#### 1.8 Create System User for Laravel
```bash
# Create email user
useradd -m -s /bin/bash laravel-mail
passwd laravel-mail

# Grant Laravel permission
usermod -aG mail www-data
```

#### 1.9 SSL Certificate
```bash
# Get SSL certificate for mail subdomain
certbot certonly --nginx -d mail.bizmark.id

# Reload Postfix with new cert
systemctl reload postfix
```

#### 1.10 Firewall Configuration
```bash
# Allow mail ports
ufw allow 25/tcp   # SMTP
ufw allow 587/tcp  # Submission (STARTTLS)
ufw allow 465/tcp  # SMTPS (SSL/TLS)
ufw allow 143/tcp  # IMAP
ufw allow 993/tcp  # IMAPS
ufw reload
```

#### 1.11 Testing
```bash
# Test SMTP connection
telnet localhost 25

# Test authentication
swaks --to test@example.com \
      --from noreply@bizmark.id \
      --server localhost \
      --port 587 \
      --auth LOGIN \
      --auth-user noreply@bizmark.id \
      --auth-password your_password

# Check mail queue
mailq

# Check logs
tail -f /var/log/mail.log
```

**✅ PROS:**
- Full control over email infrastructure
- No monthly costs for email service
- Unlimited email sending (within server limits)
- Complete privacy - no third-party access
- Can receive emails directly
- Custom email accounts (unlimited)
- Professional mail server

**❌ CONS:**
- Complex setup and maintenance (3-5 days initial setup)
- Requires DNS configuration expertise
- Must maintain email reputation (IP warmup required)
- Risk of being blacklisted if misconfigured
- Need to manage spam filtering
- Must monitor 24/7 for deliverability issues
- High risk of emails going to spam initially
- Requires dedicated IP with good reputation
- Must handle bounce management
- Regular security updates needed
- Resource intensive (RAM/CPU)
- Need to backup email data
- GDPR/compliance management

**⏱️ Time Estimate:** 3-5 days for full setup + ongoing maintenance

**💰 Cost:** Free (but time-intensive)

**🎯 Best For:** Large companies with dedicated DevOps team

---

### Option 2: Third-Party Transactional Email Service (RECOMMENDED) ⭐

**Architecture:**
```
Laravel ←→ [API/SMTP] ←→ Mailgun/SendGrid/SES ←→ Recipients
                              ↓
                        Dashboard Analytics
```

#### 2.1 Mailgun (Recommended for Europe)

**Setup:**
1. Sign up at https://mailgun.com
2. Verify domain bizmark.id
3. Add DNS records (provided by Mailgun):

```dns
# Mailgun DNS Records
bizmark.id.     IN  TXT "v=spf1 include:mailgun.org ~all"
pic._domainkey.bizmark.id.  IN  TXT "k=rsa; p=MAILGUN_PUBLIC_KEY"
email.bizmark.id.  IN  CNAME  mailgun.org.
```

4. Install Mailgun Laravel package:
```bash
cd /root/Bizmark.id
composer require mailgun/mailgun-php symfony/http-client
composer require symfony/mailgun-mailer
```

5. Update Laravel config:
```bash
# Add to config/services.php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'), // EU region
],
```

6. Update .env:
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.bizmark.id
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.eu.mailgun.net

MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

**Features:**
- ✅ 5,000 free emails/month (first 3 months)
- ✅ Then $35/month for 50,000 emails
- ✅ Advanced analytics dashboard
- ✅ Email validation API
- ✅ Bounce/complaint handling
- ✅ Email tracking (opens/clicks)
- ✅ A/B testing
- ✅ Template storage
- ✅ Webhook integration
- ✅ 99.99% uptime SLA
- ✅ EU data residency (GDPR compliant)

#### 2.2 SendGrid (Alternative)

```bash
composer require sendgrid/sendgrid
```

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

**Pricing:**
- Free: 100 emails/day
- Essentials: $19.95/month (50,000 emails)
- Pro: $89.95/month (100,000 emails)

#### 2.3 Amazon SES (Cheapest)

```bash
composer require aws/aws-sdk-php
```

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-central-1
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

**Pricing:**
- $0.10 per 1,000 emails
- $0.12 per GB attachment
- First 62,000 emails FREE if sent from EC2

**DNS Configuration (same for all services):**
```dns
# MX Record (if you want to receive emails)
bizmark.id.     IN  MX  10  mail.bizmark.id.

# SPF Record
bizmark.id.     IN  TXT "v=spf1 include:mailgun.org include:sendgrid.net include:amazonses.com ~all"
```

**✅ PROS:**
- ⚡ Quick setup (30 minutes)
- 🚀 High deliverability (99%+ inbox rate)
- 📊 Professional analytics dashboard
- 🔒 Managed infrastructure (no maintenance)
- ✉️ Reputation managed by provider
- 🌍 Global IP pools with warm IPs
- 📈 Scales automatically
- 🛡️ Built-in spam filtering
- 📞 Professional support
- 🔐 GDPR compliant
- 💰 Predictable costs
- 🎯 Focus on your app, not email infrastructure

**❌ CONS:**
- 💵 Monthly costs ($35-90/month for 50k emails)
- 🔗 Dependency on third-party service
- 📉 Can be more expensive at high volume
- ⚠️ Service outages (rare but possible)

**⏱️ Time Estimate:** 30 minutes setup

**💰 Cost:** 
- Mailgun: $35/month (50k emails)
- SendGrid: $19.95/month (50k emails)
- Amazon SES: ~$5/month (50k emails)

**🎯 Best For:** 99% of applications, especially SaaS products

---

### Option 3: cPanel/Plesk Email Hosting

**Check if Hostinger provides cPanel:**
```bash
# Check for cPanel/Plesk
ls -la /usr/local/cpanel 2>/dev/null || echo "cPanel not installed"
ls -la /usr/local/psa 2>/dev/null || echo "Plesk not installed"
```

If Hostinger VPS includes cPanel/Plesk:

1. Login to cPanel/Plesk
2. Go to Email Accounts
3. Create email accounts:
   - noreply@bizmark.id
   - marketing@bizmark.id
   - info@bizmark.id
4. Get SMTP credentials
5. Configure Laravel:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.bizmark.id
MAIL_PORT=587
MAIL_USERNAME=noreply@bizmark.id
MAIL_PASSWORD=cpanel_generated_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

**✅ PROS:**
- Easy setup via GUI
- Managed by hosting provider
- Email accounts included in hosting
- Webmail interface
- Spam filtering included

**❌ CONS:**
- Limited sending capacity (hourly limits)
- Shared IP reputation issues
- May have outgoing rate limits
- Not optimized for transactional emails
- Analytics limited

**⏱️ Time Estimate:** 1 hour

**💰 Cost:** Usually included in hosting

**🎯 Best For:** Small volume email (< 1000/day)

---

## 🏆 RECOMMENDED SOLUTION for Bizmark.id

### **Use Mailgun with Custom Domain** (Option 2.1)

**Why Mailgun?**

1. **Perfect for Laravel**: Native integration
2. **EU-based**: GDPR compliant (api.eu.mailgun.net)
3. **Affordable**: $35/month for 50,000 emails
4. **High deliverability**: 99%+ inbox placement
5. **Professional features**: Analytics, webhooks, validation
6. **Zero maintenance**: Focus on your business
7. **Scales with you**: Easy to upgrade
8. **Free trial**: Test before committing

**Implementation Plan:**

### Phase 1: Mailgun Setup (30 minutes)

```bash
# 1. Create Mailgun account
# Go to: https://signup.mailgun.com/

# 2. Add and verify domain
# Dashboard → Sending → Domains → Add New Domain
# Domain: mg.bizmark.id (subdomain recommended)

# 3. Add DNS records (provided by Mailgun)
# Copy all TXT, CNAME, MX records to your DNS provider

# 4. Install Mailgun package
cd /root/Bizmark.id
composer require mailgun/mailgun-php
composer require symfony/mailgun-mailer
composer require guzzlehttp/guzzle

# 5. Update config
nano config/services.php
```

Add to `config/services.php`:
```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
],
```

```bash
# 6. Update .env
nano .env
```

Add to `.env`:
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.bizmark.id
MAILGUN_SECRET=your-mailgun-api-key-here
MAILGUN_ENDPOINT=api.eu.mailgun.net

MAIL_FROM_ADDRESS=noreply@bizmark.id
MAIL_FROM_NAME="Bizmark.id"
```

```bash
# 7. Clear config cache
php artisan config:clear
php artisan cache:clear

# 8. Test sending
php artisan tinker
```

In Tinker:
```php
Mail::raw('Test email from Bizmark.id!', function ($message) {
    $message->to('your-personal-email@gmail.com')
            ->subject('Test Email');
});
```

### Phase 2: DNS Configuration (1 hour)

**Login to your DNS provider (Cloudflare/domain registrar) and add:**

```dns
# 1. MX Records (for receiving emails - optional)
bizmark.id.     300     IN  MX  10  mxa.eu.mailgun.org.
bizmark.id.     300     IN  MX  10  mxb.eu.mailgun.org.

# 2. TXT Records (from Mailgun dashboard)
bizmark.id.     300     IN  TXT "v=spf1 include:mailgun.org ~all"
mg.bizmark.id.  300     IN  TXT "v=spf1 include:mailgun.org ~all"

# 3. CNAME for tracking domain
email.bizmark.id.   300 IN  CNAME   mailgun.org.

# 4. DKIM Records (copy from Mailgun - will look like this)
pic._domainkey.bizmark.id.  300  IN  TXT "k=rsa; p=MIGfMA0GCS...very.long.key..."
```

**Wait 5-30 minutes for DNS propagation**, then verify in Mailgun dashboard.

### Phase 3: Laravel Integration (15 minutes)

Update `EmailSettingsController.php` to include Mailgun preset:

```bash
nano app/Http/Controllers/Admin/EmailSettingsController.php
```

Add Mailgun to the settings view (already done, just need to update form).

### Phase 4: Testing (15 minutes)

1. Go to `/admin/email/settings`
2. Select "Mailgun" as mail driver
3. Enter API key
4. Send test email
5. Check Mailgun dashboard for delivery stats

### Phase 5: Production Use

Now you can:
- ✅ Send campaigns from `marketing@bizmark.id`
- ✅ Send transactional emails from `noreply@bizmark.id`
- ✅ Track opens and clicks
- ✅ View bounce/complaint rates
- ✅ Export analytics
- ✅ Scale to millions of emails

---

## 📋 Quick Start: Mailgun Implementation

### Step-by-Step Commands

```bash
# 1. Install dependencies
cd /root/Bizmark.id
composer require mailgun/mailgun-php symfony/mailgun-mailer guzzlehttp/guzzle

# 2. Update config file
cat >> config/services.php << 'EOF'

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
    ],
EOF

# 3. Update .env (replace with your actual values)
sed -i 's/^MAIL_MAILER=.*/MAIL_MAILER=mailgun/' .env
echo "MAILGUN_DOMAIN=mg.bizmark.id" >> .env
echo "MAILGUN_SECRET=your-api-key-here" >> .env
echo "MAILGUN_ENDPOINT=api.eu.mailgun.net" >> .env

# Update from address
sed -i 's/^MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS="noreply@bizmark.id"/' .env
sed -i 's/^MAIL_FROM_NAME=.*/MAIL_FROM_NAME="Bizmark.id"/' .env

# 4. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Test email
php artisan tinker
# Then run: Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
```

---

## 📊 Comparison Table

| Feature | Self-Hosted | Mailgun | SendGrid | Amazon SES | cPanel |
|---------|-------------|---------|----------|------------|--------|
| **Setup Time** | 3-5 days | 30 mins | 30 mins | 1 hour | 1 hour |
| **Monthly Cost** | $0 | $35 | $20 | $5 | Included |
| **Deliverability** | 60-80% | 99%+ | 99%+ | 98%+ | 70-85% |
| **Maintenance** | High | None | None | Low | Low |
| **Scalability** | Limited | Excellent | Excellent | Excellent | Poor |
| **Analytics** | Basic | Advanced | Advanced | Good | Basic |
| **Support** | DIY | 24/7 | 24/7 | Community | Host-dependent |
| **Reputation** | Must build | Established | Established | Shared | Shared |
| **GDPR Compliant** | Your responsibility | ✅ Yes | ✅ Yes | ✅ Yes | Depends |
| **IP Warmup** | Required | Not needed | Not needed | Not needed | Not needed |
| **Recommended** | ❌ | ✅ | ✅ | ⚠️ | ⚠️ |

---

## 🎯 Final Recommendation

### **Go with Mailgun (Option 2.1)**

**Implementation Timeline:**
- **Today**: Sign up, verify domain (30 mins)
- **Tomorrow**: DNS propagation complete, verify in Mailgun
- **Day 3**: Integrate with Laravel, test campaigns
- **Day 4**: Production ready with monitoring

**Why Not Self-Hosted?**
1. You're building a SaaS, not an email infrastructure company
2. Email deliverability is complex and time-consuming
3. One misconfiguration = all emails go to spam
4. Your time is better spent on Bizmark.id features
5. $35/month is cheaper than DevOps time

**Next Steps:**
1. ✅ Sign up for Mailgun: https://signup.mailgun.com/
2. ✅ Add domain `mg.bizmark.id`
3. ✅ Add DNS records
4. ✅ Run installation commands above
5. ✅ Test with your email
6. ✅ Send first campaign!

---

## 🛡️ Security Best Practices

Regardless of chosen solution:

1. **Rate Limiting**
```php
// Add to app/Http/Kernel.php
'throttle:emails' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
```

2. **Queue Email Sending**
```bash
# Update .env
QUEUE_CONNECTION=database

# Run queue worker
php artisan queue:work --queue=emails
```

3. **Monitor Bounce Rates**
- Keep bounce rate < 5%
- Remove hard bounces immediately
- Monitor complaint rates

4. **Email Validation**
```bash
composer require egulias/email-validator
```

5. **Unsubscribe Compliance**
- Already implemented ✅
- One-click unsubscribe
- Honor unsubscribe immediately

---

## 📞 Support Resources

**Mailgun:**
- Docs: https://documentation.mailgun.com/
- Support: https://help.mailgun.com/
- Laravel Integration: https://laravel.com/docs/mail#mailgun-driver

**Hostinger (for DNS):**
- Login: https://hpanel.hostinger.com/
- DNS Management: Domain → Manage → DNS Records

**Testing Tools:**
- Mail Tester: https://www.mail-tester.com/
- MXToolbox: https://mxtoolbox.com/
- DNS Checker: https://dnschecker.org/

---

## ✅ Checklist

### Pre-Implementation
- [ ] Review all 3 options
- [ ] Get budget approval ($35/month for Mailgun)
- [ ] Choose email addresses needed
- [ ] Backup current .env file

### Mailgun Setup
- [ ] Create Mailgun account
- [ ] Verify billing information
- [ ] Add domain mg.bizmark.id
- [ ] Copy DNS records
- [ ] Add DNS records to Cloudflare/registrar
- [ ] Wait for DNS verification (green checkmarks)
- [ ] Get API key from Mailgun dashboard

### Laravel Integration
- [ ] Install Composer packages
- [ ] Update config/services.php
- [ ] Update .env file
- [ ] Clear all caches
- [ ] Test email via Tinker
- [ ] Test via Email Settings UI
- [ ] Send test campaign

### Production
- [ ] Configure queue worker
- [ ] Setup monitoring (Mailgun dashboard)
- [ ] Document for team
- [ ] Train staff on campaign sending
- [ ] Monitor first week deliverability

---

**Last Updated:** November 13, 2025  
**Status:** Ready for Implementation  
**Recommended:** Mailgun with Custom Domain
